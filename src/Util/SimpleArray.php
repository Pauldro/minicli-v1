<?php namespace Pauldro\Minicli\Util;
use ArrayObject;

/**
 * SimpleArray
 *
 * Container for array lists
 *
 * @property array $data Array where values are stored
 */
class SimpleArray implements \IteratorAggregate, \ArrayAccess, \Countable {
	protected $data = [];

/* =============================================================
	Getters
============================================================= */
	/**
	 * Return the item at the given index, or null if not set.
	 * @param  int|string $key  Provide any of the following:
	 * @return mixed
	 */
	public function get($key) : mixed
	{
		if ($this->has($key) === false) {
			return null;
		}
		return $this->data[$key];
	}

	/**
	 * Return if Item Exists by Key
	 * @param  int|string $key Provide any of the following:
	 * @return bool
	 */
	public function has($key) : bool 
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * Return a PHP array of all the items in this SimpleArray with original keys maintained
	 * @return array Copy of the array that SimpleArray uses internally.
	 */
	public function getArray() : array
	{
		return $this->data;
	}

	/**
	 * Return a regular PHP array of all keys used in this SimpleArray.
	 * @return array Keys used in the SimpleArray.
	 */
	public function getKeys() : array
	{
		return array_keys($this->data);
	}

	/**
	 * Return a regular PHP array of all values used in this SimpleArray.
	 * NOTE: this does not attempt to maintain original
	 * keys of the items. The returned array is reindexed from 0.
	 * @return array Values used in the SimpleArray.
	 */
	public function getValues() : array
	{
		return array_values($this->data);
	}

	/**
	 * Return the first item in the SimpleArray or boolean false if empty.
	 * Note that this resets the internal SimpleArray pointer, which would affect other active iterations.
	 * @return mixed
	 */
	public function first() : mixed 
	{
		return reset($this->data);
	}

	/**
	 * Returns the last item in the SimpleArray or boolean false if empty.
	 * Note that this resets the internal SimpleArray pointer, which would affect other active iterations.
	 */
	public function last() : mixed
	{
		return end($this->data);
	}

	/**
	 * Return subset of the List
	 *
	 * Given a starting point and a number of items, returns a new SimpleArray of those items.
	 * If `$limit` is omitted, then it includes everything beyond the starting point.
	 *
	 * ~~~~~
	 * // Get first 3 items
	 * $myItems = $items->slice(0, 3);
	 * ~~~~~
	 *
	 * @param  int $start  Starting index.
	 * @param  int $limit  Number of items to include. If omitted, includes the rest of the array.
	 * @return SimpleArray
	 */
	public function subset($start, $limit = 0) : SimpleArray
	{
		if ($limit) {
			$slice = array_slice($this->data, $start, $limit);
		} else {
			$slice = array_slice($this->data, $start);
		}
		$items = new static();
		$items->setArray($slice);
		return $items;
	}

/* =============================================================
	Setters
============================================================= */
	/**
	 * Set an item by key in the SimpleArray
	 * @param  int|string $key   Key of item to set.
	 * @param  mixed      $value Item value to set.
	 * @return $this
	 */
	public function set($key, $value) : static 
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Add an item to the end of the SimpleArray.
	 * ~~~~~
	 * $list->add($item);
	 * ~~~~~
	 * @param  mixed $item Item to add.
	 * @return $this
	 */
	public function add($item) : static
	{
		$this->data[] = $item;
		return $this;
	}

	/**
	 * Set Entire SimpleArray.
	 * ~~~~~
	 * $list->set($items);
	 * ~~~~~
	 * @param  array Set Array
	 * @return $this
	 */
	public function setArray($items = []) : static
	{
		$this->data = $items;
		return $this;
	}

/* =============================================================
	Removal
============================================================= */
	/**
	 * Remove the given item or index from the SimpleArray (if it exists).
	 * @param int|string $key Item to remove (object), or index of that item.
	 * @return $this
	 */
	public function remove($key) : static
	{
		if(array_key_exists($key, $this->data)) {
			unset($this->data[$key]);
		}
		return $this;
	}

/* =============================================================
	Interface Functions
============================================================= */
	/**
	 * Allows iteration of the SimpleArray.
	 * - Fulfills PHP's IteratorAggregate interface so that you can traverse the SimpleArray.
	 * - No need to call this method directly, just use PHP's `foreach()` method on the SimpleArray.
	 *
	 * ~~~~~
	 * // Traversing a SimpleArray with foreach:
	 * foreach($items as $item) {
	 *   // ...
	 * }
	 * ~~~~~
	 * @return ArrayObject
	 */
	public function getIterator() : ArrayObject
	{
		return new ArrayObject($this->data);
	}

	/**
	 * Returns the number of items in this SimpleArray.
	 * Fulfills PHP's Countable interface, meaning it also enables this SimpleArray to be used with PHP's `count()` function.
	 * ~~~~~
	 * // These two are the same
	 * $qty = $items->count();
	 * $qty = count($items);
	 * ~~~~~
	 * @return int
	 */
	public function count() : int
	{
		return count($this->data);
	}

	/**
	 * Sets an index in the SimpleArray.
	 * For the \ArrayAccess interface.
	 * @param int|string $key Key of item to set.
	 * @param mixed $value Value of item.
	 */
	public function offsetSet($key, $value) : void
	{
        $this->set($key, $value);
    }

	/**
	 * Returns the value of the item at the given index, or false if not set.
	 * @param int|string $key Key of item to retrieve.
	 * @return mixed
	 */
	public function offsetGet($key) : mixed
	{
		if($this->offsetExists($key)) {
			return $this->data[$key];
		}
		return false;
	}


	/**
	 * Unsets the value at the given index.
	 * For the \ArrayAccess interface.
	 * @param int|string $key Key of the item to unset.
	 * @return bool True if item existed and was unset. False if item didn't exist.
	 */
	public function offsetUnset($key) : void
	{
		if($this->offsetExists($key)) {
			$this->remove($key);
			return;
		}
		return;
	}

	/**
	 * Determines if the given index exists in this SimpleArray.
	 * For the \ArrayAccess interface
	 * @param int|string $key Key of the item to check for existance.
	 * @return bool True if the item exists, false if not.
	 */
	public function offsetExists($key) : bool 
	{
		return array_key_exists($key, $this->data);
	}
}
