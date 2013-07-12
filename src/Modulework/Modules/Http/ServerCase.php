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
class ServerCase extends ArrayCase {

	public function getHeaders()
	{
		$headers = array();

		foreach($this->array as $header => $content) {
			if (0 === strpos($header, 'HTTP_')) {
				$this->set(substr($header, 5), $content);
			}
		}


		return $headers;
	}
}