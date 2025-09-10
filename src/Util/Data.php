<?php namespace Pauldro\Minicli\Util;
//
use ArrayObject;

/**
 * Data
 *
 * Container for Data
 *
 * @property array $data Array where get/set properties are stored
 */
class Data implements \IteratorAggregate, \ArrayAccess {
	protected $data = [];

/* =============================================================
	Getters
============================================================= */
	/**
	 * Returns the full array of properties set to this object
	 *
	 * If descending classes also store data in other containers, they may want to
	 * override this method to include that data as well.
	 * @return array Returned array is associative and indexed by property name.
	 */
	public function getArray() : array
	{
		return $this->data;
	}

	/**
	 * Return this data as an array
	 * @return array
	 */
	public function getJsonArray() : array
	{
		return $this->data;
	}

	/**
	 * Retrieve the value for a previously set property, or retrieve an API variable
	 * ~~~~~
	 * // Retrieve the value of a property
	 * $value = $item->get("some_property");
	 * // Retrieve a value using array access
	 * $value = $item["some_property"];
	 * ~~~~~
	 *
	 * @param  string|object $key  Name of property you want to retrieve.
	 * @return mixed|null          Returns value of requested property, or null if the property was not found.
	 *
	 */
	public function get($key)
	{
		$method = 'get' . ucfirst($key);

		if (method_exists($this, $method)) {
			return $this->$method();
		}

		if (property_exists($this, $key)) {
			return $this->$key;
		}

		if (array_key_exists($key, $this->data)) {
			return $this->data[$key];
		}
		return null;
	}

	/**
	 * Provides direct reference access to variables in the $data array
	 * Otherwise the same as get()
	 *
	 * @param  string $key
	 * @return mixed|null
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Enables use of $var('key')
	 * @param string $key
	 * @return mixed
	 */
	public function __invoke($key) : mixed
	{
		return $this->get($key);
	}

	/**
	 * Determines if the given index exists in this Data.
	 * @param  int|string $key  Key of the item to check for existence.
	 * @return bool             True if the item exists, false if not.
	 */
	public function has($key) : bool
	{
		return $this->__isset($key);
	}

/* =============================================================
	Setters
============================================================= */
	/**
	 * Set a value to this object’s data
	 *
	 * ~~~~~
	 * // Set a value for a property
	 * $item->set('foo', 'bar');
	 *
	 * // Set a property value directly
	 * $item->foo = 'bar';
	 *
	 * // Set a property using array access
	 * $item['foo'] = 'bar';
	 * ~~~~~
	 *
	 * @param  string $key Name of property you want to set
	 * @param  mixed $value Value of property
	 * @return void
	 */
	public function set($key, $value) : void
	{
		if ($key === 'data') {
			if (is_array($value) === false) {
				$value = (array) $value;
			}
			$this->setArray($value);
			return;
		}

		if (property_exists($this, $key)) {
			$this->$key = $value;
			return;
		}

		$this->data[$key] = $value;
	}

	/**
	 * Set an array of key=value pairs
	 * @param  array $data Associative array of where the keys are property names, and values are… values.
	 * @return $this
	 */
	public function setArray(array $data) : void 
	{
		foreach ($data as $key => $value) {
			$this->__set($key, $value);
		}
	}

	/**
	 * Provides direct reference access to set values in the $data array
	 * @param  string $key
	 * @param  mixed $value
	 */
	public function __set($key, $value) : void
	{
		$method = "set".ucfirst($key);

		if (method_exists($this, $method)) {
			$this->$method($value);
			return;
		}
		$this->set($key, $value);
		return;
	}

	/**
	 * Ensures that isset() and empty() work for this classes properties.
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key) : bool 
	{
		return isset($this->data[$key]);
	}

/* =============================================================
	Removal
============================================================= */
	/**
	 * Ensures that unset() works for this classes data.
	 * @param string $key
	 */
	public function __unset($key) : void
	{
		$this->remove($key);
		return;
	}

	/**
	 * Remove a previously set property
	 * ~~~~~
	 * $item->remove('some_property');
	 * ~~~~~
	 * @param string $key Name of property you want to remove
	 */
	public function remove($key) : void
	{
		unset($this->data[$key]);
	}

/* =============================================================
	IteratorAggregate Interface Functions
============================================================= */
	/**
	 * Enables the object data properties to be iterable as an array
	 * @return ArrayObject
	 */
	public function getIterator() : ArrayObject
	{
		return new ArrayObject($this->data);
	}

/* =============================================================
	ArrayAccess Interface Functions
============================================================= */
	/**
	 * Sets an index in the Array.
	 * @param int|string              $key    Key of item to set.
	 * @param int|string|array|object $value  Value of item.
	 */
	public function offsetSet($key, $value) : void 
	{
		$this->set($key, $value);
	}

	/**
	 * @param  int|string               $key  Key of item to retrieve.
	 * @return int|string|array|object        Value of item requested, or false if it doesn't exist.
	 */
	public function offsetGet($key) : mixed
	{
		$value = $this->get($key);
		return is_null($value) ? false : $value;
	}

	/**
	 * Unsets the value at the given index.
	 * @param  int|string $key Key of the item to unset.
	 * @return bool            True if item existed and was unset. False if item didn't exist.
	 */
	public function offsetUnset($key) : void
	{
		if ($this->__isset($key)) {
			$this->remove($key);
			return;
		}
		return;
	}

	/**
	 * Determines if the given index exists in this Data.
	 * @param  int|string $key  Key of the item to check for existence.
	 * @return bool             True if the item exists, false if not.
	 */
	public function offsetExists($key) : bool
	{
		return $this->__isset($key);
	}
}
