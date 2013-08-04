<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use stdClass;

/**
* JSON-Response
* A HTTP Response in the JSON format (application/json)
*/
class JsonResponse extends Response
{
	protected $callback;
	protected $json;

	public static function make($json = null, $code = 200, array $headers = array())
	{
		return new static($json, $code, $headers);
	}
	
	public function __construct($json = null, $code = 200, array $headers = array())
	{
		parent::__construct('', $code, $headers);

		if ($json === null) {
			$json = new stdClass;
		}

		$this->setJson($json);
	}
}