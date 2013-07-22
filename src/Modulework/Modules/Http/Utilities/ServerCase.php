<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

/**
 * {@inheritdoc}
 * Adds:
 * - getHeaders()
 */
class ServerCase extends ArrayCase {

	/**
	 * Returns the HTTP headers
	 * @return array The HTTP headers
	 */
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