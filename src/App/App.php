<?php namespace Pauldro\Minicli\App;
// Minicli Library
use Minicli\App as MinicliApp;
use Minicli\Config as MinicliConfig;
// Pauldro Minicli
use Pauldro\Minicli\Cmd;
use Pauldro\Minicli\Output\Printer;
use Pauldro\Minicli\Services\Env;
use Pauldro\Minicli\Services\Logger;

/**
 * @property MinicliConfig       $config
 * @property Cmd\CommandRegistry $command_registry
 * @property Env                 $dotenv
 * @property Logger              $log
 * @property Printer             $printer
 */
class App extends MinicliApp {

	 public function __construct(array $config = null) {
		parent::__construct($this->parseConfig($config));

		$this->addServices();
		$this->parseSetInis();
	}

/* =============================================================
    Inits, Boots, Loads
============================================================= */
	/**
	 * Add Services (printer, command_registry)
	 * @return void
	 */
	protected function addServices() : void
	{
		$this->addService('printer', Printer::instance());

		$reg = new Cmd\CommandRegistry($this->config->cmd_dir);
		$reg->setCmdNamespace($this->config->app_namespace);
		$this->addService('command_registry', $reg);
		$this->addService('log', new Logger());
		$this->addService('dotenv', new Env());
	}

	/**
     * Parse, Set Inis
     * @return void
     */
    private function parseSetInis() : void
    {
        if ($this->config->has('php_ini') === false) {
            return;
        }
		/** @var array */
        $conf = $this->config->php_ini;
        $dir = rtrim($conf['dir'], '/') . '/';
        $files = array_key_exists('files', $conf) ? $conf['files'] : [];

        foreach ($files as $file) {
            $settings = parse_ini_file($dir.$file);

            foreach ($settings as $option => $value) {
                ini_set($option, $value);
            }
        }
    }

/* =============================================================
    Public
============================================================= */
	public function parseConfig(array $config = null) : array
	{
		return array_merge([
			'app_path' => __DIR__ . '/../app/Cmd',
		], $config);
	}

	public function runCommand(array $argv = []) : void
	{
		$input = new Cmd\CommandCall($argv);

		if (count($input->args) < 2) {
			$this->printSignature();
			exit;
		}

		$controller = $this->command_registry->getCallableController($input->command, $input->subcommand);

		try {
			$controller = $this->command_registry->getCallableController($input->command, $input->subcommand);
		} catch (\ReflectionException $e) {
			$controller = null;
		}

		if (empty($controller)) {
			$cmd = $input->command;

			if (strtolower($input->subcommand) == 'default') {
				$cmd .= " $input->subcommand";
			}
			Printer::instance()->error("Controller not found for $cmd");
			exit;
		}

		if ($controller instanceof Cmd\AbstractController) {
			$controller->boot($this);
			$controller->run($input);
			$controller->teardown();
			exit;
		}
		$this->runSingle($input);
	}
}
