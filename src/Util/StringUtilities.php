<?php namespace Pauldro\Minicli\Util;


class StringUtilities {
	public static function pad(string $cmd, int $length, $padding = ' ') : string
    {
		return str_pad($cmd, $length, $padding);
	}

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
}