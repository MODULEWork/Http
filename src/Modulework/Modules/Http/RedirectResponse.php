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

	protected $url;


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
	 * @throws \InvalidArgumentException (from Constructor)
	 */
	public static function make($url = null, $code = 302, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{
		return new static($url, $code, $headers, $headerWrapper);
	}

	/**
	 * Constructor.
	 * @param  string  $content The target URL
	 * @param  integer $code    The HTTP status code
	 * @param  array   $headers The HTTP headers
	 * 
	 * @param  \Modulework\Modules\Http\Utilities\HeaderWrapperInterface | null $headerWrapper The wrapper for PHP' s native header releated functions
	 * 
	 * @return \Modulework\Modules\Http\RedirectResponse The new RedirectResponse object
	 *
	 * @throws \InvalidArgumentException [(from setContent, setUrl)]
	 */
	public function __construct($url = null, $code = 302, array $headers = array(), HeaderWrapperInterface $headerWrapper = null)
	{

		parent::__construct('', $code, $headers, $headerWrapper);

		$this->setUrl($url);

		if (!$this->isRedirect()) {
            throw new InvalidArgumentException('HTTP status code not compatible with a redirect');
        }
	}

	/**
	 * Set the target URL
	 * @param string $url The target URL
	 *
	 * @return \Modulework\Modules\Http\RedirectResponse THIS
	 * 
	 * @throws \InvalidArgumentException [(from setContent)]
	 */
	public function setUrl($url)
	{
		if ($url === '' || $url === null) throw new InvalidArgumentException('URL cannot be empty.');
		$this->url = $url;

		// Fallback, if the header doesn' t fire!
		$this->setContent(
					'<!DOCTYPE html>
					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
							<meta http-equiv="refresh" content="1;url=' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" />

							<title>Redirect to ' . htmlspecialchars($url) . '</title>
						</head>
						<body>
							Redirecting to <a href="%1$s">%1$s</a>...
						</body>
					</html>'
		);

		$this->headers->set('Location', $url, true);

		return $this;
	}

	public function getUrl()
	{
		return $this->url;
	}


}