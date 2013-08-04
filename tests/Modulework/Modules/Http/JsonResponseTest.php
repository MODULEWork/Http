<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */

use Modulework\Modules\Http\JsonResponse;

/**
* PHPUnit Test
*/
class JsonResponseTest extends PHPUnit_Framework_TestCase
{

	public function testMake()
	{
		$response = JsonResponse::make();
		$this->assertInstanceOf('Modulework\Modules\Http\JsonResponse', $response);
	}

	public function testConstruct()
	{
		$response = new JsonResponse;
		$this->assertEquals(new stdClass, $response->getJson(true));

		$response = new JsonResponse(array('foo' => 'bar'));
		$this->assertEquals(array('foo' => 'bar'), $response->getJson(true));
	}

	public function testSetJson()
	{
		$data = array('foo' => 'bar');
		$response = JsonResponse::make();
		
		$response->setJson($data);

		$json = new ReflectionProperty($response, 'json');
		$json->setAccessible(true);
		
		$raw = new ReflectionProperty($response, 'rawdata');
		$raw->setAccessible(true);
		
		$this->assertEquals(json_encode($data), $json->getValue($response));
		$this->assertEquals($data, $raw->getValue($response));
	}

	public function testGetJson()
	{
		$data = array('foo' => 'bar');
		$response = JsonResponse::make($data);

		
		$this->assertEquals(json_encode($data), $response->getJson());
		$this->assertEquals($data, $response->getJson(true));
	}

	public function testSetCallback()
	{
		$response = JsonResponse::make();
		
		$response->setCallback('$');

		$cb = new ReflectionProperty($response, 'callback');
		$cb->setAccessible(true);
		
		$this->assertEquals('$', $cb->getValue($response));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetCallbackException()
	{
		$response = JsonResponse::make();
		
		$response->setCallback('false');

	}

	public function testGetCallback()
	{
		$response = JsonResponse::make();
		$response->setCallback('$');
		
		$this->assertEquals('$', $response->getCallback());
	}

	/**
	 * @dataProvider isValidIdentifierData
	 */
	public function testIsValidIdentifier($idf, $exp)
	{
		$response = JsonResponse::make();

		$rm = new \ReflectionMethod($response, 'isValidIdentifier');
		$rm->setAccessible(true);

		$this->assertSame($exp, $rm->invoke($response, $idf));
	}

	public function isValidIdentifierData()
	{
		return array(
				array('$', true),
				array('foo', true),
				array('bar/', false),
				array('foo.', false),
				array('instanceof', false),
				array('false', false),
			);
	}

}