<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use Modulework\Modules\Http\Cookie;
use Modulework\Modules\Http\Request;
use Modulework\Modules\Http\Utilities\ArrayCase;
use Modulework\Modules\Http\Utilities\HeaderCase;
use Modulework\Modules\Http\Utilities\HeaderWrapper;
use Modulework\Modules\Http\Utilities\HeaderWrapperInterface;


/**
* Response
* This class should represent the HTTP response,
* done by the application.
*/
class Response
{

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
	 * The HTTP status Text
	 * @var string
	 */
	protected $statusText;

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
	 * @var \Modulework\Modules\Http\Utilities\ArrayCase
	 */
	public $cookies;

	/**
	 * The HeaderWrapper
	 * @var \Modulework\Modules\Http\Utilities\HeaderWrapperInterface
	 */
	protected $headerWrapper;

	/**
	 * Factory for the Response object
	 * @param  string  $content The body of the HTTP response
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\Response The new Response object
	 *
	 * @throws \InvalidArgumentException (from Constructor)
	 */
	public static function make($content = '', $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		return new static($content, $code, $headers, $headerWrapper);
	}

	/**
	 * Constructor.
	 * @param  string  $content The body of the HTTP response
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\Response The new Response object
	 *
	 * @throws \InvalidArgumentException (from setContent)
	 */
	public function __construct($content = '', $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		$this->setStatusCode($code);
		$this->setContent($content);
		
		$this->headers = new HeaderCase($headers);
		$this->cookies = new ArrayCase();

		if (!$this->headers->has('Date')) {
			$this->setDate(new DateTime(null, new DateTimeZone('UTC')));
		}

		$this->setHeaderWrapper($headerWrapper);
	}

	/**
	 * PHP' s magic method __toString
	 * Format:
	 * HTTP/{VERSION} {STATUSCODE} {STATUSTEXT}
	 * {HEADERS}
	 * {BODY}
	 * 
	 * @return string The response as string
	 */
	public function __toString()
	{
		return
		sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->statusCode, $this->statusText) . "\r\n" .
		$this->headers->showForResponse() . "\r\n" .
		$this->getContent();
	}

	/**
	 * Dependency injection for the HeaderWrapper (also availbe thru the constructor)
	 * If null is passed it will create a new instance of \Modulework\Modules\Http\Utilities\HeaderWrapper
	 *
	 * It returns the "previous" HeaderWrapper or null
	 * 
	 * @param \Modulework\Modules\Http\Utilities\HeaderWrapperInterface $headerWrapper The HeaderWrapper
	 *
	 * @return \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null 	"previous" HeaderWrapper | null
	 */
	public function setHeaderWrapper(HeaderWrapperInterface $headerWrapper = null)
	{
		if ($this->headerWrapper !== null) {
			$ret = $this->headerWrapper;
		} else {
			$ret = null;
		}

		if ($headerWrapper === null) {
			$this->headerWrapper = new HeaderWrapper;
		} else {
			$this->headerWrapper = $headerWrapper;
		}

		return $ret;
	}

	/**
	 * Send the headers and cookies to the client
	 * @uses sendCookies
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function sendHeaders()
	{
		if ($this->headerWrapper->headers_sent()) {
			return $this;
		}

		$this->headerWrapper->header(sprintf('HTTP/%s %s %s', $this->getProtocolVersion(), $this->statusCode, $this->statusText));

		foreach ($this->headers->all() as $name => $value) {
			$this->headerWrapper->header($name . ': ' . $value);
		}

		$this->sendCookies();

		return $this;

	}

	/**
	 * Send the cookies only to the client
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function sendCookies()
	{
		if ($this->headerWrapper->headers_sent()) {
			return $this;
		}

		foreach ($this->cookies->all() as $cookie) {
			$this->headerWrapper->setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
		}
		return $this;
	}

	/**
	 * Sends the content to the client
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function sendContent()
	{
		echo $this->content;

		return $this;
	}

	/**
	 * Send the response to the client (headers, cookies, content)
	 * @uses sendHeaders
	 * @uses sendContent
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function send()
	{
		$this->sendHeaders();
		$this->sendContent();

		return $this;
	}

	/**
	 * Prepares the response based on the Request object
	 * This method is not essential and can be cut out of the chain.
	 * However it cleans up the headers and does some other stuff under the hood.
	 * @param  Request $req The request object
	 * 
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function prepare(Request $request)
	{

		// This method tries may cause some issues, if 200 is REQUIRED even when it' s
		// redirect response. If you want to change the status code just call it AFTER
		// this method:
		// e. g. [...]->prepare()->setStatusCode(301)[...]
		if ($this->statusCode === 200 && $this->headers->has('Location')) {
			$this->setStatusCode('302');
		}

		if ($request->isMethod('HEAD')) {
			$this->content = null;
		}

		if ('1.0' == $this->getProtocolVersion() && 'no-cache' == $this->headers->get('Cache-Control')) {
			$this->headers->set('pragma', 'no-cache');
			$this->headers->set('expires', -1);
		}

		return $this;
	}

	/**
	 * Add a cookie to the response
	 * @param Cookie $cookie The cookie object
	 */
	public function addCookie(Cookie $cookie)
	{
		$this->cookies->push($cookie);
	}

	/**
	 * Add a header to the response
	 * @param string  $name      The name of the header (e.g. "Location")
	 * @param string  $value     The value of the header (e.g. "foo.bar")
	 * @param boolean $overwrite Whether it should replaces existing headers
	 */
	public function addHeader($name, $value, $overwrite = false)
	{
		$this->headers->set($name, $value, $overwrite);
	}

	/**
	 * Set the status code of the response
	 * If the text is null it will try to determine the text from the internal lib
	 * @param integer $code The status code
	 * @param string  $txt  The status text
	 * @return \Modulework\Modules\Http\Response THIS
	 */
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

	/**
	 * Returns the status code of this response
	 * @return integer The status code
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * Set the content for this response
	 * @param string $content The content
	 * @return \Modulework\Modules\Http\Response THIS
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setContent($content = '')
	{
		if (!$this->validateContent($content)) {
			throw new InvalidArgumentException('The Response content must be a string or an object implementing __toString() magic method, "' . gettype($content) . '" given.');
		}

		$this->content = (string) $content;
		
		return $this;
	}

	/**
	 * Append to the content for this response
	 * @param string $content The content to append
	 * @return \Modulework\Modules\Http\Response THIS
	 *
	 * @throws \InvalidArgumentException
	 */
	public function appendContent($content = '')
	{
		if (!$this->validateContent($content)) {
			throw new InvalidArgumentException('The Response content must be a string or an object implementing __toString() magic method, "' . gettype($content) . '" given.');
		}

		if ($this->content === '') {
			return $this->setContent($content);
		}

		$this->content .= (string) $content;
		
		return $this;
	}
	/**
	 * Returns the content of this response
	 * @return string          The content
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Set the date for this request
	 * @param DateTime $date The DateTime object
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function setDate(DateTime $date)
	{
		$this->headers->set('Date', $date->format('D, d M Y H:i:s') . ' GMT');
		return $this;
	}

	/**
	 * Returns the date of this response
	 * @return string The Date
	 */
	public function getDate()
	{
		$default = new DateTime();
		$default = $default->format('D, d M Y H:i:s') . 'GMT';

		return $this->headers->get('Date', $default);
	}

	/**
	 * Set the HTTP protocol version for this response
	 * @param string $version The HTTP protocol version
	 * @return \Modulework\Modules\Http\Response THIS
	 */
	public function setProtocolVersion($version = '1.0')
	{
		$this->protocolVersion = $version;
		return $this;
	}

	/**
	 * Returns the HTTP protocol version of this response
	 * @return string The HTTP protocol version
	 */
	public function getProtocolVersion()
	{
		return $this->protocolVersion;
	}

	public function isSuccess()
	{
		return ($this->statusCode >= 200 && $this->statusCode < 300);
	}

	public function isOk()
	{
		return (200 === $this->statusCode);
	}

	public function isNotFound()
	{
		return (404 === $this->statusCode);
	}

	public function isForbidden()
	{
		return (403 === $this->statusCode);
	}

	public function isRedirect()
	{
		return ($this->headers->has('Location') && ($this->statusCode >= 300 && $this->statusCode < 400));
	}

	public function isClientError()
	{
		return ($this->statusCode >= 400 && $this->statusCode < 500);
	}

	public function isServerError()
	{
		return ($this->statusCode >= 500 && $this->statusCode < 600);
	}

	public function isEmpty()
	{
		return in_array($this->statusCode, array(201, 204, 304));
	}

	public function isInvalid()
	{
		return ($this->statusCode < 100 || $this->statusCode >= 600);
	}

	public function isInformational()
	{
		return ($this->statusCode >= 100 && $this->statusCode < 200);
	}

	/**
	 * Validate the content
	 * Allowed types:
	 * - string
	 * - integers
	 * - objects implementing __toString()
	 * 
	 * @param  mixed $content The content to check
	 * @return bool          Whether the content is valid
	 */
	protected static function validateContent($content)
	{
		if (!is_string($content) && !is_numeric($content) && !is_callable(array($content, '__toString'))) {
			return false;
		}

		return true;
	}




}