<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */

use Modulework\Modules\Http\Cookie;

/**
* PHPUnit Test
*/
class CookieTest extends PHPUnit_Framework_TestCase
{
	public function testMake()
	{
		$cookie = Cookie::make('foo');

		$this->assertInstanceOf('Modulework\Modules\Http\Cookie', $cookie);
	}

	public function testConstruct()
	{
		$time = time();
		$cookie = new Cookie('foo', null, $time);
		$this->assertEquals($time, $cookie->getExpiresTime());

		$time = '08/25/2013 15:17:54 UTC';
		$cookie = new Cookie('foo', null, $time);
		$time = strtotime($time);
		$this->assertEquals($time, $cookie->getExpiresTime());

		$time = new DateTime;
		$cookie = new Cookie('foo', null, $time);
		$this->assertEquals($time->format('U'), $cookie->getExpiresTime());
	}

	public function testGetName()
	{
		$cookie = Cookie::make('foo');

		$this->assertEquals('foo', $cookie->getName());
	}

	public function testGetValue()
	{
		$cookie = Cookie::make('foo');

		$this->assertEquals(null, $cookie->getValue());

		$cookie = Cookie::make('foo', 'bar');

		$this->assertEquals('bar', $cookie->getValue());
	}

	public function testGetExpiresTime()
	{
		$cookie = Cookie::make('foo');

		$this->assertEquals(0, $cookie->getExpiresTime());

		$cookie = Cookie::make('foo', null, 555);

		$this->assertEquals(555, $cookie->getExpiresTime());
	}

	public function testGetPath()
	{
		$cookie = Cookie::make('foo');

		$this->assertEquals('/', $cookie->getPath());

		$cookie = Cookie::make('foo', 'bar', true, 'baz');

		$this->assertEquals('baz', $cookie->getPath());
	}

	public function testGetDomain()
	{
		$cookie = Cookie::make('foo');

		$this->assertEquals(null, $cookie->getDomain());

		$cookie = Cookie::make('foo', null, 0, '/', 'foo.bar');

		$this->assertEquals('foo.bar', $cookie->getDomain());
	}

	public function testIsSecure()
	{
		$cookie = Cookie::make('foo');

		$this->assertFalse($cookie->isSecure());

		$cookie = Cookie::make('foo', null, 0, '/', null, true);

		$this->assertTrue($cookie->isSecure());
	}

	public function testIsHttpOnly()
	{
		$cookie = Cookie::make('foo');

		$this->assertTrue($cookie->isHttpOnly());

		$cookie = Cookie::make('foo', null, 0, '/', null, false, false);

		$this->assertFalse($cookie->isHttpOnly());
	}

	public function testStillExists()
	{
		$time = time() - 3600;
		$cookie = Cookie::make('foo', null, $time);

		$this->assertFalse($cookie->stillExists());


		$time = time() + 3600;
		$cookie = Cookie::make('foo', null, $time);

		$this->assertTrue($cookie->stillExists());
	}

	public function testSetName()
	{
		$cookie = Cookie::make('foo');
		$cookie->setName('bar');
		$this->assertEquals('bar', $cookie->getName());
	}

	public function testSetValue()
	{
		$cookie = Cookie::make('foo');
		$cookie->setValue('bar');
		$this->assertEquals('bar', $cookie->getValue());
	}

	public function testSetExpiresTime()
	{
		$cookie = Cookie::make('foo');
		$cookie->setExpiresTime(123456789);
		$this->assertEquals(123456789, $cookie->getExpiresTime());
	}

	public function testSetPath()
	{
		$cookie = Cookie::make('foo');
		$cookie->setPath('bar');
		$this->assertEquals('bar', $cookie->getPath());
	}

	public function testSetDomain()
	{
		$cookie = Cookie::make('foo');
		$cookie->setDomain('foo.bar');
		$this->assertEquals('foo.bar', $cookie->getDomain());
	}

	public function testSetSecure()
	{
		$cookie = Cookie::make('foo');
		$cookie->setSecure(true);
		$this->assertTrue($cookie->isSecure());
	}

	public function testSetHttpOnly()
	{
		$cookie = Cookie::make('foo');
		$cookie->setHttpOnly(false);
		$this->assertFalse($cookie->isHttpOnly());
	}

}