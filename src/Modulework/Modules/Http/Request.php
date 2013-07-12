<?php 
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */
 
namespace Modulework\Modules\Http;

/**
* 
*/
class Request
{

	/**
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $query;

	/**
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $request;

	/**
	 * @var \Modulework\Modules\Http\ServerCase
	 */
	public $server;

	/**
	 * @var \Modulework\Modules\Http\FileCase
	 */
	public $files;

	/**
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $cookies;

	/**
	 * @var \Modulework\Modules\Http\HeaderCase
	 */
	public $headers;

	/**
	 * @var string
	 */
	protected $uri;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * @var string
	 */
	protected $basePath;

	/**
	 * @var string
	 */
	protected $method;

	/**
	 * Create the Request object from PHP _ENV (or superglobals)
	 * @return \Modulework\Modules\Http\Request The new Request object
	 */
	public static function makeFromGlobals()
	{
		return new static($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
	}

	/**
	 * Constructor
	 * @param  array  $query   GET
	 * @param  array  $request POST
	 * @param  array  $cookies COOKIE
	 * @param  array  $files   FILES
	 * @param  array  $server  SERVER
	 */
	public function __construct(array $query = array(), array $request = array(), array $cookies = array(), array $files = array(), array $server = array())
	{
		$this->init($query, $request, $cookies, $files, $server);
	}

	public function __toString()
	{
		return sprintf('%s %s >> %s', $this->server->get('SERVER_PROTOCOL'), $this->getMethod(), $this->getBaseUri());
	}

	/**
	 * Initialize all parameters
	 * @param  array  $query   GET
	 * @param  array  $request POST
	 * @param  array  $cookies COOKIE
	 * @param  array  $files   FILES
	 * @param  array  $server  SERVER
	 */
	public function init(array $query = array(), array $request = array(), array $cookies = array(), array $files = array(), array $server = array())
	{
		$this->query = new ArrayCase($query);
		$this->request = new ArrayCase($request);
		$this->cookies = new ArrayCase($cookies);
		$this->files = new FileCase($files);
		$this->server = new ServerCase($server);
		$this->headers = new ArrayCase($this->server->getHeaders());
	}

	public function applyChanges()
	{
		$_GET = $this->query->all();
		$_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();
	}

	public function getMethod()
	{
		if ($this->method === null) {
			$this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
		}
		return $this->method;
	}

	public function getBaseUri()
	{
		if ($this->uri === null) {
			$this->uri = $this->generateBaseUri();
		}
		return $this->uri;
	}


	public function isMethod($method)
	{
		return ($this->getMethod() === strtoupper($method));
	}

	public function isXmlHttpRequest()
	{
		return ('XMLHttpRequest' == $this->headers->get('X-Requested-With'));
	}



	protected function generateBaseUri()
	{
		return "Coming soon!";
	}

}