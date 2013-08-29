<?php namespace Modulework\Modules\Http\Exceptions;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use Exception;

/**
* HttpNotFoundException
* Should be thrown if a 404 should be send to the browser
*/
class HttpNotFoundException extends Exception implements HttpExceptionInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct($code = null, $message = null, Exception $previous = null)
	{
		$code = $code ?: 404;
		parent::__construct($message, $code, $previous);
	}

	public function getStatusCode()
	{
		return $this->code;
	}
}