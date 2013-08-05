<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use DateTime;
use InvalidArgumentException;

/**
* Cookie
* Cookie is an OOP way of handling a cookie
*/
class Cookie {

	/**
	 * The name of the cookie
	 * @var string
	 */
	protected $name;

	/**
	 * The value of the cookie
	 * @var mixed
	 */
	protected $value;

	/**
	 * The domain attribute of the cookie
	 * @var string|null
	 */
	protected $domain;

	/**
	 * The expire date of the cookie
	 * @var int
	 */
	protected $expire;

	/**
	 * The path of where the cookie should be avaible
	 * @var string
	 */
	protected $path;

	/**
	 * Whether the cookie is SSL only
	 * @var bool
	 */
	protected $secure;

	/**
	 * Whether the cookie is HTTP only
	 * @var bool
	 */
	protected $httpOnly;


	/**
	 * Factory for a cookie
	 * @param  string  $name     The name of the cookie
	 * @param  mixed   $value    The value of the cookie
	 * @param  integer $expire   The expire date of the cookie (DateTime object possible as well) UNIX timestamp
	 * @param  string  $path     The path of where the cookie should be avaible
	 * @param  string  $domain   The domain attribute of the cookie
	 * @param  boolean $secure   Whether the cookie is SSL only
	 * @param  boolean $httpOnly Whether the cookie is HTTP only
	 *
	 * @return \Modulework\Modules\Http\Cookie   A new Cookie instance
	 *
	 * @throws \InvalidArgumentException (from Constructor)
	 */
	public static function make($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
	{
		return new static($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

	/**
	 * Constructor.
	 * @param  string  $name     The name of the cookie
	 * @param  mixed   $value    The value of the cookie
	 * @param  integer $expire   The expire date of the cookie (DateTime object possible as well) UNIX timestamp
	 * @param  string  $path     The path of where the cookie should be avaible
	 * @param  string  $domain   The domain attribute of the cookie
	 * @param  boolean $secure   Whether the cookie is SSL only
	 * @param  boolean $httpOnly Whether the cookie is HTTP only
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
	{
		if ($name == '') throw new InvalidArgumentException('Cookie name cannot be empty');

		if ($expire instanceof DateTime) {
			$expire = $expire->format('U');
		} elseif (!is_numeric($expire)) {
			$expire = strtotime($expire);	
		}

		$this->name 		= $name;
		$this->value 		= $value;
		$this->domain 		= $domain;
		$this->expire 		= $expire;
		$this->path 		= ($path == '') ? '/' : $path;
		$this->secure 		= (Boolean) $secure;
		$this->httpOnly 	= (Boolean) $httpOnly;
	}

	/**
	 * Set the cookie' s name
	 * @param string $name The name
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Returns the name of the cookie
	 * @return string The name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the cookie' s value
	 * @param mixed $value The value
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns the value of the cookie
	 * @return mixed The value
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Set the cookie' s expire time
	 * @param string $expire The expire time
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setExpiresTime($expire)
	{
		$this->expire = $expire;
		return $this;
	}

	/**
	 * Returns the expire timestamp (UNIX)
	 * @return int Unix Timestamp
	 */
	public function getExpiresTime()
	{
		return $this->expire;
	}

	/**
	 * Set the cookie' s path
	 * @param string $path The path
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Returns the path of the cookie
	 * @return string The path
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the cookie' s domain
	 * @param string $domain The domain
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
		return $this;
	}

	/**
	 * Returns the domain of the cookie
	 * @return string The domain
	 */
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * Set the cookie secure only
	 * @param bool $secure Whether the cookie is SSL only
	 *
	 * @return \Modulework\Modules\Http\Cookie  THIS
	 */
	public function setSecure($secure)
	{
		$this->secure = $secure;
		return $this;
	}

	/**
	 * Checks whether the cookie is SSL only
	 * @return boolean Whether the cookie is "secure"
	 */
	public function isSecure()
	{
		return $this->secure;
	}

	/**
	 * Set the cookie HTTP only
	 * @param bool $httpOnly Whether the cookie is HTTP only
	 */
	public function setHttpOnly($httpOnly)
	{
		$this->httpOnly = $httpOnly;
		return $this;
	}

	/**
	 * Checks whether the cookie is HTTP only
	 * @return boolean Whether the cookie is HTTP only
	 */
	public function isHttpOnly()
	{
		return $this->httpOnly;
	}

	/**
	 * Checks if the cookie still exists on the client side
	 * (Only checks timestamp, not whether the client deleted the cookie)
	 * @return bool Whether the cookie "could" exists on the client side
	 */
	public function stillExists()
	{
		return ($this->expire > time());
	}
}