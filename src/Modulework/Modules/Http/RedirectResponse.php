<?php namespace Modulework\Modules\Http;
/*
 * (c) Christian Gärtner <christiangaertner.film@googlemail.com>
 * This file is part of the Modulework Framework
 * License: View distributed LICENSE file
 */

use InvalidArgumentException;
use Modulework\Modules\Http\Utilities\HeaderWrapperInterface;

/**
* Redirect-Response
* A HTTP header redirect
*/
class RedirectResponse extends Response
{


	/**
	 * Factory for the Response object
	 * @param  string  $content The target URL
	 * @param  integer $code    The HTTP status code (302 = default)
	 * @param  array   $headers The HTTP headers (Location header is done automagical)
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\JsonResponse The new RedirectResponse object
	 *
	 * @throws InvalidArgumentException (from Constructor)
	 */
	public static function make($url, $code = 302, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
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
	 * @return \Modulework\Modules\Http\JsonResponse The new RedirectResponse object
	 *
	 * @throws InvalidArgumentException (from setContent)
	 */
	public function __construct($url = null, $code = 302, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		if ($url === '' || $url === null) throw new InvalidArgumentException('URL cannot be empty.');

		parent::__construct('', $code, $headers, $headerWrapper);

		$this->setUrl($url);

		if (!$this->isRedirect()) {
            throw new InvalidArgumentException('HTTP status code not compatible with a redirect');
        }
	}


}