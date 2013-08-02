<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use DateTime;
use DateTimeZone;
use Modulework\Modules\Http\Cookie;
use Modulework\Modules\Http\Utilities\ArrayCase;
use Modulework\Modules\Http\Utilities\HeaderCase;


/**
* Response
* This class should represent the HTTP response,
* done by the application.
*/
class Response {

	/**
	 * The main content
	 * @var string
	 */
	protected $content;

	/**
	 * The HTTP status code
	 * @var integer
	 */
	protected $statusCode;

	/**
	 * The charset (header)
	 * @var string
	 */
	protected $charset;

	/**
	 * The HTTP protocol version
	 * @var string
	 */
	protected $protocolVersion = '1.0';

	/**
	 * The status code registry
	 * List according to {@link http://www.iana.org/assignments/http-status-codes/http-status-codes.txt}
	 * @var array
	 */
	public static $statusCodeRegistry = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		306 => 'Reserved',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large ',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency ',
		425 => 'Unassigned',
		426 => 'Upgrade Required',
		427 => 'Unassigned',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		430 => 'Unassigned',
		431 => 'Request Header Fields Too Large ',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates (Experimental)',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Unassigned',
		510 => 'Not Extended',
		511 => 'Network Authentication Required'
		);
	
	/**
	 * The headers to get sent
	 * @var \Modulework\Modules\Http\Utilities\ArrayCase
	 */
	public $headers;

	/**
	 * The cookies to get sent
	 * @var \Modulework\Modules\Http\Utilities\CookieCase
	 */
	public $cookies;

	/**
	 * Factory for the Response object
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * @param  string  $content The body of the HTTP response
	 * 
	 * @return \Modulework\Modules\Http\Response The new Request object
	 */
	public static function make($code = 200, $headers = array(), $content = '')
	{
		return new static($code, $headers, $content);
	}

	public function __construct($code = 200, $headers = array(), $content = '')
	{
		$this->setStatusCode($code);
		$this->setContent($content);
		
		$this->headers = new HeaderCase($headers);
		$this->cookies = new ArrayCase();

		if (!$this->headers->has('Date')) {
			$this->setDate(new DateTime(null, new DateTimeZone('UTC')));
		}
	}

	public function __toString()
	{
		return
		sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->statusCode, $this->statusText) . "\r\n" .
		$this->headers->showForResponse() . "\r\n" .
		$this->getContent();
	}


	public function sendHeaders()
	{
		if (headers_sent()) {
			return $this;
		}

		header(sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->statusCode, $this->statusText));

		foreach ($this->headers->all() as $name => $values) {
			foreach ($values as $value) {
				header($name . ': ' . $value, false);
			}
		}

		$this->sendCookies();

		return $this;

	}

	public function sendCookies()
	{
		if (headers_sent()) {
			return $this;
		}

		foreach ($this->cookies->all() as $cookie) {
			setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
		}
	}

	public function addCookie()
	{
		
	}

	public function setStatusCode($code = 200, $txt = null)
	{
		$this->statusCode = $code;

		if ($txt === null) {
			$this->statusText = isset(self::$statusCodeRegistry[$code]) ? self::$statusCodeRegistry[$code] : '';
			return $this;
		}

		if ($txt === false) {
			$this->statusText = '';
			return $this;
		}

		$this->statusText = $txt;

		return $this;
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function setContent($content = '')
	{
		$this->content = $content;
	}

	public function getContent($content = '')
	{
		return $this->content;
	}

	public function setDate(DateTime $date)
	{
		$this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');
	}

	public function getDate()
	{
		 return $this->headers->get('Date', new \DateTime());
	}

	public function setProtocolVersion($version = '1.0')
	{
		$this->protocolVersion = $version;
	}

	public function getProtocolVersion()
	{
		return $this->protocolVersion;
	}




}