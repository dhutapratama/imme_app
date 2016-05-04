<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Info extends CI_Controller {
	public function __construct() {
        parent::__construct();

     }

    public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	/**
	 * Untuk menerima data main page
	 * 
	 * POST Param :
	 * [session_key]
	 *
	 * Feedback :
	 */
	function get_main_data() {
		$this->load->library('secure');
		$this->load->model(array('customers', 'accounts', 'balances', 'transactions'));

		$api_param = array();
		$data = $this->secure->auth_account($api_param);

		$balance 		= $this->balances->select_by_id($data['login_data']->balance_id);
		$account 		= $this->accounts->select_by_id($data['login_data']->account_id);
		$customer		= $this->customers->select_by_id($data['login_data']->customer_id);
		$transactions 	= $this->transactions->select_3_by_customer_id($data['login_data']->customer_id);

		if ($customer[0]->is_email_verified && $customer[0]->is_phone_verified) {
			$is_verified = true;
		} else {
			$is_verified = false;
		}
		
		$feedback['error'] 						= false;
		$feedback['data']['balance']			= number_format($balance[0]->balance, 0, ",", ".");
		$feedback['data']['full_name']			= $customer[0]->full_name;
		$feedback['data']['is_verified']		= $is_verified;
		$feedback['data']['picture_url']		= $customer[0]->picture_url;

		$i = 0;
		foreach ($transactions as $value) {
			// Get name data
			$reference = $this->transactions->select_vs_by_transaction_reference($value->transaction_reference, $data['login_data']->customer_id);
			if ($reference) {
				if ($reference[0]->merchant_id == "") {
				// User
				$name = $this->customers->select_by_id($reference[0]->customer_id);
				$feedback['data']['last_transaction'][$i]['name'] 		= $name[0]->full_name;
				} else {
					// Merchant
					$name = $this->merchants->select_by_id($reference[0]->merchant_id);
					$feedback['data']['last_transaction'][$i]['name'] 		= $name[0]->merchant_name;
				}
			} else {
				$feedback['data']['last_transaction'][$i]['name'] 		= "Unknown";
			}
			
			$feedback['data']['last_transaction'][$i]['reference']	= $value->transaction_reference;
			$feedback['data']['last_transaction'][$i]['amount'] 	= "Rp " . number_format($value->amount, 0, ",", ".");
			$feedback['data']['last_transaction'][$i]['type']		= $value->transaction_type_id;
			$feedback['data']['last_transaction'][$i]['balance']	= "Rp " . number_format($value->balance, 0, ",", ".");
			$feedback['data']['last_transaction'][$i]['date']		= date("d M Y H:i", strtotime($value->transaction_date));
			$feedback['data']['last_transaction'][$i]['status']		= $value->transaction_status_id;
			$i++;
		}

		$this->_feedback($feedback);
	}

	function referral_code() {
		$this->load->library('secure');
		$this->load->model(array('customers'));

		$api_param = array();
		$data = $this->secure->auth_account($api_param);

		$customer_data	= $this->customers->select_by_id($data['login_data']->customer_id);
		
		$feedback['error'] 						= false;
		$feedback['data']['referral_code']		= $customer_data[0]->referral_code;

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
