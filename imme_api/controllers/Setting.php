<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'accounts', 'settings', 'customers'));
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	public function pin_1() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('pin_1', 'PIN 1', 'required')
			->set_rules('pin_2', 'PIN 2', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$pin_1			= md5($this->input->post('pin_1'));
			$pin_2			= md5($this->input->post('pin_2'));
		} else {
			$this->_error('123', 'Change PIN 1 failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->select_by_customer_id($login_data[0]->customer_id);
		if ($account_data[0]->pin_2 != $pin_2) 
		{
			$this->_error('127', 'Wrong PIN 2');
		}

		$account['pin_1'] = $pin_1;
		$this->accounts->update_by_id($account_data[0]->account_id, $account);
		
		$feedback['error'] 		= false;
		$feedback['message']	= "Change success";

		$this->_feedback($feedback);
	}

	public function pin_2() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('pin_2_new', 'PIN 2 New', 'required')
			->set_rules('pin_2', 'PIN 2', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$pin_2_new		= md5($this->input->post('pin_2_new'));
			$pin_2			= md5($this->input->post('pin_2'));
		} else {
			$this->_error('124', 'Change PIN 2 failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->select_by_customer_id($login_data[0]->customer_id);
		if ($account_data[0]->pin_2 != $pin_2) 
		{
			$this->_error('127', 'Wrong PIN 2');
		}

		$account['pin_2'] = $pin_2_new;
		$this->accounts->update_by_id($account_data[0]->account_id, $account);
		
		$feedback['error'] 		= false;
		$feedback['message']	= "Change success";

		$this->_feedback($feedback);
	}

	public function track() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('track_transaction', 'Track GPS', 'required')
			->set_rules('pin_2', 'PIN 2', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$track_transaction	= $this->input->post('track_transaction');
			$pin_2				= md5($this->input->post('pin_2'));
		} else {
			$this->_error('125', 'Change track transaction failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->select_by_customer_id($login_data[0]->customer_id);
		if ($account_data[0]->pin_2 != $pin_2) 
		{
			$this->_error('127', 'Wrong PIN 2');
		}

		$setting['track_transaction'] = $track_transaction;
		$this->settings->update_by_customer_id($account_data[0]->customer_id, $setting);
		
		$feedback['error'] 		= false;
		$feedback['message']	= "Change success";

		$this->_feedback($feedback);
	}

	public function color() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('color_security', 'Track GPS', 'required')
			->set_rules('pin_2', 'PIN 2', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$color_security	= $this->input->post('color_security');
			$pin_2				= md5($this->input->post('pin_2'));
		} else {
			$this->_error('126', 'Change color security failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->select_by_customer_id($login_data[0]->customer_id);
		if ($account_data[0]->pin_2 != $pin_2) 
		{
			$this->_error('127', 'Wrong PIN 2');
		}

		$setting['color_security'] = $color_security;
		$this->settings->update_by_customer_id($account_data[0]->customer_id, $setting);
		
		$feedback['error'] 		= false;
		$feedback['message']	= "Change success";

		$this->_feedback($feedback);
	}

	public function password() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('password', 'Password', 'required')
			->set_rules('new_password', 'New Password', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$password		= md5($this->input->post('password'));
			$new_password	= md5($this->input->post('new_password'));
		} else {
			$this->_error('-', 'Change password failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$customer_data = $this->customers->select_by_customer_id($login_data[0]->customer_id);
		if ($customer_data[0]->password != md5($password)) 
		{
			$this->_error('-', 'Wrong Password');
		}

		$customer['password'] = md5($new_password);
		$this->customers->update_by_id($customer_data[0]->customer_id, $customer);
		
		$feedback['error'] 		= false;
		$feedback['message']	= "Change password success";

		$this->_feedback($feedback);
	}

	public function get_account_setting() {
		$this->load->library('secure');
		//$this->load->model(array('customers', 'accounts'));

		$api_param = array();
		$data = $this->secure->auth_account($api_param);
		
		$customer_data	= $this->customers->select_by_id($data['login_data']->customer_id);
		$account_data	= $this->accounts->select_by_id($data['login_data']->account_id);
		if ($customer_data[0]->idcard_type_id) {
			$this->load->model('idcard_types');
			$idcard_type_data = $this->idcard_types->select_by_id($customer_data[0]->customer_id);
			$idcard_type = $idcard_type_data[0]->idcard_type_name;
		} else {
			$idcard_type = "-";
		}

		if ($customer_data[0]->idcard_number) {
			$idcard_number = $customer_data[0]->idcard_number;
		} else {
			$idcard_number = "-";
		}
		
		$feedback['error'] 					= false;
		$feedback['data']['account_number']	= $account_data[0]->account_number;
		$feedback['data']['full_name']		= $customer_data[0]->full_name;
		$feedback['data']['email']			= $customer_data[0]->email;
		$feedback['data']['phone_number']	= $customer_data[0]->phone_number;
		$feedback['data']['idcard_number']	= $idcard_number;
		$feedback['data']['idcard_type']	= $idcard_type;
		$feedback['data']['verified_email']	= ($customer_data[0]->is_email_verified == '1')? true : false;
		$feedback['data']['verified_phone']	= ($customer_data[0]->is_phone_verified == '1')? true : false;

		$this->_feedback($feedback);
	}

	public function save_account_setting() {
		$this->load->library('secure');
		//$this->load->model(array('customers', 'accounts'));

		$api_param = array(
			'full_name' => 'Full Name',
			'email' => 'Email',
			'phone_number' => 'Phone Number',
			'pin2' => 'PIN 2' );
		$data = $this->secure->auth_account($api_param);

		$account_data = $this->accounts->select_by_id($data['login_data']->account_id);
		if ($account_data[0]->pin_2 != md5($data['pin2'])) {
			$this->_error('-', 'Wrong PIN 2');
		}
		
		$customers['full_name']		= $data['full_name'];
		$customers['email']			= $data['email'];
		$customers['phone_number']	= $data['phone_number'];
		$this->customers->update_by_id($data['login_data']->customer_id, $customers);
		

		$feedback['error'] 				= false;
		$feedback['data']['message']	= "Success saving your account";

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
