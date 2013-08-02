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

	public function testGetDate()
	{
		$response = Response::make();

		$date = new DateTime(null, new DateTimeZone('UTC'));

		$this->assertEquals($date->format('D, d M Y H:i:s') . ' GMT', $response->getDate());
	}

	public function testSetProtocolVersion()
	{
		$response = Response::make();
		$response->setProtocolVersion('1.1');

		$this->assertEquals('1.1', $response->getProtocolVersion());
	}

	/**
	 * @dataProvider setStatusCodeData
	 */
	public function testSetStatusCode($code, $txt, $expTxt)
	{
		$response = Response::make();
		$response->setStatusCode($code, $txt);
		$statusText = new ReflectionProperty($response, 'statusText');
		$statusText->setAccessible(true);

		$this->assertEquals($expTxt, $statusText->getValue($response));
	}

	public function setStatusCodeData()
	{
		return array(
			array(
				200,
				null,
				'OK'
				),
			array(
				200,
				false,
				''
				),
			array(
				200,
				'foo',
				'foo'
				),
			array(
				199,
				'foo',
				'foo'
				),
			array(
				199,
				null,
				''
				),
			);
	}

	public function testAddCookie()
	{
		$response = Response::make();
		$response->addCookie(Cookie::make('foo'));

		$this->assertEquals(1, count($response->cookies));
	}

	public function testSendContent()
	{
		$response = Response::make(200, array(), 'foo');
		ob_start();
		$response->sendContent();
		$actual = ob_get_clean();

		$this->assertEquals('foo', $actual);
	}

	public function testSendHeaders()
	{
		$response = Response::make();
		$headers = $response->sendHeaders();

		$this->assertObjectHasAttribute('content', $headers);
		$this->assertObjectHasAttribute('statusCode', $headers);
		$this->assertObjectHasAttribute('statusText', $headers);
		$this->assertObjectHasAttribute('protocolVersion', $headers);
		$this->assertObjectHasAttribute('headers', $headers);

	}

	public function testSendCookies()
	{
		$response = Response::make();
		$coo = $response->sendCookies();

		$this->assertObjectHasAttribute('content', $coo);
		$this->assertObjectHasAttribute('statusCode', $coo);
		$this->assertObjectHasAttribute('statusText', $coo);
		$this->assertObjectHasAttribute('protocolVersion', $coo);
		$this->assertObjectHasAttribute('headers', $coo);
	}

	public function testSend()
	{
		$response = Response::make();
		$res = $response->send();

		$this->assertObjectHasAttribute('content', $res);
		$this->assertObjectHasAttribute('statusCode', $res);
		$this->assertObjectHasAttribute('statusText', $res);
		$this->assertObjectHasAttribute('protocolVersion', $res);
		$this->assertObjectHasAttribute('headers', $res);

	}


}