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

	protected $name;
	protected $value;
	protected $domain;
	protected $expire;
	protected $path;
	protected $secure;
	protected $httpOnly;

	public static function make($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
	{
		return new static($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

	public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
	{
		if ($name == '') throw new InvalidArgumentExceptionException('Cookie name cannot be empty');

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

	public function getName()
	{
		return $this->name;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getExpiresTime()
	{
		return $this->expire;
	}

	public function getDomain()
	{
		return $this->domain;
	}

	public function isSecure()
	{
		return $this->secure;
	}

	public function isHttpOnly()
	{
		return $this->httpOnly;
	}

	public function stillExists()
	{
		return ($this->expire < time());
	}
}