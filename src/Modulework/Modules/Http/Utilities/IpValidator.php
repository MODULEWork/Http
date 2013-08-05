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
class IpValidator implements IpValidatorInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function all($ip)
	{

		$func = false !== strpos($ip, ':') ? 'ipv6': 'ipv4';
		
		if (self::isPrivate($ip) || self::isReserved($ip) || self::isBroadcast($ip)) {
			return false;
		}
		return self::$func($ip);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function ipv4($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function ipv6($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function isPrivate($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public static function isReserved($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public static function isBroadcast($ip)
	{
		$segments = explode('.', $ip);
		if (isset($segments[3])) {
			return ($segments[3] == '0' || $segments[3] == '255');
		} else {
			return false;
		}
		
	}
}