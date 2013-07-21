<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Request;
/**
* PHPUnit Test
*/
class RequestTest extends PHPUnit_Framework_TestCase
{

	public function testInit()
	{
		$request = new Request();

		// GET
		$request->init(array('foo' => 'bar', 'baz' => 'foo'));
		$this->assertEquals('bar', $request->query->get('foo'));
		$this->assertEquals('foo', $request->query->get('baz'));

		// POST
		$request->init(array(), array('foo' => 'bar', 'baz' => 'foo'));
		$this->assertEquals('bar', $request->request->get('foo'));
		$this->assertEquals('foo', $request->request->get('baz'));

		// COOKIE
		$request->init(array(), array(), array('foo' => 'bar', 'baz' => 'foo'));
		$this->assertEquals('bar', $request->cookies->get('foo'));
		$this->assertEquals('foo', $request->cookies->get('baz'));

		// SERVER
		$request->init(array(), array(), array(), array(), array('foo' => 'bar', 'baz' => 'foo', 'HTTP_FOO' => 'bazz'));
		$this->assertEquals('bar', $request->server->get('foo'));
		$this->assertEquals('foo', $request->server->get('baz'));

		$this->assertEquals('bazz', $request->headers->get('FOO'));
	}

	public function testMock()
	{
		$request = Request::mock('http://localhost/foo?bar=baz');
		$this->assertEquals(80, $request->getPort());
		$this->assertEquals('localhost', $request->getHttpHost());
		$this->assertFalse($request->isSecure());
		$this->assertEquals('baz', $request->query->get('bar'));
		$this->assertEquals('/foo?bar=baz', $request->getBaseUri());

		$request = Request::mock('https://localhost/foo?bar=baz&foo=bar');
		$this->assertEquals(443, $request->getPort());
		$this->assertEquals('localhost', $request->getHttpHost());
		$this->assertTrue($request->isSecure());
		$this->assertEquals('bar', $request->query->get('foo'));
		$this->assertEquals('/foo?bar=baz&foo=bar', $request->getBaseUri());
	}
}