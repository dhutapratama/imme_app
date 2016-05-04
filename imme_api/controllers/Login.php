<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Untuk login customer
 * 
 * POST Param :
 * [email]
 * [password]
 *
 * Feedback :
 * [error]
 * [message]
 * [session_key]
 * [imme_algorithm]
 * [tba_algorithm]
 * [cba_algorithm]
 * [cba_counter]
 */

class Login extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->model(array('customers', 'accounts', 'balances', 'login_sessions', 'settings', 'transactions', 'security_algorithm'));
		$this->load->config('config');
     }

	public function index()
	{
		$email		= $this->input->post('email'); // Must to be md5 on beta
		$password	= $this->input->post('password'); // Must to be md5 on beta

		if (!$email OR !$password) {
			$this->_error('117', 'Login failed');
		}

		$customer_data = $this->customers->select_by_email($email);
		if (!$customer_data) {
			$this->_error('103', 'Email not registered');
		}

		if ($customer_data[0]->password == md5($password)) {
			// Create login session
			$expired_session_date = time() + $this->config->item('csrf_expire');
			$balance_data = $this->balances->select_by_customer_id($customer_data[0]->customer_id);
			$setting_data = $this->settings->select_by_customer_id($customer_data[0]->customer_id);
			//$device_data  = $this->devices->check_user_agent($user_agent);
			$security_algorithm			= $this->security_algorithm->select_by_customer_id($customer_data[0]->customer_id);

			if (!$setting_data) {
				$this->_error("114", "Missing setting data");
			}

			$login_session['session_key']			= md5(rand(1, 9999999999));
			$login_session['start_session_date']	= date('Y-m-d H:i:s');
			$login_session['expired_session_date']	= date('Y-m-d H:i:s', $expired_session_date);
			$login_session['customer_id']			= $customer_data[0]->customer_id;
			$login_session['account_id']			= $balance_data[0]->account_id;
			$login_session['balance_id']			= $balance_data[0]->balance_id;
			$login_session['setting_id']			= $setting_data[0]->setting_id;
			//$login_session['device_id']				= $device_data[0]->device_id;
			//$login_session['device_ip']				= $this->input->post('device_ip');
			$login_session['public_ip']				= $_SERVER['REMOTE_ADDR'];
			$this->login_sessions->insert($login_session);

			$feedback['error']					= false;
			$feedback['data']['session_key']	= $login_session['session_key'];

			//$feedback['csrf_token']		= $this->security->get_csrf_hash();
			//$feedback['imme_algorithm']	= $security_algorithm[0]->imme_algorithm; // Must encrypted before send to client
			//$feedback['tba_algorithm']	= $security_algorithm[0]->tba_algorithm; // Must encrypted before send to client
			//$feedback['cba_algorithm']	= $security_algorithm[0]->cba_algorithm; // Must encrypted before send to client
			//$feedback['cba_counter']		= $security_algorithm[0]->cba_counter; // Must encrypted before send to client

			$this->_feedback($feedback);
		} else {
			$this->_error('115', 'Wrong password');
		}
	}

	function session_check() {
		$this->load->library('secure');
		//$this->load->model(array('customers', 'accounts'));

		$api_param = array();
		$data = $this->secure->auth_account($api_param);
		
		$feedback['error'] 				= false;
		$feedback['data']['message']	= "Welcome Back!";

		$this->_feedback($feedback);
	}

	private function _error($code = '100', $message = 'Unknown error')
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
	}
}
