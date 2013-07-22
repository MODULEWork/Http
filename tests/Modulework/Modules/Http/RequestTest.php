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
		$request = Request::mock('http://localhost/foo?bar=baz', 'GET', array('password' => 'pa$$word'));
		$this->assertEquals(80, $request->getPort());
		$this->assertEquals('localhost', $request->getHttpHost());
		$this->assertFalse($request->isSecure());
		$this->assertEquals('baz', $request->query->get('bar'));
		$this->assertEquals('pa$$word', $request->request->get('password'));
		$this->assertEquals('/foo?bar=baz', $request->getBaseUri());

		$request = Request::mock('https://localhost/foo?bar=baz&foo=bar');
		$this->assertEquals(443, $request->getPort());
		$this->assertEquals('localhost', $request->getHttpHost());
		$this->assertTrue($request->isSecure());
		$this->assertEquals('bar', $request->query->get('foo'));
		$this->assertEquals('/foo?bar=baz&foo=bar', $request->getBaseUri());

		$request = Request::mock('https://localhost:8888/foo?bar=baz&foo=bar');
		$this->assertEquals(8888, $request->getPort());
		$this->assertEquals('localhost:8888', $request->getHttpHost());
		$this->assertTrue($request->isSecure());
		$this->assertEquals('bar', $request->query->get('foo'));
		$this->assertEquals('/foo?bar=baz&foo=bar', $request->getBaseUri());

		$request = Request::mock('https://localhost:8888');
		$this->assertEquals(8888, $request->getPort());
		$this->assertEquals('localhost:8888', $request->getHttpHost());
		$this->assertTrue($request->isSecure());
		$this->assertEquals('/', $request->getBaseUri());
	}

	public function testToString()
	{
		$request = Request::mock('http://localhost/foo?bar=baz');
		$this->assertEquals('HTTP/1.1 GET >> /foo?bar=baz', (string) $request);
	}

	public function testNormalizeQuery()
	{
		$this->assertEquals('foo=', Request::normalizeQuery('foo='));
		$this->assertEquals('her=Diana%20Test&him=Chris%20Master', Request::normalizeQuery('him=Chris%20Master&her=Diana+Test'));
		$this->assertEquals('Chris%20Gaertner&Test%20Case', Request::normalizeQuery('Chris%20Gaertner&Test%20Case'));
		$this->assertEquals('', Request::normalizeQuery(''));
	}

	public function testMakeFromGlobals()
	{
		$_GET['foo'] = 'bar';
		$_POST['password'] = 'foo';
		$_COOKIE = array();
		$_FILES = array();
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = Request::makeFromGlobals();
		$this->assertEquals('GET', $request->getMethod());
	}

	public function testApplyChanges()
	{
		$_GET['foo'] = 'bar';
		$_POST['password'] = 'foo';
		$_COOKIE = array();
		$_FILES = array();
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = Request::makeFromGlobals();
		$request->query->set('foo', 'baz', true);
		$request->applyChanges();

		$this->assertEquals('baz', $_GET['foo']);
	}

	public function testSetMethod()
	{
		$request = Request::mock('http://localhost');
		$request->setMethod('POST');
		
		$this->assertEquals('POST', $request->getMethod());
	}

	public function testGetHost()
	{
		$request = Request::mock('http://localhost');
		
		$this->assertEquals('localhost', $request->getHost());
		
		$request->headers->remove('HOST');
		$request->headers->set('SERVER_ADDR', 'localhost');
		
		$this->assertEquals('localhost', $request->getHost());

	}

	public function testSetHost()
	{
		$request = Request::mock('http://localhost');
		$request->setHost('example.com');

		$this->assertEquals('example.com', $request->getHost());

	}

	public function testGetClientIp()
	{
		$request = Request::mock('http://localhost');
		$request->server->set('REMOTE_ADDR', '::1', true);

		$this->assertEquals('::1', $request->getClientIp());
	}

	public function testGetScript()
	{
		$request = new Request(array(), array(), array(), array(), array('SCRIPT_NAME' => 'foo.php'));

		$this->assertEquals('foo.php', $request->getScript());
	}

	public function testIsAjax()
	{
		$request = new Request(array(), array(), array(), array(), array('HTTP_X-Requested-With' => 'XMLHttpRequest'));

		$this->assertTrue($request->isAjax());

		$request = new Request;

		$this->assertFalse($request->isAjax());

	}

	public function testIsMethod()
	{
		$request = Request::mock('http://localhost');

		$this->assertTrue($request->isMethod('GET'));
		$this->assertFalse($request->isMethod('POST'));

		$request = Request::mock('http://localhost', 'POST');

		$this->assertTrue($request->isMethod('POST'));
		$this->assertFalse($request->isMethod('GET'));
	}

	public function testGetBaseUri()
	{
		$request = Request::mock('http://localhost/foo/bar');
		$request->server->set('REQUEST_URI', $request->getScheme() . '://' . $request->getHttpHost() . $request->server->get('REQUEST_URI'), true);


		$this->assertEquals('/foo/bar', $request->getBaseUri());
	}

	public function testGetBasePath()
	{
		$request = new Request;
		
		$this->assertEquals('', $request->getBasePath());


		$request->init(array(), array(), array(), array(), array(), array('SCRIPT_FILENAME' => '/foo/bar/baz.php'));

		$this->assertEquals('', $request->getBasePath());


		$request->init(array(), array(), array(), array(), array(), array('SCRIPT_FILENAME' => '/foo/bar/baz.php', 'SCRIPT_NAME' => '/index.php'));

		$this->assertEquals('', $request->getBasePath());


		$request->init(array(), array(), array(), array(), array(), array('SCRIPT_FILENAME' => '/foo/bar/baz.php', 'PHP_SELF' => '/index.php'));
		$request->server->remove('SCRIPT_NAME');

		$this->assertEquals('', $request->getBasePath());
	}

	public function testGetPath()
	{
		$request = new Request;
		$request->getPath();
	}
}