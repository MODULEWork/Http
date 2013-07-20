<?php
use Modulework\Modules\Http\Request;
/**
* PHPUnit Test
*/
class RequestTest extends PHPUnit_Framework_TestCase
{

	public function testInit()
	{
		$request = new Request();

		$request->init(array('foo' => 'bar', 'baz', 'foo'));
		$this->assertEquals('bar', $request->query->get('foo'));
	}

}