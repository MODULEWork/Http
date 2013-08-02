<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Response;
/**
* PHPUnit Test
*/
class ResponseTest extends PHPUnit_Framework_TestCase
{

	public function testMake()
	{
		$response = Response::make();
		$this->assertInstanceOf('Modulework\Modules\Http\Response', $response);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('', $response->getContent());

		$response = Response::make(302, array('Foo' => 'bar'), 'FooBarBody');
		$this->assertInstanceOf('Modulework\Modules\Http\Response', $response);
		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEquals('FooBarBody', $response->getContent());
		$this->assertEquals('bar', $response->headers->get('Foo'));
	}

	public function testConstruct()
	{
		$response = new Response;
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('', $response->getContent());

		$response = new Response(302, array(), 'FooBarBody');
		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEquals('FooBarBody', $response->getContent());
	}

	public function testToString()
	{
		$response = Response::make();
		$response = explode("\r\n", $response);
		$this->assertEquals("HTTP/1.0 200 OK", $response[0]);
	}


}