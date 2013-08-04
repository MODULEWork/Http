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
		$this->assertEquals('{}', $response->getJson());
		
	}
}