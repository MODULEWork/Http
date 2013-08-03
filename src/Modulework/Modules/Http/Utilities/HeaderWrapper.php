<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

/**
 * {@inheritdoc}
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
			return header($string, $replace);
		} else {
			return header($string, $replace, $http_response_code);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public static function setcookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
	{
		return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}
}