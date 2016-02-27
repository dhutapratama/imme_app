<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('blog_url'))
{
	/**
	 * Base URL
	 *
	 * Create a blog URL based on your blogpath.
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 *
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function blog_url($uri = '', $protocol = NULL)
	{
		return get_instance()->config->blog_url($uri, $protocol);
	}
}