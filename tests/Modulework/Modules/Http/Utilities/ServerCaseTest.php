<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Utilities\ServerCase;
/**
* PHPUnit Test
*/
class ServerCaseTest extends PHPUnit_Framework_TestCase
{
	public function testGetHeaders()
	{
		$arr = array('HTTP_FOO' => 'bar', 'HTTP_BAZ' => 'qux');
		$ret = array('FOO' => 'bar', 'BAZ' => 'qux');


		$case = new ServerCase($arr);
		$this->assertEquals($ret, $case->getHeaders());
	}
}