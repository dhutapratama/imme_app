<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Receive extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'receive_sessions', 'customers', 'balances'));
    }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	/**
	* Untuk receive money
	* 
	* POST Param :
	* [session_key]
	* [amount]
	*
	* Feedback :
	* [error]
	* [transaction_code]
	*/

	public function create()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('amount', 'Amount', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$amount			= $this->input->post('amount');
		} else {
			$this->_error('107', 'Request amount failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$transaction_code 							= "ee-".md5(rand(1000000,9999999));
		$receive_sessions['transaction_code']		= $transaction_code;
		$receive_sessions['customer_id']			= $login_data[0]->customer_id;
		$receive_sessions['account_id']				= $login_data[0]->account_id;
		$receive_sessions['balance_id']				= $login_data[0]->balance_id;
		$receive_sessions['amount']					= $amount;
		$receive_sessions['transaction_type_id']	= '1';
		$receive_sessions['created_date']			= date('Y-m-d H:i:s');
		$receive_sessions['expired_date']			= date('Y-m-d H:i:s', time() + 60);
		$receive_sessions['transaction_status_id']	= '1';

		$this->receive_sessions->insert($receive_sessions);

		$feedback['error'] 				= false;
		$feedback['amount']				= $amount;
		$feedback['transaction_code']	= $transaction_code;

		$this->_feedback($feedback);
	}

	/**
	* Untuk receive money
	* 
	* POST Param :
	* [session_key]
	* [transaction_code]
	*
	* Feedback :
	* [error]
	* [sender_name]
	* [sender_picture]
	*/
	public function check_sender() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('transaction_code', 'Transaction Code', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$transaction_code	= $this->input->post('transaction_code');
		} else {
			$this->_error('128', 'Check sender failed');
			
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
			
		}

		while (true) {
			$session_data = $this->receive_sessions->check_scanned($login_data[0]->balance_id, $transaction_code);
			if (!$session_data) {
				$this->_error('120', 'Transaction is not found');
			}
			if ($session_data[0]->expired_date <= date('Y-m-d H:i:s')) {
				$receive_sessions['transaction_status_id']	= 8;
				$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);
				$this->_error('129', 'Transaction code expired');
			}
			if ($session_data[0]->transaction_status_id != 1) {
				break;
			}
			sleep(1);
		}

		if ($session_data[0]->transaction_status_id == 6) {
			$payer = $this->customers->select_by_id($session_data[0]->payer_customer_id);
			$feedback['error'] 				= false;
			$feedback['sender_name']		= $payer[0]->full_name;
			$feedback['sender_picture']		= $payer[0]->picture_url;

			$this->_feedback($feedback);
		} else {
			$this->_error('134', 'Transaction canceled');
		}
		
	}

	public function check_transfered() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('transaction_code', 'Transaction Code', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$transaction_code	= $this->input->post('transaction_code');
		} else {
			$this->_error('128', 'Check sender failed');
			
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
			
		}

		$timeout = 60;
		while (true) {
			$session_data = $this->receive_sessions->check_scanned($login_data[0]->balance_id, $transaction_code);
			if ($session_data[0]->transaction_status_id == 2) {
				$balance_data 			= $this->balances->select_by_id($login_data[0]->balance_id);
				$feedback['error'] 		= false;
				$feedback['balance']	= $balance_data[0]->balance;

				$this->_feedback($feedback);
			} elseif ($session_data[0]->transaction_status_id == 3) {
				$this->_error('130', 'Payer not send money');
			} elseif ($session_data[0]->transaction_status_id == 4) {
				$this->_error('134', 'Transaction canceled by payer');
			} elseif ($session_data[0]->transaction_status_id == 7) {
				$this->_error('135', 'Transaction failed');
			} else {
				$timeout--;
				if ($timeout == 0) {
					break;
				}
				sleep(1);
			}
		}

		$receive_sessions['transaction_status_id']	= 8;
		$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);
		$this->_error('129', 'Transaction code expired');
	}

	/**
	* Untuk receive money
	* 
	* POST Param :
	* [session_key]
	* [transaction_code]
	*
	* Feedback :
	* [error]
	* [sender_name]
	* [sender_picture]
	*/

	public function cancel() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('transaction_code', 'Transaction Code', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$transaction_code	= $this->input->post('transaction_code');
		} else {
			$this->_error('136', 'Cancel transaction failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
			
		}

		$session_data = $this->receive_sessions->check_scanned($login_data[0]->balance_id, $transaction_code);
		if ($session_data[0]->transaction_status_id == 1) {
			$receive_sessions['transaction_status_id']	= 4;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);
			$feedback['error'] 	= false;
			$this->_feedback($feedback);
		} else {
			$this->_error('137', 'You cannot cancel this transaction');
		}
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
