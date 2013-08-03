<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 * <strong>This just for the code coverage report, it' s not TESTING, but RUNNING the code</strong>
 */
use Modulework\Modules\Http\Utilities\HeaderWrapper;
/**
* PHPUnit Test
*/
class HeaderWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testheaders_sent()
	{
		HeaderWrapper::headers_sent();
		$str = 'foo';
		$num = 12;
		HeaderWrapper::headers_sent($str, $num);
	}

	/**
	 * @dataProvider headerData
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testheader($str, $rep, $code)
	{
		HeaderWrapper::header($str, $rep, $code);
	}

	public function headerData()
	{
		return array(
			array(
				'foo', true, null
				),
			array(
				'foo', true, 404
				)
			);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testsetcookie()
	{
		HeaderWrapper::setcookie('foo');
	}


}