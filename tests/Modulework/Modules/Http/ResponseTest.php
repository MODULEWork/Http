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
	public function testConstruct()
	{
		$response = new Response;
	}
}