<?php namespace Modulework\Modules\Http\Exceptions;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use Exception;

/**
* HttpExceptionInterface
* All HTTP releated excpetions should implement this.
*/
interface HttpExceptionInterface
{
	/**
	 * Constructor.
	 *
	 * The message can be be ommited and will be figured out by the Response class.
	 * 
	 * @param int    		$code     The http status code
	 * @param string 		$message  The messages
	 * @param \Exceptions 	$previous Previous exception (if nested)
	 */
	public function __construct($code = null, $message = null, Exception $previous = null);
	
	/**
	 * Returns the status code for this exception
	 * @return int The code
	 */
	public function getStatusCode();
}