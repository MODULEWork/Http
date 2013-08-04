<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use stdClass;
use InvalidArgumentException;
use Modulework\Modules\Http\Utilities\HeaderWrapperInterface;
/**
* JSON-Response
* A HTTP Response in the JSON format (application/json)
*/
class JsonResponse extends Response
{
	const CONTENTTYPE_JS = 'application/javascript';
	const CONTENTTYPE_JSON = 'application/json';
	protected $callback;
	protected $json;
	protected $rawdata;

	/**
	 * Factory for the Response object
	 * @param  mixed  $content 	The data which gets encoded to json
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\JsonResponse The new JsonResponse object
	 *
	 * @throws InvalidArgumentException (from Constructor)
	 */
	public static function make($json = null, $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		return new static($json, $code, $headers, $headerWrapper);
	}

	/**
	 * Constructor.
	 * @param  mixed   $content The data which gets encoded to json
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\JsonResponse The new JsonResponse object
	 *
	 * @throws InvalidArgumentException (from setContent)
	 */
	public function __construct($json = null, $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		parent::__construct('', $code, $headers, $headerWrapper);

		if ($json === null) {
			$json = new stdClass;
		}

		$this->setJson($json);
	}

	/**
	 * Set the json data for the response
	 * @param mixed   $json        The data (a JSON string is possible as well) which gets encoded
	 * @param boolean $json_string Whether the input doesn' t need to get encoded
	 *
	 * @return \Modulework\Modules\Http\JsonResponse THIS
	 */
	public function setJson($json = array(), $json_string = false)
	{
		$this->rawdata = $json;
		$this->json = ($json_string) ? $json : json_encode($json);

		return $this->refresh();
	}

	/**
	 * Returns the json data
	 * @param  boolean $raw Whether it shoud return the raw or encoded data
	 * 
	 * @return mixed|string The json data
	 */
	public function getJson($raw = false)
	{
		return ($raw) ? $this->rawdata : $this->json;
	}

	/**
	 * Set the json callback for the response
	 * @param string $callback The callback
	 *
	 * @return \Modulework\Modules\Http\JsonResponse THIS
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setCallback($callback = null)
	{
		if ($callback !== null) {
			if (!self::isValidIdentifier($callback)) {
				throw new InvalidArgumentException('This identifier is not valid!');
			}
		}

		$this->callback = $callback;

		return $this->refresh();
	}

	/**
	 * Returns the callback
	 * @return string The callback
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Updates all parameters (changing headers and setting content)
	 *
	 * @return \Modulework\Modules\Http\JsonResponse THIS
	 */
	protected function refresh()
	{
		if ($this->callback !== null) {
			$this->headers->set('Content-Type', self::CONTENTTYPE_JS);

			// This will produce "CALLBACK(JSON);"
			return $this->setContent($this->callback . '(' .  $this->json . ');');
		}

		if (!$this->headers->has('Content-Type') || $this->headers->get('Content-Type') === self::CONTENTTYPE_JS) {
			$this->headers->set('Content-Type', self::CONTENTTYPE_JSON);
		}

		return $this->setContent($this->json);
	}

	/**
	 * Checks if the string is a valid javascript identifier
	 * Taken from http://www.geekality.net/2011/08/03/valid-javascript-identifier
	 * Formatting modified + minor changes
	 * @param  string  $callback The string to check
	 * @return boolean          Whether the string is valid
	 */
	protected static function isValidIdentifier($callback)
	{
		$identifierSyntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
		$reservedWords = array(
			'break',
			'do',
			'instanceof',
			'typeof',
			'case',
			'else',
			'new',
			'var',
			'catch',
			'finally',
			'return',
			'void',
			'continue',
			'for',
			'switch',
			'while',
			'debugger',
			'function',
			'this',
			'with',
			'default',
			'if',
			'throw',
			'delete',
			'in',
			'try',
			'class',
			'enum',
			'extends',
			'super',
			'const',
			'export',
			'import',
			'implements',
			'let',
			'private',
			'public',
			'yield',
			'interface',
			'package',
			'protected',
			'static',
			'null',
			'true',
			'false'
			);

		return (preg_match($identifierSyntax, $callback) && !in_array(mb_strtolower($callback, 'UTF-8'), $reservedWords));
	}
}