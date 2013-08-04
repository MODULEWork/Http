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

	public static function make($json = null, $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		return new static($json, $code, $headers, $headerWrapper);
	}

	public function __construct($json = null, $code = 200, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		parent::__construct('', $code, $headers, $headerWrapper);

		if ($json === null) {
			$json = new stdClass;
		}

		$this->setJson($json);
	}

	public function setJson($json = array())
	{
		$this->json = json_encode($json);

		return $this->refresh();
	}

	public function getJson()
	{
		return $this->json;
	}

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

	public function getCallback()
	{
		return $this->callback;
	}

	protected function refresh()
	{
		if ($this->callback !== null) {
			$this->headers->set('Content-Type', self::CONTENTTYPE_JS);

			// This will produce "CALLBACK(JSON);"
			return $this->setJson($this->callback . '(' .  $this->data . ');');
		}

		if (!$this->headers->has('Content-Type') || $this->headers->get('Content-Type') === self::CONTENTTYPE_JS) {
			$this->headers->set('Content-Type', self::CONTENTTYPE_JSON);
		}
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