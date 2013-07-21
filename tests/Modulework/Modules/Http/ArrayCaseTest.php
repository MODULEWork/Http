<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\ArrayCase;
/**
* PHPUnit Test
*/
class ArrayCaseTest extends PHPUnit_Framework_TestCase
{
	public function testAll()
	{
		$arr = array('foo' => 'bar', 'baz' => 'qux');
		$case = new ArrayCase($arr);
		$this->assertEquals($arr, $case->all());
	}
}