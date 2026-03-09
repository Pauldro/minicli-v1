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

    /**
     * Convert string to be all hyphenated-lowercase (aka kabab-case, hyphen-case, dash-case, etc.)
     * 
     * EXAMPLE: FooBar becomes foo-bar
     * 
     * @param string $value
     * @param array  $options .
     *  - `hyphen` (string): Character to use as the hyphen (default='-')
     *  - `allow` (string): Characters to allow or range of characters to allow, for placement in regex (default='a-z0-9').
     *  - `allowUnderscore` (bool): Allow underscores? (default=false)
     * @return string
     */
    public static function hyphenCase(string $value, array $options = []) {
        
        $defaults = [
            'hyphen' => '-', 
            'allowed'  => 'a-z0-9', 
        ];

        $options = array_merge($defaults, $options);
        $hyphen = $options['hyphen'];
    
        if (strlen($value) == 0) {
             return '';
        }
        
        // check if value is already in the right format, and return it if so
        if (strtolower($value) === $value) {
            if ($options['allowed'] === $defaults['allowed'] && ctype_alnum(str_replace($hyphen, '', $value))) {
                return $value;
            }
            if ($options['allowed'] !== $defaults['allowed'] && preg_match('/^[' . $hyphen . $options['allowed'] . ']+$/', $value)) {
                return $value;
            }
        }
        
        // handle apostrophes
        $value = str_replace(array("'", "’"), '', $value);
        // handle whitespace
        $value = str_replace(array(" ", "\r", "\n", "\t"), $hyphen, $value);	
        // convert everything not allowed to hyphens
        $value = preg_replace('/[^' . $options['allowed'] . ']+/i', $hyphen, $value);
        // convert camel case to hyphenated
        $value = preg_replace('/([[:lower:]])([[:upper:]])/', '$1' . $hyphen . '$2', $value);
        // handle doubled hyphens
        $value = preg_replace('/' . $hyphen . $hyphen . '+/', $hyphen, $value);
        return strtolower(trim($value, $hyphen)); 
    }
}