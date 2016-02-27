<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deposit extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'deposit_vouchers', 'transactions', 'balances', 'customers'));
        $this->load->helper('file');
     }

	public function index()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('voucher_code', 'Voucher Code', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$voucher_code	= $this->input->post('voucher_code');
		} else {
			$this->_error('121', 'Deposit failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$voucher = $this->deposit_vouchers->select_by_voucher_code($voucher_code);
		if (!$voucher) {
			$this->_error('122', 'Voucher code not valid');
		}

		if ($voucher[0]->is_used == "1") {
			$this->_error('122', 'Voucher code had used before');
		}

		if ($voucher[0]->is_simulation == "1") {
			$customer = $this->customers->select_by_id($login_data[0]->customer_id);
			if ($customer[0]->use_simulation == "1") {
				$this->_error('139', 'You has used this gift voucher before');
			}
			$customers['use_simulation'] = 1;
			$this->customers->update_by_id($login_data[0]->customer_id, $customers);
		}

		$deposit_vouchers['is_used']		= '1';
		$deposit_vouchers['account_id']		= $login_data[0]->account_id;
		$this->deposit_vouchers->update_by_id($voucher[0]->voucher_id, $deposit_vouchers);

		$transaction_reference = md5(time().rand('1000000', '99999999'));
		$transaction_date = date('Y-m-d H:i:s');
		$customer_balance = $this->balances->select_by_customer_id($login_data[0]->customer_id);
		$balance = $customer_balance[0]->balance + $voucher[0]->amount;
		$transaction['customer_id']				= $login_data[0]->customer_id;
		$transaction['account_id']				= $login_data[0]->account_id;
		$transaction['balance_id']				= $login_data[0]->balance_id;
		$transaction['amount']					= $voucher[0]->amount;
		$transaction['transaction_type_id']		= 5;
		$transaction['balance']					= $balance;
	
		$transaction['transaction_date']		= $transaction_date;
		$transaction['transaction_reference']	= $transaction_reference;
		$transaction['transaction_status_id']	= 2;
		$this->transactions->insert($transaction);

		$balance_now['balance'] = $balance;
		$this->balances->in_transaction($login_data[0]->balance_id);
		$this->balances->update_by_id($login_data[0]->balance_id, $balance_now);
		$this->balances->close_transaction($login_data[0]->balance_id);

		$feedback['error'] 			= false;
		$feedback['deposit_amount']	= $transaction['amount'];
		$feedback['balance']		= $balance;

		// Deleting non existing voucher code
		if ($voucher[0]->is_simulation == "1") {
			$file = FCPATH.'storage/vouchers/'.$voucher_code.'.png';
			if (get_file_info($file)) {
				unlink($file);
			}
		} else {
			$file = FCPATH.'vouchers/'.$voucher_code.'.png';
			if (get_file_info($file)) {
				unlink($file);
			}
		}

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
