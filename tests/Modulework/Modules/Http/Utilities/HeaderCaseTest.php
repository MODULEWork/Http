<?php
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework Tests
 * License: View distributed LICENSE file
 *
 * 
 * This file is meant to be used in PHPUnit Tests
 */
use Modulework\Modules\Http\Utilities\HeaderCase;
/**
* PHPUnit Test
*/
class HeaderCaseTest extends PHPUnit_Framework_TestCase
{
	public function testGetDate()
	{
		$case = new HeaderCase(array('foo' => 'Mon, 26 Nov 2013 20:49:00 +0200'));
        $headerDate = $case->getDate('foo');
        $this->assertInstanceOf('DateTime', $headerDate);
	}
}