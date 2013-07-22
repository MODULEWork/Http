<?php namespace Modulework\Modules\Http\Utilities;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */


/**
* Extends ArrayCase
* Adds:
* - getDate()
*/
class HeaderCase extends ArrayCase {

	/**
	 * Get the header value converted to a date
	 * @param  string 			$key     	The header name
	 * @param  \DateTime 		$default 	The default value
	 * @return null|\DateTime          		The parsed DateTime or default
	 *
	 * @throws \RuntimeExecptopm when header not parseable by \DateTime
	 */
	public function getDate($key, \DateTime $default = null)
	{
		if (null === $val = $this->get($key)) return $default;
		if (false === $date = \DateTime::createFromFormat(DATE_RFC2822, $val)) throw new \RuntimeException(sprintf('The %s HTTP header is not parseable (%s).', $key, $val));
		return $date;
	}
}