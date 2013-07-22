<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 
/**
 * ArrayCase is an OOP way of handling arrays
 */
interface ArrayCaseInterface
{
	/**
	 * Constructor.
	 * @param array $array The array to "wrap"
	 */
	public function __construct(array $array = array());

	/**
	 * Returns the array
	 * @return array The array
	 */
	public function all();

	/**
	 * Returns the value for a specific key
	 * @param  string 	$key     The key
	 * @param  mixed 	$default This returns when the key wasn' t found
	 * @return mixed
	 */
	public function get($key, $default = null);

	/**
	 * Sets a parameter for a specific key
	 * @param string 	$key   		The key
	 * @param mixed 	$value 		The value to store
	 * @param boolean 	$override	FALSE will force the method to not override existing keys
	 * @return boolean 	Whether the setting was successful
	 */
	public function set($key, $value, $override = true);

	/**
	 * Checks if a key parameter exists
	 * @param  string  $key The key
	 * @return boolean      TRUE if found
	 */
	public function has($key);

	/**
	 * Removes a parameter from the array
	 * @param  string $key The key
	 */
	public function remove($key);

	/**
	 * Merge the internal array with the new one
	 * @param  array  $array The array to merge
	 */
	public function merge(array $array = array());

	/**
	 * Replace the internal completly with the new one
	 * It' s just a assigning, no magic things!
	 * @param  array  $array The array
	 */
	public function mock(array $array = array());

	/**
	 * Return all keys from the array
	 * @return array The keys in a array
	 */
	public function keys();
}