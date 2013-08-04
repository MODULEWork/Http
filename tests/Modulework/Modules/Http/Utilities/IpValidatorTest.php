<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Utilities\IpValidator;
/**
* PHPUnit Test
*/
class IpValidatorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider ipv4Data
	 */
	public function testIpv4($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::ipv4($ip));
	}

	public function ipv4Data()
	{
		return array(
			array('127.0.0.1', true),
			array('127.0.1', false),
			array('8.8.8.8', true),
			array('54:45:45', false),
			array('0.0.0.0', true),
			array('foo.bar', false),
			array('foobar', false)
			);
	}

	/**
	 * @dataProvider ipv6Data
	 */
	public function testIpv6($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::ipv6($ip));
	}

	public function ipv6Data()
	{
		return array(
			array('127.0.0.1', false),
			array('2001:0db8:85a3:08d3:1319:8a2e:0370:7334', true),
			array('2001:0db8:0000:0000:0000:ff00:0042:8329', true),
			array('2001:db8:0:0:0:ff00:42:8329', true),
			array('2001:db8::ff00:42:8329', true),
			array('0000:0000:0000:0000:0000:0000:0000:0001', true),
			array('::1', true),
			array('0000:050:0000:0000:0000:0000:0000:0001', true),
			array('foo:050:bar:baz:', false)
			);
	}

	/**
	 * @dataProvider privateIpData
	 */
	public function testprivateIp($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::privateIp($ip));
	}

	public function privateIpData()
	{
		return array(
			array('127.0.0.1', true),
			array('5.254.63.157', false),
			);
	}

	/**
	 * @dataProvider reservedData
	 */
	public function testreserved($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::reserved($ip));
	}

	public function reservedData()
	{
		return array(
			array('127.0.0.1', true),
			array('5.254.63.157', false),
			);
	}

	/**
	 * @dataProvider broadcastData
	 */
	public function testBroadcast($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::broadcast($ip));
	}

	public function broadcastData()
	{
		return array(
			array('255.255.255.255', true),
			array('0.0.0.0', true),
			array('5.254.63.157', false),
			array('5.foo', false),
			);
	}

	/**
	 * @dataProvider allData
	 */
	public function testAll($ip, $exp)
	{
		$this->assertEquals($exp, IpValidator::all($ip));
	}

	public function allData()
	{
		return array(
			array('127.0.0.1', false),
			array('5.254.63.157', true),
			);
	}
}