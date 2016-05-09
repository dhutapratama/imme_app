<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth {

	public function __construct()
	{
		$CI =& get_instance();
	}

	function login_key() {
		$CI =& get_instance();
        $CI->load->model('login');
        
		$login_data = $CI->login->get_by_login_key($CI->input->post('login_key'));
		if (!$login_data) {
			$CI->write->error('Your session was expired');	
		}
		return $login_data;
	}

	function input($input = array()) {
		$CI =& get_instance();
		$CI->load->library('form_validation');

		foreach ($input as $key => $value) {
			$CI->form_validation->set_rules($key, '', $value);
			$post_data[$key]	= $CI->input->post($key);
		}

		if (!$CI->form_validation->run()) {
			$CI->write->error('There some missing input');
			exit();
		}

		return $post_data;
	}

	function input_v2() {
		$json = file_get_contents('php://input');
		return json_decode($json);
	}

	function login_key_v2() {
		$CI =& get_instance();
        $CI->load->model('login');

        $input = $this->input_v2();
        
		$login_data = $CI->login->get_by_login_key($input->login_key);
		if (!$login_data) {
			$CI->write->error('Your session was expired');	
		}
		return $login_data;
	}

	//----------------------- Merchants -----------------------//
	function merchant_key() {
		$CI =& get_instance();
        $CI->load->model('merchants');
        
		$merchants_data = $CI->merchants->get_by_merchant_key($CI->input->post('merchant_key'));
		if (!$merchants_data) {
			$CI->write->error('Merchant key is not valid');
			exit();
		}
		return $merchants_data;
	}
}