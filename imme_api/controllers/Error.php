<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends CI_Controller {
	public function index()
	{
		$this->_error('114', 'Error request url', FALSE);
	}

	private function _error($code = '100', $message = 'Unknown error', $csrf = TRUE)
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		if($csrf) {
			$json['csrf_token']	= $this->security->get_csrf_hash();
		}
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
	}
}
