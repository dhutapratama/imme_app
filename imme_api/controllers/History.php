<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'transactions', 'customers', 'merchants'));
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	public function transaction() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$message		= $this->input->post('message');
		} else {
			$this->_error('', 'Get transaction history failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$feedback['error'] 		= false;
		$i = 0;

		// Get all data transaction
		$transactions_data = $this->transactions->select_last_by_customer_id($login_data[0]->customer_id);
		foreach ($transactions_data as $value) {
			// Get name data
			$reference = $this->transactions->select_vs_by_transaction_reference($value->transaction_reference, $login_data[0]->customer_id);
			if ($reference) {
				if ($reference[0]->merchant_id == "") {
				// User
				$name = $this->customers->select_by_id($reference[0]->customer_id);
				$feedback['transactions'][$i]['name'] 		= $name[0]->full_name;
				} else {
					// Merchant
					$name = $this->merchants->select_by_id($reference[0]->merchant_id);
					$feedback['transactions'][$i]['name'] 		= $name[0]->merchant_name;
				}
			} else {
				$feedback['transactions'][$i]['name'] 		= "Unknown";
			}
			
			$feedback['transactions'][$i]['reference']	= $value->transaction_reference;
			$feedback['transactions'][$i]['amount'] 	= number_format($value->amount, 0, ",", ".");
			$feedback['transactions'][$i]['type']		= $value->transaction_type_id;
			$feedback['transactions'][$i]['balance']	= number_format($value->balance, 0, ",", ".");
			$feedback['transactions'][$i]['date']		= date("d M Y H:i", strtotime($value->transaction_date));
			$feedback['transactions'][$i]['status']		= $value->transaction_status_id;
			$i++;
		}

		$this->_feedback($feedback);
	}

	public function transaction_detail() {
		$this->load->library('secure');
		$this->load->model(array('transaction_types'));

		$api_param = array('reference' => 'Reference Number');
		$data = $this->secure->auth_account($api_param);

		$transaction 	= $this->transactions->select_vs_by_transaction_reference($data['reference'], $data['login_data']->customer_id);
		if (!$transaction) {
			$this->_error('-', 'This is not your transaction');
		}

		if ($transaction[0]->merchant_id == "") {
			// User
			$name = $this->customers->select_by_id($transaction[0]->customer_id);
			$feedback['data']['name'] 			= $name[0]->full_name;
			$feedback['data']['picture_url'] 	= $name[0]->picture_url;
		} else {
			// Merchant
			$name = $this->merchants->select_by_id($transaction[0]->merchant_id);
			$feedback['data']['name'] 			= $name[0]->merchant_name;
			$feedback['data']['picture_url'] 	= $name[0]->picture_url;
		}

		$transaction_type 	= $this->transaction_types->select_by_id($transaction[0]->transaction_type_id);

		$feedback['error'] 				= false;
		$feedback['data']['reference']	= $transaction[0]->transaction_reference;
		$feedback['data']['amount'] 	= "Rp" . number_format($transaction[0]->amount, 0, ",", ".");
		$feedback['data']['type']		= $transaction_type[0]->transaction_type_name;
		$feedback['data']['balance']	= "Rp" . number_format($transaction[0]->balance, 0, ",", ".");
		$feedback['data']['date']		= date("d M Y H:i", strtotime($transaction[0]->transaction_date));
		$feedback['data']['status']		= $transaction[0]->transaction_status_id;

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
