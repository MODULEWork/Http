<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 

/**
 * {@inheritdoc}
 */
class ArrayCase implements ArrayCaseInterface, \Countable, \IteratorAggregate
{
	/**
	 * The array which gets "wrap" by this class
	 * @var array
	 */
	protected $array;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(array $array = array())
	{
		$this->array = $array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function all()
	{
		return $this->array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->array) ? $this->array[$key] : $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($key, $value, $override = false)
	{
		if ($this->has($key) && !$override) {
			return false;
		}

		$this->array[$key] = $value;
		return true;
		
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($key)
	{
		return array_key_exists($key, $this->array);
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove($key)
	{
		unset($this->array[$key]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function merge(array $array = array())
	{
		$this->array = array_merge($this->array, $array);
	}

	/**
	 * {@inheritdoc}
	 */
	public function mock(array $array = array())
	{
		$this->array = $array;
	}

	/**
	 * {@inheritdoc}
	 */
	public function keys()
	{
		return array_keys($this->array);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->array);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count()
	{
		return count($this->array);
	}

}