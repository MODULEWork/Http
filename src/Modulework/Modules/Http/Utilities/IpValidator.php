<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 

/**
 * IP Validator
 * Validates IPv4 and IPv6 address
 */
class IpValidator
{
	public static function all($ip)
	{
		if (!self::ipv4($ip)) {
			return false;

		} elseif (!self::ipv6($ip)) {
			return false;

		} elseif (!self::notPrivate($ip)) {
			return false;

		} elseif (!self::notReserved($ip)) {
			return false;

		} elseif (!self::notBroadcast($ip)) {
			return false;

		}
	}

	/**
	 * Checks if the given string is valid IPv4 address
	 * @param  string $ip 	The IP
	 * @return boolean     	Whether it is a valid IPv4 address
	 */
	public static function ipv4($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	/**
	 * Checks if the given string is valid IPv6 address
	 * @param  string $ip 	The IP
	 * @return boolean     	Whether it is a valid IPv6 address
	 */
	public static function ipv6($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	/**
	 * Checks if the given string is not a private IP address
	 * (in private range (RFC 1918))
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is NOT a private address
	 */
	public static function notPrivate($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
	}

	/**
	 * Checks if the given string is not a reserved IP address
	 * (in reserved range)
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is NOT a private address
	 */
	public static function notReserved($ip)
	{
		return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);
	}

	/**
	 * Checks if the given string is not a broadcast IP address
	 * e.g. 0.0.0.0
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is NOT a broadcast address | FALSE if not a valid IP
	 */
	public static function notBroadcast($ip)
	{
		$segments = explode('.', $ip);
		if (isset($segments[3])) {
			return !($segments[3] == '0' || $segments[3] == '255');
		} else {
			return false;
		}
		
	}
}