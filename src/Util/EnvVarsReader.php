<?php namespace Pauldro\Minicli\Util;

/**
 * Utility for reading values from $_ENV
 */
class EnvVarsReader {
    /** @return bool */
    public static function exists(string $key) {
        return array_key_exists($key, $_ENV);
    }

    /** @return string */
    public static function get(string $key, $default = '') {
        if (self::exists($key) === false) {
            return $default;
        }
        return $_ENV[$key];
    }

    /** @return bool */
    public static function getBool(string $key) {
        $value = self::get($key, 'false');
        return $value == 'true';
    }

    /** @return array */
    public static function getArray(string $key, $delimiter = ',') {
        return explode($delimiter, self::get($key));
    }
}