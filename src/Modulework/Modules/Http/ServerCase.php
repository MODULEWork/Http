<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

/**
* Extends ArrayCase
* Adds:
* - getHeaders()
*/
class ServerCase extends ArrayCase {

	public function getHeaders()
	{
		$headers = array();

		foreach($this->array as $header => $content) {
			if (0 === strpos($header, 'HTTP_')) {
				$headers[substr($header, 5)] = $content;
			}
		}

		return $headers;
	}
}