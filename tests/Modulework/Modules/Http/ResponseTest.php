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

		$response = Response::make('FooBarBody', 302, array('Foo' => 'bar'));
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

		$response = new Response('FooBarBody', 302, array());
		$this->assertEquals(302, $response->getStatusCode());
		$this->assertEquals('FooBarBody', $response->getContent());
	}

	public function testToString()
	{
		$response = Response::make();
		$response = explode("\r\n", $response);
		$this->assertEquals("HTTP/1.0 200 OK", $response[0]);
	}

	public function testSetHeaderWrapper()
	{
		$response = Response::make();
		$response->setHeaderWrapper(new UnitHeaderWrapper);

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
		$response = Response::make('foo', 200, array());
		$this->expectOutputString('foo');
		$response->sendContent();

	}

	public function testSendHeaders()
	{
		$headersSent = true;
		$headers = array();
		$cookies = array();

		UnitHeaderWrapper::setUp($headersSent, $headers, $cookies);

		$response = Response::make('', 302, array('Location' => 'foo.bar'));
		$response->setHeaderWrapper(new UnitHeaderWrapper);
		$response->sendHeaders();

		
		$this->assertCount(0, $headers);


		$headersSent = false;
		$headers = array();
		$cookies = array();

		UnitHeaderWrapper::setUp($headersSent, $headers, $cookies);

		$response = Response::make('', 302, array('Location' => 'foo.bar'));
		$response->setHeaderWrapper(new UnitHeaderWrapper);
		$response->sendHeaders();

		
		$this->assertEquals('HTTP/1.0 302 Found', $headers[0]['string']);
		$this->assertEquals('Location: foo.bar', $headers[1]['string']);

	}

	public function testSendCookies()
	{

		$headersSent = true;
		$headers = array();
		$cookies = array();

		UnitHeaderWrapper::setUp($headersSent, $headers, $cookies);

		$response = Response::make('', 302, array('Location' => 'foo.bar'));
		$response->setHeaderWrapper(new UnitHeaderWrapper);
		$response->addCookie(Cookie::make('foo'));
		$response->sendCookies();

		$this->assertCount(0, $cookies);

		
		$headersSent = false;
		$headers = array();
		$cookies = array();

		UnitHeaderWrapper::setUp($headersSent, $headers, $cookies);

		$response = Response::make('', 302, array('Location' => 'foo.bar'));
		$response->setHeaderWrapper(new UnitHeaderWrapper);
		$response->addCookie(Cookie::make('foo'));
		$response->sendCookies();

		$this->assertEquals('foo', $cookies[0]['name']);

	}

	public function testSend()
	{
		$headersSent = false;
		$headers = array();
		$cookies = array();

		UnitHeaderWrapper::setUp($headersSent, $headers, $cookies);

		$response = Response::make('foo', 302, array('Location' => 'foo.bar'));
		$response->setHeaderWrapper(new UnitHeaderWrapper);

		$this->expectOutputString('foo');
		$response->send();

		
		$this->assertEquals('HTTP/1.0 302 Found', $headers[0]['string']);
		$this->assertEquals('Location: foo.bar', $headers[1]['string']);

	}

	public function testAddHeader()
	{
		$response = Response::make();

		$response->addHeader('Location', 'foo.bar');

		// Not relevant for this test
		$response->headers->remove('Date');

		$this->assertEquals(array('Location' => 'foo.bar'), $response->headers->all());
	}


}

/**
 * Mocked Classes:
 * - UnitHeaderWrapper
 *
 */

class UnitHeaderWrapper implements \Modulework\Modules\Http\Utilities\HeaderWrapperInterface
{
	public static $headersSent = false;
	public static $headers = array();
	public static $cookies = array();

	public static function setUp(&$headersSent = array(), &$headers = array(), &$cookies = array())
	{
		self::$headersSent 	= &$headersSent;
		self::$headers 		= &$headers;
		self::$cookies 		= &$cookies;
	}
	public static function headers_sent(&$file = null, &$line = null)
	{
		return (self::$headersSent);
	}

	public static function header($string, $replace = true, $http_response_code = null)
	{
		$tmp = array(
			'string' => $string,
			'replace' => $replace,
			'http_response_code' => $http_response_code
			);
		self::$headers[] = $tmp;
	}
	

	public static function setcookie($name, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
	{
		$tmp = array(
			'name' => $name,
			'value' => $value,
			'expire' => $expire,
			'path' => $path,
			'domain' => $domain,
			'secure' => $secure,
			'httponly' => $httponly
			);
		self::$cookies[] = $tmp;
		return true;
	}
	
}