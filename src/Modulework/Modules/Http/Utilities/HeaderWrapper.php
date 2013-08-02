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
class HeaderWrapper implements HeaderWrapperInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function headers_sent(&$file = null, &$line = null)
	{
		if ($file === null && $line === null) return headers_sent();

		return headers_sent($file, $line);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function header($string, $replace = true, $http_response_code = null)
	{
		if ($http_response_code === null) {
			header($string, $replace);
		} else {
			header($string, $replace, $http_response_code);
		}
	}
}