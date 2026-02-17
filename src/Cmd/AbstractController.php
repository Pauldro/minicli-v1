<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\App as MinicliApp;
use Minicli\Command\CommandController;
// Pauldro Minicli
use Pauldro\Minicli\App\App;
use Pauldro\Minicli\Output\Printer;
use Pauldro\Minicli\Services\Logger;
use Pauldro\Minicli\Util\EnvVarsReader as EnvVars;
use Pauldro\Minicli\Util\StringUtilities as Strings;

/**
 * Class for Handling Executing Commands
 * 
 * @property App         $app
 * @property CommandCall $input
 * @property Logger      $log
 * @property Printer     $printer
 */
abstract class AbstractController extends CommandController {
	const DESCRIPTION = '';
	const OPTIONS = [];
	const NOTES = [];
	const OPTIONS_DEFINITIONS = [];
	const OPTIONS_DEFINITIONS_OVERRIDE = [];
	const REQUIRED_PARAMS = [];
	const SENSITIVE_PARAM_VALUES = [];
	const REQUIRED_ENV_VARS = [];

	/**
     * Called before `run`.
     * @param App $app
     */
    public function boot(MinicliApp $app) : void
    {
        parent::boot($app);
		$this->printer = $app->printer;
		$this->log     = $app->log;
    }

	/**
	 * Setup controller
	 * @param  App $app
	 * @param  CommandCall $input
	 * @return void
	 */
	public function bootstrap(MinicliApp $app, CommandCall $input) : void
	{
		$this->app     = $app;
        $this->config  = $app->config;
        $this->log     = $app->log;
		$this->printer = $app->printer;
		$this->input   = $input;
	}

/* =============================================================
	Inits
============================================================= */
	/**
	 * Initialize App
	 * @return bool
	 */
	protected function init() : bool
	{
		$this->initEnvTimeZone();
		
		if ($this->initRequiredEnvVars() === false) {
			return false;
		}
		if ($this->initRequiredParams() === false) {
			return false;
		}
		return true;
	}

	/**
	 * Initialize the Local Time Zone
	 * NOTE: used for logging
	 * @return bool
	 */
	protected function initEnvTimeZone() : bool
	{
		$sysTZ = exec('date +%Z');
		$abbr = timezone_name_from_abbr($sysTZ);
		return date_default_timezone_set($abbr);
	}

	protected function initRequiredEnvVars() : bool 
	{
		foreach (static::REQUIRED_ENV_VARS as $var) {
			if (EnvVars::exists($var) === false) {
				$description = static::REQUIRED_ENV_VARS[$var];
				return $this->error("Missing .env variable: $var - $description");
			}
		}
		return true;
	}

	protected function initRequiredParams() : bool
	{
		foreach (static::REQUIRED_PARAMS as $param) {
			if ($this->hasParam($param) === false) {
				$description = array_key_exists($param, static::OPTIONS_DEFINITIONS) ? static::OPTIONS_DEFINITIONS[$param] : $param;
				$use         = array_key_exists($param, static::OPTIONS) ? static::OPTIONS[$param] : '';
				return $this->error("Missing Parameter: $description ($use)");
			}
		}
		return true;
	}

/* =============================================================
	Parameter Functions
============================================================= */
	/**
	 * Return boolean value for parameter
	 * @param  string $param Parameter to get Value from
	 * @return bool
	 */
	protected function getParamBool($param) : bool
	{
		return $this->input->getParamBool($param);
	}

	/**
	 * Return Parameter Value
	 * @param  string $param
	 * @return string|null
	 */
	protected function getParam($param)
	{
		return $this->input->getParam($param);
	}

	/**
	 * Return Parameter Value as array
	 * @param  string $param      Parameter Key
	 * @param  string $delimeter  Delimiter
	 * @return array
	 */
	protected function getParamArray($param, $delimeter = ",") : array
	{
		return $this->input->getParamArray($param, $delimeter);
	}
	
/* =============================================================
	Logging
============================================================= */
	/**
	 * Setup Logs Directory
	 * @return bool
	 */
	protected function setupLogDir() : bool
	{
		if (is_dir($this->app->config->log_dir)) {
			return true;
		}
		return mkdir($this->app->config->log_dir);
	}
	
	/**
	 * Log Command sent to App
	 * @return void
	 */
	protected function logCommand() : void
	{
		if (EnvVars::exists('LOG.COMMANDS') === false || EnvVars::getBool('LOG.COMMANDS') === false) {
			return;
		}

		if ($this->setupLogDir() === false) {
			return;
		}

		$cmd  = Logger::sanitizeCmdForLog($this->input, static::SENSITIVE_PARAM_VALUES);
		$this->log->log('commands', $cmd);
	}

	/**
	 * Log Command sent to App
	 * @return void
	 */
	protected function logError($msg) : void
	{
		if ($this->setupLogDir() === false) {
			return;
		}
		$cmd = Logger::sanitizeCmdForLog($this->input, static::SENSITIVE_PARAM_VALUES);
		$this->log->log('errors', Logger::createLogString([$cmd, $msg]));
	}

	/**
	 * Log Error Message
	 * @param  string $msg
	 * @return false
	 */
	protected function error($msg) : bool
	{
		$this->printer->error($msg);
		$this->logError($msg);
		return false;
	}

	/**
	 * Display Success Message
	 * @param  string $msg
	 * @return true
	 */
	protected function success($msg) : bool
	{
		if ($this->hasFlag('--debug')) {
			$this->printer->success("Success: $msg");
			return true;
		}
		$this->printer->success($msg);
		return true;
	}

/* =============================================================
	Displays
============================================================= */
	/**
	 * Display Key Value Data
	 * @param  array $data
	 * @return void
	 */
	protected function displayDictionary(array $data) : void
	{
		$printer = $this->printer;
		$titleLength = Strings::longestStrlen(array_keys($data));
		
		foreach ($data as $title => $value) {
            $lineData = [
                $printer->spaces(4),
                $printer->filterOutput(Strings::pad($title, $titleLength + 2), 'success'),
                $value
            ];
            $printer->line(implode('', $lineData));
		}
	}
}
