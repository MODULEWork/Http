<?php 
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 
namespace Modulework\Modules\Http;

/**
*
*/
class ArrayCase implements ArrayCaseInterface, \Countable, \IteratorAggregate
{
	protected $array;

	public function __construct(array $array = array())
	{
		$this->array = $array;
	}

	public function all()
	{
		return $this->array;
	}

	public function get($key, $default = null)
	{
		return array_key_exists($key, $this->array) ? $this->array[$key] : $default;
	}

	public function set($key, $value, $override = false)
	{
		if ($this->has($key) && !$override) {
			return false;
		}

		$this->array[$key] = $value;
		return true;
		
	}

	public function has($key)
	{
		return array_key_exists($key, $this->array);
	}

	public function remove($key)
	{
		unset($this->parameters[$key]);
	}

	public function merge(array $array = array())
	{
		$this->array = array_merge($this->array, $array);
	}

	public function mock(array $array = array())
	{
		$this->array = $array;
	}

	public function keys()
	{
		return array_keys($this->array);
	}

	public function getIterator()
	{
		return new \ArrayIterator($this->array);
	}

	public function count()
	{
		return count($this->array);
	}

}