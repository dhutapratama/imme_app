<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V1_server extends CI_Controller {
	public function __construct() {
        parent::__construct();
		$this->load->model(array('merchants', 'payment', 'customers'));
     }

	public function index()
	{
		$this->write->error("Are you trying to hack this server?");
	}

	public function create_payment() {
    	$merchants_data = $this->auth->merchant_key();
		$check = array(
			'google_user_id' => 'required',
			'description' => 'required',
			'payment_number' => 'required',
			'amount' => 'required' );
    	$input = $this->auth->input($check);

    	$customers_data = $this->customers->get_by_google_user_id($input['google_user_id']);
    	if (!$customers_data) {
    		$this->write->error("User tidak terdaftar di IMME Wallet!");
    	}

    	$payment_key = md5(time().rand(1000, 9999));

    	$payment['merchant_id']			= $merchants_data->merchant_id;
    	$payment['account_id']			= $customers_data->customer_id;
    	$payment['payment_number']		= $input['payment_number'];
		$payment['description']			= $input['description'];
		$payment['amount']				= $input['amount'];
		$payment['date']				= date("Y-m-d H:i:s");
		$payment['payment_status_id']	= 0;
		$payment['payment_key']			= $payment_key;
		$this->payment->insert($payment);
		$payment_id = $this->db->insert_id();

		$this->load->library('google');
		$this->google->push_message(
			$customers_data->gcm_token,
			"Tagihan " . $merchants_data->name,
			$payment['payment_key'],
			$input['description'],
			$payment_id );

		$feedback['payment_number']		= $input['payment_number'];
		$feedback['payment_key']		= $payment_key;
		$this->write->feedback($feedback);
	}
}
