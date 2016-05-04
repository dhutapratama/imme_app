<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pay extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'receive_sessions', 'customers', 'accounts', 'balances', 'transaction_pending', 'transactions', 'sim_transactions', 'merchants'));
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	/**
	 * Untuk check available dan lock receive
	 * 
	 * POST Param :
	 * [session_key]
	 * [transaction_code]
	 *
	 * Feedback :
	 * [error]
	 * [transaction_message]
	 * [recipient_name]
	 * [amount]
	 * [apply_code]
	 */
	public function check()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('transaction_code', 'Transaction Code', 'required');

		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$transaction_code	= $this->input->post('transaction_code');
		} else {
			$this->_error('118', 'Check transaction code failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) {
			$this->_error('108', 'Expired login session');
		}
		
		$session_data = $this->receive_sessions->select_by_transaction_code($transaction_code);
		if (!$session_data) {
			$this->_error('133', 'Transaction code is not found');
		}

		if ($login_data[0]->customer_id == $session_data[0]->customer_id) {
			$this->_error('-', 'You cant send money to yourself account');
		}

		$update_receive_session['apply_code']				= md5(rand(1000000, 9999999));
		$update_receive_session['transaction_status_id']	= 6;
		$update_receive_session['payer_customer_id']		= $login_data[0]->customer_id;
		$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $update_receive_session);

		if ($session_data[0]->transaction_type_id == '1') 
		{
			$recipient = $this->customers->select_by_id($session_data[0]->customer_id);
			$feedback['recipient_name']		= $recipient[0]->full_name;
		} 
		elseif ($session_data[0]->transaction_type_id == '8')
		{
			$recipient = $this->merchants->select_by_id($session_data[0]->merchant_id);
			$feedback['recipient_name']		= $recipient[0]->merchant_name;
		}
		
		
		$feedback['error']					= false;
		$feedback['transaction_type']		= $session_data[0]->transaction_type_id; // 2 = Transfer or 8 = Payment
		$feedback['amount']					= $session_data[0]->amount;
		$feedback['apply_code']				= $update_receive_session['apply_code'];
		$this->_feedback($feedback);
	}

	/**
	 * Kirim uang ke user lainnya
	 * 
	 * POST Param :
	 * [session_key]
	 * [apply_code]
	 * [pin_1]
	 *
	 * Feedback :
	 * [error]
	 * [balance]
	 * [transaction_reference]
	 */
	public function send()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('apply_code', 'Apply Code', 'required')
			->set_rules('pin_1', 'PIN 1', 'required');

		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$apply_code			= $this->input->post('apply_code');
			$pin_1				= md5($this->input->post('pin_1'));
		} else {
			$this->_error('119', 'Pay failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) {
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->match_pin1_by_id($login_data[0]->account_id, $pin_1);
		if (!$account_data) {
			$this->_error('113', 'PIN 1 Error');
		}

		$session_data = $this->receive_sessions->select_by_payer_apply_code($apply_code, $login_data[0]->customer_id);
		if (!$session_data) {
			$this->_error('111', 'Apply code not found');
		}

		if ($session_data[0]->transaction_type_id != "1") {
			$this->_error('140', 'Error API transaction URL');
		}

		$payer_balance_data = $this->balances->select_by_customer_id($login_data[0]->customer_id);
		$payee_balance_data = $this->balances->select_by_customer_id($session_data[0]->customer_id);

		if ($payer_balance_data[0]->in_transaction OR $payee_balance_data[0]->in_transaction) {
			$transaction_date = date('Y-m-d H:i:s');
			$insert_pending = array(
			        array(
			                'transaction_code'		=> $session_data[0]->transaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 2,
			                'transaction_status_id'	=> 3
			        ),
			        array(
			                'transaction_code'		=> $session_data[0]->transaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> $payee_balance_data[0]->customer_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 1,
			                'transaction_status_id'	=> 3
			        )
				);
			$this->transaction_pending->insert_batch($insert_pending);

			$receive_sessions['transaction_status_id']	= 3;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);
			
			$feedback['error']					= false;
			$feedback['transaction_message']	= "Transaction pending";
			$feedback['transaction_reference']	= $transaction_reference;
			$this->_feedback($feedback);
		}

		$payer_update['balance'] = $payer_balance_data[0]->balance - $session_data[0]->amount; // Belum ada pengurangan fee transaksi
		$payee_update['balance'] = $payee_balance_data[0]->balance + $session_data[0]->amount;

		if ($payer_update['balance'] < 0) {
			$receive_sessions['transaction_status_id']	= 7;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

			$this->_error('112', 'Insufficient balance');
		}

		$this->balances->in_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$this->balances->update_by_id($payer_balance_data[0]->balance_id, $payer_update);
		$this->balances->update_by_id($payee_balance_data[0]->balance_id, $payee_update);

		$this->balances->close_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$transaction_date = date('Y-m-d H:i:s');
		$transaction_reference = md5(time().rand('1000000', '99999999'));
		$insert_transaction = array(
			        array(
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 2,
			                'balance'				=> $payer_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2

			        ),
			        array(
			                'customer_id'			=> $payee_balance_data[0]->customer_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 1,
			                'balance'				=> $payee_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2
			        )
				);
		$this->transactions->insert_batch($insert_transaction);

		$receive_sessions['transaction_status_id']	= 2;
		$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

		$feedback['error']					= false;
		$feedback['balance']				= $payer_update['balance'];
		$feedback['transaction_reference']	= $transaction_reference;
		$this->_feedback($feedback);
	}

	public function payment()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('apply_code', 'Apply Code', 'required')
			->set_rules('pin_1', 'PIN 1', 'required');

		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$apply_code			= $this->input->post('apply_code');
			$pin_1				= md5($this->input->post('pin_1'));
		} else {
			$this->_error('119', 'Pay failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) {
			$this->_error('108', 'Expired login session');
		}

		$account_data = $this->accounts->match_pin1_by_id($login_data[0]->account_id, $pin_1);
		if (!$account_data) {
			$this->_error('113', 'PIN 1 Error');
		}

		$session_data = $this->receive_sessions->select_by_payer_apply_code($apply_code, $login_data[0]->customer_id);
		if (!$session_data) {
			$this->_error('111', 'Apply code not found');
		}

		if ($session_data[0]->transaction_type_id != "8") {
			$this->_error('140', 'Error API transaction URL');
		}

		$payer_balance_data = $this->balances->select_by_customer_id($login_data[0]->customer_id);
		$payee_balance_data = $this->balances->select_by_merchant_id($session_data[0]->merchant_id);

		// Simulation Purpose
		if ($session_data[0]->is_simulation == "1") {
			$payer_update['balance'] = $payer_balance_data[0]->balance; // Belum ada pengurangan fee transaksi
			$payee_update['balance'] = $payee_balance_data[0]->balance;

			$transaction_date = date('Y-m-d H:i:s');
			$transaction_reference = md5(time().rand('1000000', '99999999'));
			$insert_transaction = array(
				        array(
				                'customer_id'			=> $payer_balance_data[0]->customer_id,
				                'merchant_id'			=> 0,
				                'account_id'			=> $payer_balance_data[0]->account_id,
				                'balance_id'			=> $payer_balance_data[0]->balance_id,
				                'amount'				=> 0,
				                'transaction_type_id'	=> 2,
				                'balance'				=> $payer_update['balance'],
				                'transaction_date'		=> $transaction_date,
				                'transaction_reference'	=> $transaction_reference,
				                'transaction_status_id'	=> 2

				        ),
				        array(
				        		'customer_id'			=> 0,
				                'merchant_id'			=> $payee_balance_data[0]->merchant_id,
				                'account_id'			=> $payee_balance_data[0]->account_id,
				                'balance_id'			=> $payee_balance_data[0]->balance_id,
				                'amount'				=> 0,
				                'transaction_type_id'	=> 8,
				                'balance'				=> $payee_update['balance'],
				                'transaction_date'		=> $transaction_date,
				                'transaction_reference'	=> $transaction_reference,
				                'transaction_status_id'	=> 2
				        )
					);
			$this->sim_transactions->insert_batch($insert_transaction);

			$receive_sessions['transaction_status_id']	= 2;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

			$feedback['error']					= false;
			$feedback['balance']				= $payer_update['balance'];
			$feedback['transaction_reference']	= $transaction_reference;
			$this->_feedback($feedback);
		}

		if ($payer_balance_data[0]->in_transaction OR $payee_balance_data[0]->in_transaction) {
			$transaction_date = date('Y-m-d H:i:s');
			$insert_pending = array(
			        array(
			                'transaction_code'		=> $session_data[0]->transaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
				            'merchant_id'			=> 0,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 2,
			                'transaction_status_id'	=> 3
			        ),
			        array(
			                'transaction_code'		=> $session_data[0]->transaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> 0,
			                'merchant_id'			=> $payee_balance_data[0]->merchant_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 8,
			                'transaction_status_id'	=> 3
			        )
				);
			$this->transaction_pending->insert_batch($insert_pending);

			$receive_sessions['transaction_status_id']	= 3;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);
			
			$feedback['error']					= false;
			$feedback['transaction_message']	= "Transaction pending";
			$feedback['transaction_reference']	= $transaction_reference;
			$this->_feedback($feedback);
		}

		$payer_update['balance'] = $payer_balance_data[0]->balance - $session_data[0]->amount; // Belum ada pengurangan fee transaksi
		$payee_update['balance'] = $payee_balance_data[0]->balance + $session_data[0]->amount;

		if ($payer_update['balance'] < 0) {
			$receive_sessions['transaction_status_id']	= 7;
			$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

			$this->_error('112', 'Insufficient balance');
		}

		$this->balances->in_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$this->balances->update_by_id($payer_balance_data[0]->balance_id, $payer_update);
		$this->balances->update_by_id($payee_balance_data[0]->balance_id, $payee_update);

		$this->balances->close_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$transaction_date = date('Y-m-d H:i:s');
		$transaction_reference = md5(time().rand('1000000', '99999999'));
		$insert_transaction = array(
			        array(
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
				            'merchant_id'			=> 0,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 2,
			                'balance'				=> $payer_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2

			        ),
			        array(
			        		'customer_id'			=> 0,
			                'merchant_id'			=> $payee_balance_data[0]->merchant_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $session_data[0]->amount,
			                'transaction_type_id'	=> 8,
			                'balance'				=> $payee_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2
			        )
				);
		$this->transactions->insert_batch($insert_transaction);

		$receive_sessions['transaction_status_id']	= 2;
		$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

		$feedback['error']					= false;
		$feedback['balance']				= $payer_update['balance'];
		$feedback['transaction_reference']	= $transaction_reference;
		$this->_feedback($feedback);
	}

	public function cancel() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('apply_code', 'Apply Code', 'required');

		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$apply_code			= $this->input->post('apply_code');
		} else {
			$this->_error('119', 'Cancel failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) {
			$this->_error('108', 'Expired login session');
		}

		$session_data = $this->receive_sessions->select_by_payer_apply_code($apply_code, $login_data[0]->customer_id);
		if (!$session_data) {
			$this->_error('111', 'Error apply code');
		}

		$receive_sessions['transaction_status_id']	= 4;
		$this->receive_sessions->update_by_id($session_data[0]->receive_session_id, $receive_sessions);

		$feedback['error']					= false;
		$feedback['message']				= "Transaction was canceled";
		$this->_feedback($feedback);
	}

	public function transfer() {
		$this->load->library('secure');
		//$this->load->model(array('customers', 'accounts'));

		$api_param = array('search_id' => 'Recipient', 'amount' => 'Amount', 'message' => 'message', 'pin_1' => 'PIN 1');
		$data = $this->secure->auth_account($api_param);

		$account_data = $this->accounts->select_by_id($data['login_data']->account_id);
		if (!$account_data) {
			$this->_error('-', 'Your account is not active');
		}


		$account_data = $this->accounts->match_pin1_by_id($data['login_data']->account_id, md5($data['pin_1']));
		if (!$account_data) {
			$this->_error('113', 'PIN 1 Error');
		}

		$customer_data = $this->customers->select_by_search_id($data['search_id']);
		if (!$customer_data) {
			$this->_error('-', 'Payee customer error');
		}

		$payer_balance_data = $this->balances->select_by_customer_id($data['login_data']->customer_id);
		$payee_balance_data = $this->balances->select_by_customer_id($customer_data[0]->customer_id);

		$transaction_code = md5(time().rand(11111, 999999));

		if ($payer_balance_data[0]->in_transaction OR $payee_balance_data[0]->in_transaction) {
			$transaction_date = date('Y-m-d H:i:s');
			$insert_pending = array(
			        array(
			                'transaction_code'		=> $transaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $data['amount'],
			                'transaction_type_id'	=> 2,
			                'transaction_status_id'	=> 3
			        ),
			        array(
			                'transaction_code'		=> $stransaction_code,
			                'transaction_date'		=> $transaction_date,
			                'last_processed_date'	=> $transaction_date,
			                'customer_id'			=> $payee_balance_data[0]->customer_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $data['amount'],
			                'transaction_type_id'	=> 1,
			                'transaction_status_id'	=> 3
			        )
				);
			$this->transaction_pending->insert_batch($insert_pending);
			
			$feedback['error']		= false;
			$feedback['message']	= "Transaction pending";
			$feedback['reference']	= $transaction_reference;
			$this->_feedback($feedback);
		}

		$payer_update['balance'] = $payer_balance_data[0]->balance - $data['amount']; // Belum ada pengurangan fee transaksi
		$payee_update['balance'] = $payee_balance_data[0]->balance + $data['amount'];

		if ($payer_update['balance'] < 0) {
			$this->_error('112', 'Insufficient balance');
		}

		$this->balances->in_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$this->balances->update_by_id($payer_balance_data[0]->balance_id, $payer_update);
		$this->balances->update_by_id($payee_balance_data[0]->balance_id, $payee_update);

		$this->balances->close_transaction($payer_balance_data[0]->balance_id, $payee_balance_data[0]->balance_id);

		$transaction_date = date('Y-m-d H:i:s');
		$transaction_reference = md5(time().rand('1000000', '99999999'));
		$insert_transaction = array(
			        array(
			                'customer_id'			=> $payer_balance_data[0]->customer_id,
			                'account_id'			=> $payer_balance_data[0]->account_id,
			                'balance_id'			=> $payer_balance_data[0]->balance_id,
			                'amount'				=> $data['amount'],
			                'transaction_type_id'	=> 2,
			                'balance'				=> $payer_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2

			        ),
			        array(
			                'customer_id'			=> $payee_balance_data[0]->customer_id,
			                'account_id'			=> $payee_balance_data[0]->account_id,
			                'balance_id'			=> $payee_balance_data[0]->balance_id,
			                'amount'				=> $data['amount'],
			                'transaction_type_id'	=> 1,
			                'balance'				=> $payee_update['balance'],
			                'transaction_date'		=> $transaction_date,
			                'transaction_reference'	=> $transaction_reference,
			                'transaction_status_id'	=> 2
			        )
				);
		$this->transactions->insert_batch($insert_transaction);

		$feedback['error']					= false;
		$feedback['data']['balance']				= $payer_update['balance'];
		$feedback['data']['transaction_reference']	= $transaction_reference;
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
