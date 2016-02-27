<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	public function index()
	{
		/*
		if ($this->input->get('request_code') == '1000') {
			// Acknowledgement
			$this->load->model('apps_version');
			$this->load->model('devices');
			$this->_not_logged_acknowledgement();
		} else {
			switch ($this->input->post('request_code')) {
				case '1001':
					// Customer Registration
				break;

				case '1002':
					// Customer Login
				break;

				case '1004':
					// Customer Logout
					$this->load->model(array('login_sessions'));
					$this->_logout();
				break;

				case '1005':
					// Request Money
				break;

				case '1006':
					// Check Transaction Session
				break;

				case '1007':
					// Send Money
					$this->load->model(array('login_sessions', 'security_algorithm', 'transaction_sessions'));
					$security = $this->_security_transaction_check();
					$this->_receive($security);
				break;
				
				case '9898':
					$this->load->model(array('login_sessions', 'security_algorithm', 'transaction_sessions', 'balances', 'transaction_pending', 'transactions', 'accounts'));
					//$security = $this->_security_transaction_check();
					//$this->_testing($security);
					$this->_testing();
				break;

				default:
					$this->_error('104', 'Error request code');
					break;
			}
		}
		*/
		$this->load->helper('url');
		redirect("http://imme.asia/", 'refresh', 302);
	}


	private function _testing() {
		echo $this->secure->cba_decode();
	}

	private function _create_algorithm_test() {
		$session_key = $this->input->post('session_key');

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
			exit();
		}

		$algorithm = $this->security_algorithm->select_by_customer_id($login_data[0]->customer_id);
		
		$cba_chalange = $algorithm[0]->cba_counter;

		$feedback['tba_key'] = $this->secure->tba_encode($algorithm[0]->tba_algorithm);
		$feedback['cba_key'] = $this->secure->cba_encode($cba_chalange, $algorithm[0]->cba_algorithm);

		$feedback['tba_key'] = $this->secure->imme_encode($feedback['tba_key'], $algorithm[0]->imme_algorithm);
		$feedback['cba_key'] = $this->secure->imme_encode($feedback['cba_key'], $algorithm[0]->imme_algorithm);
		$this->_feedback($feedback);
	}

	private function _security_transaction_check() {
		$session_key = $this->input->post('session_key');
		$tba_key = $this->input->post('tba_key');
		$cba_key = $this->input->post('cba_key');

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
			exit();
		}

		$algorithm = $this->security_algorithm->select_by_customer_id($login_data[0]->customer_id);

		$tba_value	= $this->secure->tba_decode($tba_key, $algorithm[0]->tba_algorithm);
		$tba_time	= time();
		$tba_answer = $tba_time - $algorithm[0]->tba_diff;
		$tba_result	= FALSE;

		for ($i = $tba_answer-60 ; $i <= $tba_answer+60; $i++) { 
			if($tba_value == $i) {
				$tba_result = TRUE;
				break;
			}
		}

		$cba_value = $this->secure->cba_decode($cba_key, $algorithm[0]->cba_algorithm);
		$cba_answer = $algorithm[0]->cba_counter;
		$cba_result	= FALSE;
		for ($i = $cba_answer-1; $i <= $cba_answer+1 ; $i++) { 
			if ($cba_value == $i) {
				$cba_result	= TRUE;
				break;
			}
		}

		if ($cba_result && $tba_result) {
			$update_counter['cba_counter'] = $algorithm[0]->cba_counter + 1;
			$this->security_algorithm->update_by_id($algorithm[0]->algorithm_id, $update_counter);
			$return_value['imme_algorithm'] = $algorithm[0]->cba_algorithm;
			return $return_value;
		} else {
			$this->_error('109', 'Error key'.'(TBA:'.$tba_answer.':'.$tba_value.')'.'('.$cba_answer.':'.$cba_value.')');
			exit();
		}
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
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

	/**
	 * Untuk meregistrasikan aplikasi dan device dengan server distributor imme
	 * 
	 * GET Param :
	 * [request_code]
	 * [device_id]
	 * [device_type]
	 * [device_ip]
	 * [date]
	 * [client_version]
	 * [authentication_code] secara default aplikasi memiliki authentication code yang di sembunyikan dan dikirim balik dalam bentuk enkripsi
	 *
	 *
	 * Feedback :
	 * [error]
	 * [hello_message]
	 * [user_agent]
	 * [csrf_token]
	 */
	private function _not_logged_acknowledgement()
	{
		$device_data['device_id']	= $this->input->get('device_id');
		$device_data['device_type']	= $this->input->get('device_type');
		$device_data['device_ip']	= $this->input->get('device_ip');
		
		$client_version			= $this->input->get('client_version');
		$authentication_code	= $this->input->get('authentication_code');
		$date					= $this->input->get('date');

		$decoded_authentication_code = $this->secure->imme_decode($authentication_code, $date."qwerty");

		// Auth Process
		$auth = $this->apps_version->check_authentication_code($client_version, $decoded_authentication_code);
		if ($auth) {
			$device['physical_device_id']	= $device_data['device_id'];
			$device['device_type']			= $device_data['device_type'];
			$device['device_ip']			= $device_data['device_ip'];
			$device['public_ip']			= $_SERVER['REMOTE_ADDR'];
			$device['client_version']		= $client_version;
			$device['device_date']			= $date;
			$device['different_date']		= time() - $date; // diff < 0 = device faster than server

			// Cek data device apakah sudah pernah teregistrasi
			$device_check = $this->devices->check_device($device['physical_device_id']);
			if ($device_check) {
				$this->devices->update_by_physical($device['physical_device_id'], $device);
				$device['user_agent'] = $device_check[0]->user_agent;
			} else {
				$agent_number = $this->devices->count_devices() + 100001;
				$device['user_agent'] = "imme".$agent_number;
				$this->devices->insert($device);
			}
			$feedback['error'] = false;
			$feedback['hello_message'] = 'Welcome to IMME';
			$feedback['user_agent'] = $device['user_agent'];
			$feedback['csrf_token'] = $this->security->get_csrf_hash();
			$this->_feedback($feedback);
		} else {
			$this->_error('101', 'Authentication new device failed', FALSE);
		}
	}

	/**
	 * Untuk logout customer
	 * 
	 * POST Param :
	 * [request_code]
	 * [csrf_token]
	 * [session_key]
	 *
	 * Feedback :
	 * [error]
	 * [logout_message]
	 * [csrf_token]
	 */
	private function _logout()
	{
		$session_key = $this->input->post('session_key');
		if ($this->login_sessions->delete_session_key($session_key)) {
			$feedback['error']			= false;
			$feedback['logout_message']	= 'You are logged out successful';
			$feedback['csrf_token']		= $this->security->get_csrf_hash();
			$this->_feedback($feedback);
		} else {
			$this->_error('106', 'Logout Error');
		}
	}

	
	private function _receive($security_data) {
	}

	
	private function _check_transaction_session() {
		
	}

	 
	private function _send($security_data = '') 
	{
		

		// FUTURE PENDING CHECK
	}
}
