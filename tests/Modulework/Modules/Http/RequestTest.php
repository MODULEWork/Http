<?php
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

		$request = Request::mock('https://localhost/foo?bar=baz');
		$this->assertEquals(443, $request->getPort());
		$this->assertEquals('localhost', $request->getHttpHost());
		$this->assertTrue($request->isSecure());
	}
}