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
		$this->headers = new HeaderCase($this->server->getHeaders());
	}

	/**
	 * Write changes to the Request to the globals
	 */
	public function applyChanges()
	{
		$_GET = $this->query->all();
		$_POST = $this->request->all();
        $_SERVER = $this->server->all();
        $_COOKIE = $this->cookies->all();
	}

	/**
	 * Returns the request method
	 * @return string The request method
	 */
	public function getMethod()
	{
		if ($this->method === null) {
			$this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
		}
		return $this->method;
	}

	/**
	 * Mock the request method
	 * @param string $method The request method
	 */
	public function setMethod($method)
	{
		$this->$method = null; //Reset, so it' s getting regenerated properly.
		$this->server->set('REQUEST_METHOD', $method);
	}

	/**
	 * Returns either http or https
	 * calls isSecure()
	 * @return string The scheme (http | https)
	 */
	public function getScheme()
	{
		return $this->isSecure() ? 'https' : 'http';
	}

	/**
	 * Returns the HTTP Host (with port if not default)
	 * 
	 * For example:
	 *	localhost
	 * or
	 * 	localhost:4000
	 * 
	 * @return string The host
	 */
	public function getHttpHost()
	{
		$port = $this->getPort();
		$scheme = $this->getScheme();

		if (('https' == $scheme && $port == 443) || ('http' == $scheme && $port == 80)) {
			// if standard ports then don' t add em
			return $this->getHost();
		}

		return $this->getHost() . ':' . $port;

	}

	/**
	 * Returns the host of the server
	 * @return [type] [description]
	 */
	public function getHost()
	{
		if (!$host = $this->headers->get('HOST')) {
			if (!$host = $this->server->get('SERVER_ADDR')) {
				$host = $this->server->get('SERVER_NAME', '');
			}
		}

		$host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

		return $host;
	}

	/**
	 * Set the host in the header
	 * @param string $host The new host
	 */
	public function setHost($host)
	{
		$this->headers->set('HOST', $host);
	}

	/**
	 * Port for the Request
	 * @return string the port
	 */
	public function getPort()
	{
		return $this->server->get('SERVER_PORT');
	}

	/**
	 * Get the BaseUri
	 * Calls once: generateBaseUri()
	 * @return [type] [description]
	 */
	public function getBaseUri()
	{
		if ($this->uri === null) {
			$this->uri = $this->generateBaseUri();
		}
		return $this->uri;
	}

	/**
	 * Check if the request method equals the given
	 * @param  string  $method The method to test
	 * @return boolean         TRUE if match
	 */
	public function isMethod($method)
	{
		return ($this->getMethod() === strtoupper($method));
	}

	/**
	 * Wrapper for isXmlHttpRequest
	 * @return boolean TRUE if it is a XMLHttpRequest
	 */
	public function isAjax()
	{
		return $this->isXmlHttpRequest();
	}

	/**
	 * Is the request of type XMLHttpRequest
	 * @return boolean TRUE if it is a XMLHttpRequest
	 */
	public function isXmlHttpRequest()
	{
		return ('XMLHttpRequest' == $this->headers->get('X-Requested-With'));
	}

	/**
	 * Check for HTTPS connection
	 * @return boolean TRUE if it is a HTTPS connection
	 */
	public function isSecure()
	{
		return (1 == $this->server->get('HTTPS') || strtolower($this->server->get('HTTPS') == 'on'));
	}


	/**
	 * Generate the BaseUri
	 * @return string the base uri
	 */
	protected function generateBaseUri()
	{
		$uri = '';

		/*
		 * The first check is obvious, all these headers after REQUEST_URI are taken from the Zend Framework,
		 * they are for IIS and the like setups.
		 */
		if ($this->server->has('REQUEST_URI')) {
			$uri = $this->server->get('REQUEST_URI');
			// this will be with scheme and host maybe....
			// We' ll need to cut these out
			$schemeHost = $this->getScheme() . '://' . $this->getHttpHost();
			if (strpos($uri, $schemeHost) === 0) {
				$uri = substr($uri, strlen($schemeHost));
			}

		} elseif ($this->server->has('ORIG_PATH_INFO')) {
			$uri = $this->server->get('ORIG_PATH_INFO');
			if (empty($this->server->get('QUERY_STRING'))) {
				$uri .= '?' . $this->server->get('QUERY_STRING');
			}

		} elseif ($this->headers->has('X_ORIGINAL_URL')) {
			$uri = $this->headers->get('X_ORIGINAL_URL');

		} elseif ($this->headers->has('X_REWRITE_URL')) {
			$this->headers->has('X_REWRITE_URL');

		} elseif ($this->server->has('IIS_WasUrlRewritten') == '1' && !empty($this->server->get('UNENCODED_URL'))) {
			$uri = $this->server->get('UNENCODED_URL');

		} elseif ($this->server->has('IIS_WasUrlRewritten') == '1' && !empty($this->server->get('UNENCODED_URL'))) {
			$uri = $this->server->get('UNENCODED_URL');
			
		}
		return $uri;
	}

}