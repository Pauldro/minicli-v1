<?php namespace Pauldro\Minicli\Services;
// Minicli
use Minicli\App as MinicliApp;
use Minicli\ServiceInterface;
// Pauldro Minicli
use Pauldro\Minicli\App\App;
use Pauldro\Minicli\Cmd\CommandCall;

/**
 * Updates Log Files
 */
class Logger implements ServiceInterface {
	protected $dir = '';

	/**
	 * @param  App $app
	 * @return void
	 */
	public function load(MinicliApp $app) : void
	{
		$this->dir = rtrim($app->config->log_dir, '/') . '/';
	}

/* =============================================================
	Logging
============================================================= */
	/**
	 * Return filepath to log file
	 * @param  string $filename
	 * @return string
	 */
	public function filepath($filename) : string
	{
		return $this->dir . rtrim($filename, '.log') . '.log';
	}

	/**
	 * Return if file exists
	 * @param  string $filename
	 * @return bool
	 */
	public function exists($filename) : bool
	{
		return file_exists($this->filepath($filename));
	}

	/**
	 * Record Log Message
	 * @param  string $filename
	 * @param  string $text
	 * @return bool
	 */
	public function log($filename, $text) : bool
	{
		$file = $this->filepath($filename);
		$content = '';

		if (file_exists($file)) {
			$content = file_get_contents($file);
		}
		$line = self::createLogString([date('Ymd'), date('His'), $text]). PHP_EOL;
		return boolval(file_put_contents($file, $content . $line));
	}

	/**
	 * Record Log Message
	 * @param  string $filename
	 * @param  string $text
	 * @return bool
	 */
	public function logWithoutTimestamp($filename, $text) : bool
	{
		$file = $this->filepath($filename);
		$content = '';

		if (file_exists($file)) {
			$content = file_get_contents($file);
		}
		$line = self::createLogString([$text]). PHP_EOL;
		return boolval(file_put_contents($file, $content . $line));
	}

	/**
	 * Clear Log File
	 * @param  string $filename
	 * @return bool
	 */
	public function clear($filename) : bool
	{
		$file = $this->filepath($filename);
		
		if (file_exists($file) === false) {
			return true;
		}
		return boolval(file_put_contents($file, ''));
	}

	/**
	 * Archive Log file for previous month
	 * @param  string $filename
	 * @return bool
	 */
	public function archiveLogPrevMonth($filename) : bool 
	{
		$date = date('Ym', strtotime("-1 month"));

		$file = $this->filepath($filename);

		$archiveFilepath = $this->filepath("$filename-$date");

		if (file_exists($file) === false) {
			file_put_contents($archiveFilepath, '');
            return false;
		}
		copy($file, $archiveFilepath);
		if (file_exists($archiveFilepath) === false) {
			echo $archiveFilepath , "does not exist " . PHP_EOL;
			return false;
		}
		file_put_contents($file, '');
		return true;
	}

	public function getLastLine($filename) {
		$file = $this->filepath($filename);

		if (file_exists($file) === false) {
			return false;
		}
		return trim(shell_exec("tail -n 1 $file"));
	}

/* =============================================================
	Log Strings
============================================================= */
	/**
	 * Return array formatted as string for Log delimited by \t
	 * @param  array $parts
	 * @return string
	 */
	public static function createLogString($parts = []) : string
	{
		return implode("\t", $parts);
	}

	/**
	 * Sanitize Command for Log Use
	 * @return string
	 */
	public static function sanitizeCmdForLog(CommandCall $input, array $sensitiveParams) : string
    {
		$cmd = implode(' ', $input->getRawArgs());

		foreach ($sensitiveParams as $param) {
			$find = "$param=" . $input->getParam($param);
			$cmd  = str_replace($find, "$param=***", $cmd);
		}
		return $cmd;
	}
}