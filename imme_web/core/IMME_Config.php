<?php defined('BASEPATH') OR exit('No direct script access allowed');

class IMME_Config extends CI_Config {
    public function blog_url($uri = '', $protocol = NULL)
	{
		$base_url = $this->slash_item('blog_url');

		if (isset($protocol))
		{
			// For protocol-relative links
			if ($protocol === '')
			{
				$base_url = substr($base_url, strpos($base_url, '//'));
			}
			else
			{
				$base_url = $protocol.substr($base_url, strpos($base_url, '://'));
			}
		}

		return $base_url.ltrim($this->_uri_string($uri), '/');
	}
}