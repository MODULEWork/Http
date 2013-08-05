<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use Modulework\Modules\Http\Utilities\FileCase;
use Modulework\Modules\Http\Utilities\ArrayCase;
use Modulework\Modules\Http\Utilities\HeaderCase;
use Modulework\Modules\Http\Utilities\ServerCase;

/**
* Request
* This class represents the current HTTP request.
* It exposes 6 public vars (objects) for getting information
* For some there are methods for convience.
*/
class Request
{

	/**
	 * The ArrayCase for the QueryString (_GET)
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $query;

	/**
	 * The ArrayCase for the POST request (_POST)
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $request;

	/**
	 * The ArrayCase for the server varibales (_SERVER)
	 * @var \Modulework\Modules\Http\ServerCase
	 */
	public $server;

	/**
	 * The ArrayCase for the files attached to the request (_FILES)
	 * @var \Modulework\Modules\Http\FileCase
	 */
	public $files;

	/**
	 * The ArrayCase for the cookies (_COOKIE)
	 * @var \Modulework\Modules\Http\ArrayCase
	 */
	public $cookies;

	/**
	 * The ArrayCase for the HTTP headers
	 * @var \Modulework\Modules\Http\HeaderCase
	 */
	public $headers;

	/**
	 * The URI of this request
	 * @var string
	 */
	protected $uri;

	/**
	 * The path information of this request
	 * @var string
	 */
	protected $path;

	/**
	 * The base URL of this request
	 * @var string
	 */
	protected $baseUrl;

	/**
	 * The base path of this request
	 * @var string
	 */
	protected $basePath;

	/**
	 * The HTTP verb (GET/POST/PUT/DELETE)
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

	/**
	 * Displays the Request in the following format:
	 * PROTOCOLL VERB >> BASEURI
	 * @return string [description]
	 */
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

		// Reset propertys
		$this->uri = null;
		$this->path = null;
		$this->baseUrl = null;
		$this->basePath = null;
		$this->method = null;
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
	 * Mock an request by providing a URI only, to feed more info is still possible
	 * @param  string $uri     The URI
	 * @param  string $method  The HTTP request method
	 * @param  array  $request The _POST values
	 * @param  array  $cookies The _COOKIES
	 * @param  array  $files   The _FILES
	 * @param  array  $server  The _SERVER values
	 * 
	 * @return \Modulework\Modules\Http\Request          A new instance based on the info provided
	 */
	public static function mock($uri, $method = 'GET', array $request = array(), array $cookies = array(), array $files = array(), array $server = array())
	{
		$server = array_replace(array(
								'SERVER_PROTOCOL' => 'HTTP/1.1',
								'SERVER_NAME' => 'localhost',
								'SERVER_PORT' => 80,
								'SCRIPT_NAME' => '',
								'SCRIPT_FILENAME' => '',
								'HTTP_HOST' => 'localhost',
								'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
								'HTTP_USER_AGENT' => 'Modulework/Release',
								'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
								'REQUEST_TIME' => time(),
		), $server);

		$server['REQUEST_METHOD'] = strtoupper($method);


		$parsed = parse_url($uri);

		if (isset($parsed['host'])) {
			$server['SERVER_NAME'] = $parsed['host'];
			$server['HTTP_HOST'] = $parsed['host'];
		}

		if (isset($parsed['scheme'])) {
			if ($parsed['scheme'] === 'https') {
				$server['SERVER_PORT'] = 443;
				$server['HTTPS'] = 'on';
			} else {
				$server['SERVER_PORT'] = 80;
				unset($server['HTTPS']);
			}
		}

		if (isset($parsed['port'])) {
			$server['SERVER_PORT'] = $parsed['port'];
			$server['HTTP_HOST'] .= ':' . $parsed['port'];
		}

		

		if (!isset($parsed['path'])) {
			$parsed['path'] = '/';
		}

		$query = array();
		if (isset($parsed['query'])) {
			parse_str(html_entity_decode($parsed['query']), $query);
		}

		$queryString = http_build_query($query, '', '&');
		$server['QUERY_STRING'] = $queryString;


		$server['REQUEST_URI'] = $parsed['path'] . ('' == $queryString ? '' : '?' . $queryString);


		return new static($query, $request, $cookies, $files, $server);
	}

	/**
	 * Normalize a query string
	 * @param  string $query Query String
	 * @return string        The normalized version of $query
	 */
	public static function normalizeQuery($query)
	{
		if ('' == $query) {
			return '';
		}

		$parts = array();
		$sort = array();

		$split = explode('&', $query);

		foreach ($split as $value) {
			if ('' == $value || '=' === $value[0]) continue;

			$pair = explode('=', $value, 2);

			if (isset($pair[1])) {
				$parts[] = rawurlencode(urldecode($pair[0])) . '=' . rawurlencode(urldecode($pair[1]));
			} else {
				$parts[] = rawurlencode(urldecode($pair[0]));
			}

			$sort[] = urldecode($pair[0]);
		}

		array_multisort($sort, SORT_ASC, $parts);

		return implode('&', $parts);
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
		$this->server->set('REQUEST_METHOD', strtoupper($method), true);
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
	 * @return string The host of this server
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
		$this->headers->set('HOST', $host, true);
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
	 * Retrieve the accepted encoding types from the HTTP headers
	 * @return array The accepted encodings
	 */
	public function getAcceptedEncodings()
	{
		return explode(',', $this->headers->get('ACCEPT_ENCODING'));
	}

	/**
	 * Get the BaseUri
	 * Calls once: generateBaseUri()
	 * @return string The base uri for the request
	 */
	public function getBaseUri()
	{
		if ($this->uri === null) {
			$this->uri = $this->generateBaseUri();
		}
		return $this->uri;
	}

	/**
	 * The base URL
	 * @return string The base URL
	 */
	public function getBaseUrl()
	{
		if ($this->baseUrl === null) {
			$this->baseUrl = $this->generateBaseUrl();
		}
		return $this->baseUrl;
	}

	/**
	 * Returns the path.
	 * Examples (this class was initalized at /dev on localhost):
	 *
	 * * http://localhost/dev			->	''
	 * * http://localhost/dev/more		->	'/more'
	 * * http://localhost/dev/more?foo	->	'/more'
	 * @return string The path
	 */
	public function getPath()
	{
		if ($this->path === null) {
			$this->path = $this->generatePath();
		}

		return $this->path;
	}

	/**
	 * Returns the base path from the root,
	 * for example if this class was constructed in the subfolder
	 * 'foo' this method would return 'foo' for this uri:
	 * http://localhost/foo/index.php 
	 * @return string The raw path
	 */
	public function getBasePath()
	{
		if ($this->basePath === null) {
			$this->basePath = $this->generateBasePath();
		}

		return $this->basePath;
	}

	/**
	 * Returns the root URL
	 * @return string The root URL
	 */
	public function root()
	{
		return rtrim($this->getScheme() . '://' . $this->getHost() .  $this->getPath(), '/');
	}

	/**
	 * Returns the URL (without query string)
	 * @return string The URL
	 */
	public function url()
	{
		return rtrim(preg_replace('/\?.*/', '', $this->getBaseUri()), '/');
	}

	
	/**
	 * Returns all URI segments
	 * (or empty array if path = '/')
	 * @return array The URI segments
	 */
	public function segments()
	{
		$path = $this->getPath();

		return ($path == '/' ? array() : explode('/', $path));
	}
	
	/**
	 * Get a URI segment
	 * 1 based index
	 * @param  int    $index   The index
	 * @param  mixed  $default If the segement does not exists this will get returned
	 * @return mixed           The URI segment | $default
	 */
	public function segment($index, $default = null)
	{
		$segments = explode('/', rtrim($this->getPath(), '/'));

		$segments = array_filter($segments, function($val) {
			return $val != '';
		});

		return (isset($segments[$index - 1])) ? $segments[$index -1] : $default;
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
	 * Returns the client' s IP address
	 * @return string The IP
	 */
	public function getClientIp()
	{
		return $this->server->get('REMOTE_ADDR');
	}

	/**
	 * Returns the current script name
	 * @return string  The script name
	 */
	public function getScript()
	{
		return $this->server->get('SCRIPT_NAME');
	}


	/**
	 * Generate the BaseUri
	 * @return string The base uri
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
			if ('' == $this->server->get('QUERY_STRING')) {
				$uri .= '?' . $this->server->get('QUERY_STRING');
			}

		} elseif ($this->headers->has('X_ORIGINAL_URL')) {
			$uri = $this->headers->get('X_ORIGINAL_URL');

		} elseif ($this->headers->has('X_REWRITE_URL')) {
			$this->headers->has('X_REWRITE_URL');

		} elseif ($this->server->has('IIS_WasUrlRewritten') == '1' && ('' !== $this->server->get('UNENCODED_URL'))) {
			$uri = $this->server->get('UNENCODED_URL');

		} elseif ($this->server->has('IIS_WasUrlRewritten') == '1' && ('' !== $this->server->get('UNENCODED_URL'))) {
			$uri = $this->server->get('UNENCODED_URL');
			
		}

		$this->server->set('REQUEST_URI', $uri);

		return $uri;
	}

	/**
	 * Generate the BaseUrl
	 * @return string The base url
	 */
	protected function generateBaseUrl()
	{
		$filename = basename($this->server->get('SCRIPT_FILENAME'));
		
		if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
			$baseUrl = $this->server->get('SCRIPT_NAME');
		} elseif (basename($this->server->get('PHP_SELF')) === $filename) {
			$baseUrl = $this->server->get('PHP_SELF');
		} elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
			$baseUrl = $this->server->get('ORIG_SCRIPT_NAME');
        } else {			
        	$path = $this->server->get('PHP_SELF', '');
        	$file = $this->server->get('SCRIPT_FILENAME', '');

        	$parts = array_reverse(explode('/', trim($file, '/')));

        	$i = 0;

        	$prev = count($parts);

        	$baseUrl = '';

        	do {
        		$part = $parts[$i];
        		$baseUrl = '/' . $part . $baseUrl;
        		$i++;
        	} while (($prev > $i) && (($pos = strpos($path, $baseUrl)) !== false) && (0 !== $pos));
		}

		$uri = $this->getBaseUri();

		if ($baseUrl && false !== $pre = self::getPrefixUrlEncoded($uri, $baseUrl)) return $pre;

		if ($baseUrl && false !== $pre = self::getPrefixUrlEncoded($uri, dirname($baseUrl))) return rtrim($prefix, '/');

		if (($pos = strpos($uri, '?')) !== false) {
			$queryLessUri = substr($uri, 0, $pos);
		} else {
			$queryLessUri = $uri;
		}

		$base = basename($baseUrl);

		if (empty($base) || !strpos(rawurldecode($queryLessUri), $base)) return '';
		



		return rtrim($baseUrl, '/');

	}

	protected function generateBasePath()
	{
		$filename = basename($this->server->get('SCRIPT_FILENAME'));
		$baseUrl = $this->getBaseUrl();

		if (empty($baseUrl)) return '';

		$basePath = (basename($baseUrl) === $filename) ? dirname($baseUrl) : $baseUrl;

		if ('\\' === DIRECTORY_SEPARATOR) { // We are on Windows
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
	}

	protected function generatePath()
	{
		$baseUrl = $this->getBaseUrl();

		if (($baseUri = $this->getBaseUri()) === null) return '/';

		$path = '/';

		if ($pos = strpos($baseUri, '?')) $baseUri = substr($baseUri, 0, $pos);

		if ((null !== $baseUrl) && (false === ($path = substr($baseUri, strlen($baseUrl))))) {
			return '/';
		} elseif ($baseUrl === null) {
			return $baseUri;
		}

		return $path;
	}

	/**
	 * Returns the string prefix if it' s the string' s prefix (URL Encoded)
	 * false otherwise
	 * @param  string $str 		The urlencoded string
	 * @param  string $pre 		The NOT encoeded prefix
	 * @return string|false		The prefix as it is encoded in $str or false
	 */
	protected static function getPrefixUrlEncoded($str, $pre)
	{
		if (strpos(rawurldecode($str), $pre) !== 0) return false;

		$length = strlen($pre);

		/*
		NOTE! THIS REGEX is COPIED from the ZEND Framework (BSD License)
		 */
		if (preg_match("#^(%[[:xdigit:]]{2}|.){{$length}}#", $str, $match)) return $match[0];

		return false;
	}

	

}