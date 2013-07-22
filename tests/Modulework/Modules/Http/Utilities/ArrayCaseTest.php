<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Utilities\ArrayCase;
/**
* PHPUnit Test
*/
class ArrayCaseTest extends PHPUnit_Framework_TestCase
{
	private $arr;

	public function setUp()
	{
		$this->arr = array('foo' => 'bar', 'baz' => 'qux');
	}

	public function testAll()
	{
		$case = new ArrayCase($this->arr);
		$this->assertEquals($this->arr, $case->all());
	}

	public function testGet()
	{
		$case = new ArrayCase($this->arr);
		$this->assertEquals('bar', $case->get('foo'));
		$this->assertEquals('alt', $case->get('not', 'alt'));
	}

	public function testSet()
	{
		$case = new ArrayCase($this->arr);
		$case->set('key', 'value');
		$this->assertEquals('value', $case->get('key'));

		$case->set('key', 'other_val');
		$this->assertNotEquals('other_val', $case->get('key'));

		$case->set('key', 'other_val', true);
		$this->assertEquals('other_val', $case->get('key'));
	}

	public function testHas()
	{
		$case = new ArrayCase($this->arr);
		$this->assertTrue($case->has('foo'));
		$this->assertFalse($case->has('bar'));
	}

	public function testRemove()
	{
		$case = new ArrayCase($this->arr);
		$this->assertTrue($case->has('baz'));
		$case->remove('baz');
		$this->assertFalse($case->has('baz'));
	}

	public function testMerge()
	{
		$arr2 = array('key' => 'val');
		$tmp_arr = array_merge($this->arr, $arr2);

		$case = new ArrayCase($this->arr);
		$case->merge($arr2);

		$this->assertEquals($tmp_arr, $case->all());
	}

	public function testMock()
	{
		$arr = array('key' => 'val');

		$case = new ArrayCase($this->arr);
		$case->mock($arr);

		$this->assertEquals($arr, $case->all());
	}

	public function testKeys()
	{
		$case = new ArrayCase($this->arr);
		$this->assertEquals(array_keys($this->arr), $case->keys());
	}

	public function testGetIterator()
	{
		$tmp = array();

		$case = new ArrayCase($this->arr);
		foreach ($case as $key => $value) {
			$tmp[$key] = $value;
		}
		
		$this->assertEquals($this->arr, $tmp);

	}

	public function testCount()
	{
		$case = new ArrayCase($this->arr);
		$this->assertEquals(2, count($case));
	}
}