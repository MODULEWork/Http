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
use Modulework\Modules\Http\Request;
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

	public function testSetContent()
	{
		$response = Response::make();
		$response->setContent('foo');

		$this->assertEquals('foo', $response->getContent());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetContentInvalid()
	{
		$response = Response::make();
		$response->setContent(array());
	}

	public function testAppendContent()
	{
		$response = Response::make('foo');
		$response->appendContent('bar');

		$this->assertEquals('foobar', $response->getContent());

		$response = Response::make();
		$response->appendContent('bar');

		$this->assertEquals('bar', $response->getContent());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAppendContentInvalid()
	{
		$response = Response::make('foo');
		$response->appendContent(array());
	}

	/**
	 * @dataProvider validateContenteData
	 */
	public function testValidateContent($var, $exp)
	{
		$response = Response::make();

		$rm = new \ReflectionMethod($response, 'validateContent');
		$rm->setAccessible(true);

		$this->assertEquals($exp, $rm->invoke($response, $var));
	}

	public function validateContenteData()
	{
		return array(
			array(
				'string', true
				),
			array(
				111, true
				),
			array(
				new Response, true
				),
			array(
				new stdClass, false
				),
			array(
				function() {
					return 'string';
				}, false
				),
			array(
				array(), false
				)
			);
	}

	public function testPrepare()
	{
		$req = new Request;

		$response = Response::make();
		$response->addHeader('Location', 'foo.bar');
		$response->prepare($req);

		$this->assertEquals(302, $response->getStatusCode());

		$req = new Request;
		$req->setMethod('HEAD');

		$response = Response::make();
		$response->prepare($req);

		$this->assertNull($response->getContent());

		$req = new Request;
		$req->setMethod('HEAD');

		$response = Response::make();
		$response->addHeader('Cache-Control', 'no-cache');
		
		$ret = $response->prepare($req);

		$this->assertEquals('no-cache', $response->headers->get('pragma'));
		$this->assertEquals(-1, $response->headers->get('expires'));
		$this->assertInstanceOf('Modulework\Modules\Http\Response', $ret);
	}

	public function testSetExpires()
	{
		$date = new \DateTime('Sat, 01 Jan 00 00:00:00');

		$response = Response::make();

		$response->setExpires($date);

		$this->assertEquals($date->format('D, d M Y H:i:s') . ' GMT', $response->headers->get('Expires'));



		$response->setExpires();

		$this->assertNull($response->headers->get('Expires'));
	}

	public function testGetExpires()
	{
		$date = new \DateTime('Sat, 01 Jan 00 00:00:00 GMT');

		$response = Response::make();

		$response->headers->set('Expires', $date->format('D, d M Y H:i:s') . ' GMT');

		$this->assertEquals($date, $response->getExpires());
	}

	/**
	 * @dataProvider isSuccessData
	 */
	public function testIsSuccess($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isSuccess());
	}

	public function isSuccessData()
	{
		return array(
			array(200, true),
			array(199, false),
			array(404, false),
			array(257, true)
			);
	}

	/**
	 * @dataProvider isOkData
	 */
	public function testIsOk($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isOk());
	}

	public function isOkData()
	{
		return array(
			array(200, true),
			array(199, false),
			array(404, false),
			array(257, false)
			);
	}

	/**
	 * @dataProvider isNotFoundData
	 */
	public function testIsNotFound($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isNotFound());
	}

	public function isNotFoundData()
	{
		return array(
			array(200, false),
			array(199, false),
			array(404, true),
			array(257, false)
			);
	}

	/**
	 * @dataProvider isForbiddenData
	 */
	public function testIsForbidden($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isForbidden());
	}

	public function isForbiddenData()
	{
		return array(
			array(200, false),
			array(199, false),
			array(403, true),
			array(257, false)
			);
	}

	/**
	 * @dataProvider isRedirectData
	 */
	public function testIsRedirect($code, $header, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code, $header)->isRedirect());
	}

	public function isRedirectData()
	{
		return array(
			array(200, array('Location' => 'foo.bar'), false),
			array(300, array('Location' => 'foo.bar'), true),
			array(301, array('Location' => 'foo.bar'), true),
			array(302, array(), false),
			);
	}

	/**
	 * @dataProvider isClientErrorData
	 */
	public function testIsClientError($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isClientError());
	}

	public function isClientErrorData()
	{
		return array(
			array(200, false),
			array(199, false),
			array(403, true),
			array(500, false)
			);
	}

	/**
	 * @dataProvider isServerErrorData
	 */
	public function testIsServerError($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isServerError());
	}

	public function isServerErrorData()
	{
		return array(
			array(200, false),
			array(500, true),
			array(503, true),
			array(600, false)
			);
	}

	/**
	 * @dataProvider isEmptyData
	 */
	public function testIsEmpty($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isEmpty());
	}

	public function isEmptyData()
	{
		return array(
			array(200, false),
			array(201, true),
			array(204, true),
			array(304, true),
			array(600, false),
			);
	}

	/**
	 * @dataProvider isInvalidData
	 */
	public function testIsInvalid($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isInvalid());
	}

	public function isInvalidData()
	{
		return array(
			array(200, false),
			array(800, true),
			array(026, true),
			array(600, true)
			);
	}

	/**
	 * @dataProvider isInformationalData
	 */
	public function testIsInformational($code, $exp)
	{
		$this->assertEquals($exp, Response::make('', $code)->isInformational());
	}

	public function isInformationalData()
	{
		return array(
			array(200, false),
			array(150, true),
			array(100, true),
			array(600, false)
			);
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