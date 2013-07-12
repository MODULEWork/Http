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
		// Will get replaced
		return array();
	}
}