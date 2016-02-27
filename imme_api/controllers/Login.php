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
 * 
 * [account][balance]
 * [account][account_number]
 * [account][full_name]
 * [account][picture_url]
 * [account][email]
 * [account][phone_number]
 * [account][idcard_number]
 * [account][idcard_type]
 * [account][is_verified_email]
 * [account][is_verified_phone]
 * [account][referral_code]
 *
 * [transaction_history][0][transaction_number]
 * [transaction_history][0][date]
 * [transaction_history][0][merchant_name]
 * [transaction_history][0][amount]
 * [transaction_history][0][transaction_type]
 *
 * [security_setting][track_transaction]
 * [security_setting][color_security]
 */

class Error extends CI_Controller {
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

			$feedback['error']			= false;
			$feedback['message']	= 'You are logged in';
			//$feedback['csrf_token']		= $this->security->get_csrf_hash();
			$feedback['session_key']	= $login_session['session_key'];
			$feedback['imme_algorithm']	= $security_algorithm[0]->imme_algorithm; // Must encrypted before send to client
			$feedback['tba_algorithm']	= $security_algorithm[0]->tba_algorithm; // Must encrypted before send to client
			$feedback['cba_algorithm']	= $security_algorithm[0]->cba_algorithm; // Must encrypted before send to client
			$feedback['cba_counter']	= $security_algorithm[0]->cba_counter; // Must encrypted before send to client

			$account_data = $this->accounts->select_by_id($balance_data[0]->account_id);
			$feedback['account']['balance']				= $balance_data[0]->balance;
			$feedback['account']['account_number']		= $account_data[0]->account_number;
			$feedback['account']['full_name']			= $customer_data[0]->full_name;
			$feedback['account']['picture_url']			= $customer_data[0]->picture_url;
			$feedback['account']['email']				= $customer_data[0]->email;
			$feedback['account']['phone_number']		= $customer_data[0]->phone_number;
			$feedback['account']['idcard_number']		= $customer_data[0]->idcard_number;
			$feedback['account']['idcard_type']			= $customer_data[0]->idcard_type_id;
			$feedback['account']['is_verified_email']	= $customer_data[0]->is_email_verified;
			$feedback['account']['is_verified_phone']	= $customer_data[0]->is_phone_verified;
			$feedback['account']['referral_code']		= $customer_data[0]->referral_code;

			$transaction_data = $this->transactions->select_by_id($customer_data[0]->customer_id);
			$i = 0;
			foreach ($transaction_data as $value) {
				$feedback['transaction_history'][$i]['transaction_reference']	= $value->transaction_reference;
				$feedback['transaction_history'][$i]['date']					= $value->transaction_date;

				$history = $this->transactions->select_by_transaction_reference($value->transaction_reference);
				foreach ($history as $value_history) {
					if ($value_history->transaction_type_id != $value->transaction_type_id) {
						$history_name = $this->customers->select_by_id($value_history->customer_id);
						$feedback['transaction_history'][$i]['name'] = $history_name[0]->full_name;
					}
				}

				$feedback['transaction_history'][$i]['amount']					= $value->amount;
				$feedback['transaction_history'][$i]['transaction_type']		= $value->transaction_type_id;
				$i++;
			}

			$feedback['security_setting']['track_transaction']	= $setting_data[0]->track_transaction;
	 		$feedback['security_setting']['color_security']		= $setting_data[0]->color_security;
	 
	 		$feedback['gift_voucher'] = false;

			$this->_feedback($feedback);
		} else {
			$this->_error('115', 'Wrong password');
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
