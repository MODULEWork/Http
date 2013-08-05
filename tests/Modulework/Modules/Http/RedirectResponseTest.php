<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */

use Modulework\Modules\Http\RedirectResponse;

/**
* PHPUnit Test
*/
class RedirectResponseTest extends PHPUnit_Framework_TestCase
{
	public function testMake()
	{
		$response = RedirectResponse::make('http://foo.bar');
		$this->assertInstanceOf('Modulework\Modules\Http\RedirectResponse', $response);
	}

	public function testConstruct()
	{
		$response = new RedirectResponse('http://foo.bar');
		$this->assertEquals('http://foo.bar', $response->getUrl());
		$this->assertEquals(302, $response->getStatusCode());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructExecption()
	{
		$response = new RedirectResponse('http://foo.bar', 200);
	}

}