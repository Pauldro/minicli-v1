<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandCall as MinicliCommandCall;

/**
 * Call
 * Parses Command Arguments ($argv) into segments
 * 1. Command
 * 2. Subcommand ** Optional **
 * 3. Parameters
 * 4. Flags (both --[] and -[])
 */
class CommandCall extends MinicliCommandCall {
	/**
	 * Return Last Argument sent
	 * @return string
	 */
	public function lastArg() {
		$i = (sizeof($this->args) - 1);
		return array_key_exists($i, $this->args) ? $this->args[$i] : '';
	}

	/**
	 * Return boolean value for parameter
	 * @param  string $param Parameter to get Value from
	 * @return bool
	 */
	public function getParamBool($param) {
		$value = $this->getParam($param);
		if (empty($value)) {
			return false;
		}
		return strtolower($value) == 'y' || strtolower($value) == 'true';
	}

    /**
	 * Return Parameter Value as array
	 * @param  string $param      Parameter Key
	 * @param  string $delimiter  Delimiter
	 * @return array
	 */
	public function getParamArray($param, $delimiter = ",") {
		return explode($delimiter, $this->getParam($param));
	}

	/**
	 * Return Param value as integer
	 * @param  string $param
	 * @return int
	 */
	public function getParamInt($param) {
		return intval($this->getParam($param));
	}

/* =============================================================
	Internal
============================================================= */
	/**
	 * Parse Command Input
	 * @param  array $argv Input
	 * @return void
	 */
	protected function parseCommand($argv) {
		foreach ($argv as $arg) {
            $pair = explode('=', $arg, 2);

            if (count($pair) == 2) {
                $this->params[$pair[0]] = $pair[1];
                continue;
            }

            if (substr($arg, 0, 2) == '--') {
                $this->flags[] = $arg;
                continue;
            }
			
			if (substr($arg, 0, 1) == '-') {
				$this->flags[] = $arg;
				continue;
			}

            $this->args[] = $arg;
        }
	}
}
