<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 
/**
* HeaderWrapper
* This class wraps header releated methods:
* - headers_sent()
* - header()
* - setcookie()
*/
interface HeaderWrapperInterface
{
	/**
	 * Wrapper for PHP' s headers_sent()
	 * @param  string $file filename (optional)
	 * @param  string $line linenumber (optional)
	 * @return bool       The return value of PHP' s headers send
	 */
	public static function headers_sent(&$file = null, &$line = null);
	
	/**
	 * Wrapper for PHP' s header()
	 * @param  string  $string             Der Header-String.
	 * @param  boolean $replace            Replace existing headers
	 * @param  int     $http_response_code The HTTP response code
	 */
	public static function header($string, $replace = true, $http_response_code = null);

	/**
	 * Wrapper for PHP' s setcookie()
	 * @param  strign  $name     The name of the cookie
	 * @param  string  $value    The value of the cookie
	 * @param  integer $expire   Expire date of the cookie
	 * @param  string  $path     Valid path for the cookie
	 * @param  string  $domain   Domain name of the cookie
	 * @param  bool    $secure   Only accessable with SSL (HTTPS)
	 * @param  bool    $httponly Only accessable through HTTP protocol
	 * @return bool
	 */
	public static function setcookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false);
	
}