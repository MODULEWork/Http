<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 
/**
 * HeaderWrapperInterface is an OOP way of handling headers
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
	
}