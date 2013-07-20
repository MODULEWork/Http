<?php

/**
* PHPUnit Test
*/
class RequestTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @var \Modulework\Modules\Http\Request
	 */
	protected $request;

	protected function setUp()
	{
		$this->request = new \Modulework\Modules\Http\Request(
			array(
				'username' => 'Christian',
				'skill-level' => 'Awesome'
				),
			array(
				'password' => 'pwd',
				'username' => 'Gardner'
				),
			array(),
			array(),
			array(
				'DOCUMENT_ROOT' => '/Test',
				'REMOTE_ADDR' => '8.8.8.8',
				'REMOTE_PORT' => '80',
				'SERVER_SOFTWARE' => 'PHP 5.5.0 Development Server',
				'SERVER_PROTOCOL' => 'HTTP/1.1',
				'SERVER_NAME' => 'localhost',
				'SERVER_PORT' => '80',
				'REQUEST_URI' => '/index.php',
				'REQUEST_METHOD' => 'GET',
				'SCRIPT_NAME' => '/index.php',
				'SCRIPT_FILENAME' => '/Test/index.php',
				'PHP_SELF' => '/index.php',
				'HTTP_HOST' => 'localhost',
				'HTTP_CONNECTION' => 'keep-alive',
				'HTTP_CACHE_CONTROL' => 'max-age=0',
				'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36',
				'HTTP_ACCEPT_ENCODING' => 'gzip,deflate,sdch',
				'HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4',
				'HTTP_COOKIE' => 'PHPSESSID=87b5e845c6be0789b920219d184ec66e',
				'REQUEST_TIME_FLOAT' => 1373648917.0969,
				'REQUEST_TIME' => 1373648917,
				)
			);
	}

	public function testGetMethod()
	{
		$this->assertEquals('GET', $this->request->getMethod());
		
	}

	public function testGetHost()
	{
		$this->assertEquals('localhost', $this->request->getHost());
		$this->assertEquals('localhost', $this->request->getHttpHost());
	}

	public function testGetBaseUri()
	{
		$this->assertEquals('/index.php', $this->request->getBaseUri());
	}

	public function testQuery()
	{
		$this->assertEquals('Christian', $this->request->query->get('username'));
		$this->assertEquals('foo', $this->request->query->get('password', 'foo'));


		$this->assertFalse($this->request->query->has('password'));
		$this->assertTrue($this->request->query->has('username'));


		$this->assertEquals(2, count($this->request->query));


		$this->assertTrue($this->request->query->set('username', 'foo', true));
		$this->assertEquals('foo', $this->request->query->get('username'));

		$this->assertFalse($this->request->query->set('username', 'baz'));
		$this->assertNotEquals('baz', $this->request->query->get('username'));

	}


}