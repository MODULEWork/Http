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
	/**
	 * Checks if it is a valid IPv4 or IPv6 address
	 * Private, reserved and broadcast addresses will return false.
	 * @param  string $ip The IP to check
	 * @return boolean    Whether it is a valid IP address
	 */
	public static function all($ip)
	{

		$func = false !== strpos($ip, ':') ? 'ipv6': 'ipv4';
		
		if (self::privateIp($ip) || self::reserved($ip) || self::broadcast($ip)) {
			return false;
		}
		return self::$func($ip);
	}

	/**
	 * Checks if the given string is valid IPv4 address
	 * @param  string $ip 	The IP
	 * @return boolean     	Whether it is a valid IPv4 address
	 */
	public static function ipv4($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false);
	}

	/**
	 * Checks if the given string is valid IPv6 address
	 * @param  string $ip 	The IP
	 * @return boolean     	Whether it is a valid IPv6 address
	 */
	public static function ipv6($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false);
	}

	/**
	 * Checks if the given string is a private IP address
	 * (in private range (RFC 1918))
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is a private address
	 */
	public static function privateIp($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Checks if the given string is a reserved IP address
	 * (in reserved range)
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is a reserved IP address
	 */
	public static function reserved($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Checks if the given string is a broadcast IP address
	 * e.g. 0.0.0.0
	 * @param  string $ip 	The IP
	 * @return boolean     	TRUE if the IP is a broadcast address | FALSE if not a valid IP
	 */
	public static function broadcast($ip)
	{
		$segments = explode('.', $ip);
		if (isset($segments[3])) {
			return ($segments[3] == '0' || $segments[3] == '255');
		} else {
			return false;
		}
		
	}
}