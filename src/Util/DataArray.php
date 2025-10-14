<?php namespace Pauldro\Minicli\Util;

/**
 * DataArray
 *
 * Container for Data lists
 * 
 * 
 * @method Data|mixed get(string|int $key) Return the item at the given index, or null if not set.
 * @method Data|mixed first()   Return the first item in the DataArray or boolean false if empty.
 * @method Data|mixed last()    Return the last  item in the DataArray or boolean false if empty.
 * @method DataArray  subset($start, $limit = 0) Return subset of the DataArray
 * @method DataArray  set(string|int $key, Data $value) Set an item by key in the DataArray
 * @method DataArray  add(Data $item) Add an item to the end of the DataArray
 * 
 * @property array $data Array where values are stored
 */
class DataArray extends SimpleArray {
	protected $data = [];

/* =============================================================
	Getters
============================================================= */
	/**
	 * Return Array of Arrays
	 * NOTE: use if listing Data Classes
	 * @return array[array]
	 */
	public function getJsonArray() : array
	{
		$data = [];
		foreach ($this->data as $item) {
			$data[] = $item->getJsonArray();
		}
		return $data;
	}

	public function getArray() : array
	{
		$data = [];
		foreach ($this->data as $item) {
			$data[] = $item->getArray();
		}
		return $data;
	}

	/**
	 * Return new/blank item of the type that this DataArray holds
	 * @return Data
	 */
	public function newItem() : Data
	{
		return new Data();
	}

	/**
	 * Return new/blank item of the type that this DataArray holds
	 * @return Data
	 */
	public function makeBlankItem() : Data
	{
		return $this->newItem();
	}

	/**
	 * Return all the values for a fieldname
	 * @param  string $name
	 * @return array
	 */
	public function fieldValues(string $name) : array
	{
		$values = [];

		foreach ($this as $item) {
			if ($item->has($name) === false) {
				continue;
			}
			$values[] = $item->get($name);
		}
		return $values;
	}
}
