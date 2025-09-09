<?php namespace Pauldro\Minicli\Util;

/**
 * SessionVars
 * Utility for interacting with the $_SESSION vars
 */
class SessionVars {
    /**
     * Set a Session Variable
     * @param string       $key Name of session variable to set
	 * @param string|mixed $value Value to set (or name of variable, if first argument is namespace)
	 * @param mixed       $_value Value to set if first argument is namespace. Omit otherwise.
     */
    public static function set($key, $value, $_value = null) : void
    {
        if (is_null($_value) === false) {
            self::setFor($key, $value, $_value);
            return;
        }
        $_SESSION[$key] = $value;
    }

    /**
     * Get a Session Variable
     * @param  string $key  Name of session variable to retrieve
	 * @param  string $_key Name of session variable to get if first argument is namespace, omit otherwise.
	 * @return mixed        Returns value of seession variable, or NULL if not found.
     */
    public static function get($key, $_key = null) : mixed
    {
        if ($_key !== null) { // namespaced
			return self::getFor($key, $_key);
		}
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Delete a Session Variable
     * @param  string $key  Name of session variable to retrieve
	 * @param  string $_key Name of session variable to get if first argument is namespace, omit otherwise.
	 * @return mixed        Returns value of seession variable, or NULL if not found.
     */
    public static function delete($key, $_key = null) : void
    {
        if (is_null($_key)) {
            unset($_SESSION[$key]);
            return;
        }
        if (is_bool($_key)) {
            unset($_SESSION[$key]);
            return;
        }
        unset($_SESSION[$key][$_key]);
    }

    /**
     * Set a session variable within a given namespace
     * @param string $ns     Namespace string.
	 * @param string $key    Name of session variable you want to set.
	 * @param mixed  $value  Value you want to set, or specify null to unset.
     */
    public static function setFor($ns, $key, $value) : void
    {
		$data = self::get($ns);

		if (is_array($data) === false) {
            $data = [];
        }
		if (is_null($value)) {
			unset($data[$key]);
		} else {
			$data[$key] = $value;
		}
		self::set($ns, $data);
        return;
	}

    /**
	 * Get a session variable within a given namespace
	 * @param string $ns   Namespace string
	 * @param string $key  Specify variable name to retrieve, or blank string to return all variables in the namespace.
	 * @return mixed
	 */
	public static function getFor($ns, $key) : mixed
    {
		$data = self::get($ns);

		if (is_array($data) === false) {
            $data = [];
        }
		if ($key === '') {
            return $data;
        }
		return isset($data[$key]) ? $data[$key] : null;
	}

    /**
	 * Get all session variables for given namespace and return associative array
	 * @param  string
	 * @return array
	 */
	public static function getAllFor($ns) : mixed
    {
		return self::getFor($ns, '');
	}

    /**
	 * Delete a session variable within a namespace
	 * @param string $ns Namespace
	 * @param string $key Provide name of variable to remove, or boolean true to remove all in namespace.
	 */
	public function deleteFor($ns, $key) : void
    {
		self::delete($ns, $key);
        return;
	}

    /**
	 * Delete all session variables in given namespace
	 * @param  string $ns
	 */
	public function deleteAllFor($ns): void
    {
		self::delete($ns, true);
	}
}
