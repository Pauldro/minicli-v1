<?php namespace Pauldro\Minicli\App;
// Minicli Library
use Minicli\App as MinicliApp;
use Minicli\Config as MinicliConfig;
// Pauldro Minicli
use Pauldro\Minicli\Cmd;
use Pauldro\Minicli\Output\Printer;
use Pauldro\Minicli\Services\Logger;

/**
 * @property MinicliConfig       $config
 * @property Cmd\CommandRegistry $command_registry
 * @property Logger              $log
 * @property Printer             $printer
 */
class App extends MinicliApp {

	 public function __construct(array $config = null) {
		parent::__construct($this->parseConfig($config));

		$this->addServices();
	}

	/**
	 * Add Services (printer, command_registry)
	 * @return void
	 */
	protected function addServices() {
		$this->addService('printer', Printer::instance());

		$reg = new Cmd\CommandRegistry($this->config->app_dir);
		$reg->setAppNamespace($this->config->app_namespace);
		$this->addService('command_registry', $reg);
		$this->addService('log', new Logger());
	}

	public function parseConfig(array $config = null) {
		return array_merge([
			'app_path' => __DIR__ . '/../app/Cmd',
		], $config);
	}

	public function runCommand(array $argv = []) {
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
