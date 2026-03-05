<?php namespace Pauldro\Minicli\Util;


class StringUtilities {
	/**
	 * Add Padding to string
	 * NOTE: padding is added to the right of string
	 * @param  string  $value
	 * @param  int     $length
	 * @param  string  $padding
	 * @return string
	 */
	public static function pad(string $value, int $length, string $padding = ' ') : string
    {
		return str_pad($value, $length, $padding);
	}

	/**
	 * Return longest string length from list of strings
	 * @param  array $strings
	 * @return int
	 */
	public static function longestStrlen(array $strings) : int
	{
		$length = 0;
		foreach ($strings as $string) {
			if (strlen($string) > $length) {
				$length = strlen($string);
			}
		}
		return $length;
	}

	/**
	 * Convert string to be all camelCase
	 * 
	 * @param string $value
	 * @param array  $opts  options:
	 *  - `allowed` (string): Characters to allow or range of characters to allow, for placement in regex (default='a-zA-Z0-9').
	 *  - `startLowercase` (bool): Always start return value with lowercase character? (default=true)
	 *  - `startNumber` (bool): Allow return value to begin with a number? (default=false)
	 * @return string
	 */
	public static function camelCase(string $value, array $opts = []) : string
	{
		
		$defaults = [
			'allowed'        => 'a-zA-Z0-9',
			'startLowercase' => true, 
			'startNumber'    => false, 
		];
		$opts = array_merge($defaults, $opts);
		$allow = $opts['allowed'];
		$needsWork = true;

		if ($allow === $defaults['allowed'] && ctype_alnum($value)) {
			$needsWork = false;
		}
		
		if ($allow != $defaults['allowed'] && preg_match('/^[' . $allow . ']+$/', $value)) {
			 $needsWork = false;
		}
	
		if ($needsWork) {
			$value = preg_replace('/([^' . $allow . ' ]+)([' . $allow . ']+)/', '$1 $2', $value);
			$value = preg_replace('/[^' . $allow . ' ]+/', '', $value);

			$parts = explode(' ', $value);
			$value = '';

			foreach ($parts as $n => $part) {
				if (empty($part)) {
					 continue;
				}
				$value .= $n ? ucfirst($part) : $part;
			}
		}
		
		if ($opts['startLowercase'] && isset($value[0])) {
			$value[0] = strtolower($value[0]);
		}
		
		if ($opts['startNumber'] === false) {
			$value = ltrim($value, '0123456789'); 
		}
		return $value;
	}
}