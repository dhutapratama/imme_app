<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V2_customer extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->model(array('customers', 'accounts', 'payment', 'merchants', 'login', 'transactions'));
        if ($_SERVER['REQUEST_METHOD'] != "POST") {
        	//redirect();
        }
    }

    public function index() {
    	$this->load->library('google');
    	echo $this->google->getLibraryVersion();
    }

    public function account() {
    	$input = $this->auth->input_v2();

    	$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=".$input->id_token;
		$opts = array('http' => array( 'ignore_errors'  => true ) );

		$context = stream_context_create($opts);
    	$google_json = file_get_contents($url, false, $context);
    	$google_data = json_decode($google_json);

    	if (!isset($google_data->sub)) {
			if (isset($google_data->error_description)) {
	    		$this->write->error($google_data->error_description);
	    	} else {
	    		$this->write->error("Unknown Error");
	    	}
    	}

    	if (!isset($google_data->picture)) {
    		$user_picture = "http://imme.duckdns.org/default.png";
    	} else {
    		$user_picture = $google_data->picture;
    	}

    	$customers_data = $this->customers->get_by_google_user_id($google_data->sub);
    	if (!$customers_data) {
    		$customer['google_user_id']		= $google_data->sub;
    		$customer['name']				= $google_data->name;
			$customer['email']				= $google_data->email;
			$customer['phone']				= "";
			$customer['picture_url']		= $user_picture;
			$customer['email_verify_code']	= md5(time() . "em" . rand(100, 999));
			$customer['phone_verify_code']	= rand(100000, 999999);
			$customer['is_email_verified']	= 0;
			$customer['is_phone_verified']	= 0;
			$customer['is_blocked']			= 0;
			$customer['referral_code']		= rand(100000, 999999);
			$customer['search_id']			= md5(time().rand(10, 99));
			$customer['id_token']			= $input->id_token;
			if (isset($input->gcm_token)) {
				$customer['gcm_token'] 			= $input->gcm_token;
	    	}
    		$this->customers->insert($customer);
    		$customers_data = $this->customers->get_by_google_user_id($google_data->sub);

    		$this->load->library(array('image_lib', 'ciqrcode'));
			$params['data'] = $customer['search_id'];
			$params['level'] = 'H';
			$params['size'] = 200;
			$params['savename'] = FCPATH.'search_id/'.$params['data'].'.png';
			$this->ciqrcode->generate($params);

			$accounts['customer_id']	= $customers_data->customer_id;
			$accounts['merchant_id']	= 0;
			$accounts['account_number']	= 10000 + $customers_data->customer_id;
			$accounts['pin']			= 0;
			$accounts['balance']		= 10000;
			$accounts['in_transaction']	= 0;
			$this->accounts->insert($accounts);
    	} else {
    		if (isset($input->gcm_token)) {
		    	$customer['gcm_token'] 			= $input->gcm_token;
		    	$this->customers->update($customers_data->customer_id, $customer);
	    	}
    	}

    	if ($customers_data->is_blocked == "1") {
    		$this->write->error("Your Wallet was blocked");
    	}

		$accounts_data = $this->accounts->get_by_customer_id($customers_data->customer_id);
    	$login['login_key'] 	= md5(time().rand(10,99));
    	$login['customer_id'] 	= $accounts_data->customer_id;
    	$login['account_id'] 	= $accounts_data->account_id;
    	$login['merchant_id'] 	= $accounts_data->merchant_id;
    	$this->login->insert($login);

    	$feedback['error'] 			   			= false;
    	$feedback['data']['login_key'] 			= $login['login_key'];
    	$feedback['data']['is_phone_verified']	= $customers_data->is_phone_verified;
    	$this->write->feedback($feedback);
    }


    public function save_pin_phone() {
    	$login_data = $this->auth->login_key();

    	$check = array(
    		'pin' => 'required',
    		'phone' => 'required');
    	$input = $this->auth->input($check);

		$customers['phone'] = $input['phone'];
		$this->customers->update($login_data->customer_id, $customers);

		$accounts['pin'] = md5($input['pin']);
		$this->accounts->update($login_data->account_id, $accounts);

		$customers_data = $this->customers->get_by_id($login_data->customer_id);

/*
		$this->load->model("gammu");
		$send['DestinationNumber']	= $input['phone'];
		$send['TextDecoded']		= "Kode Verifikasi " . $customers_data->phone_verify_code . ". IMME Wallet, Aman dan Simpel!";
		$send['CreatorID']			= "IMME Verifikasi";
		$sms_id = $this->gammu->send($send);

		while ($this->gammu->get_by_id($sms_id)) {
    		sleep(3);
    	}
*/

    	$this->load->model("sms");
    	$sms['destination']	= $input['phone'];
		$sms['text']		= "Kode Verifikasi " . $customers_data->phone_verify_code . ". IMME Wallet, Aman dan Simpel!";
		$sms['status']		= "0";
		$sms_id = $this->sms->insert($sms);

		while ($this->sms->get_by_processed($sms_id)) {
    		sleep(3);
    	}

		$this->write->feedback();
    }

    public function verify_phone() {
    	$login_data = $this->auth->login_key();

    	$check = array( 'verification_code' => 'required');
    	$input = $this->auth->input($check);

    	$customers_data = $this->customers->get_by_id($login_data->customer_id);
    	if ($customers_data->phone_verify_code != $input['verification_code']) {
    		$this->write->error("Kode verifikasi anda salah.");
    	}

    	$customer['is_phone_verified']	= 1;
    	$this->customers->update($login_data->customer_id, $customer);

    	$this->write->feedback();
    }

    // -------- GET Balance -------- //
	public function	balance() {
		$login_data = $this->auth->login_key_v2();
    	$input = $this->auth->input_v2();

		$customers_data = $this->customers->get_by_id($login_data->customer_id);
		if (!$customers_data) {
			$this->write->error("Mohon login kembali");
		}
		$accounts_data = $this->accounts->get_by_id($login_data->account_id);
		if (!$accounts_data) {
			$this->write->error("Your account was deleted");
		}

		//$feedback['data']['balance'] 			= number_format($accounts_data->balance, 2, ',', '.')."Ҝ";
		$feedback['data']['balance'] 			= "Rp".number_format($accounts_data->balance, 0, ',', '.');
		$feedback['data']['search_id'] 			= $customers_data->search_id;
		$feedback['data']['search_id_image'] 	= "http://".$_SERVER['HTTP_HOST']."/search_id/".$customers_data->search_id.".png";
    	$feedback['data']['is_phone_verified']	= $customers_data->is_phone_verified;
		$this->write->feedback($feedback);
	}

	public function transaction_history() {
		$login_data = $this->auth->login_key_v2();

		$transactions_data = $this->transactions->get_by_customer_id($login_data->customer_id);
		if (!$transactions_data) {
			$this->write->error("Tidak ada transaksi");
		}

		$this->load->model("transaction_types");

		$i = 0;
		foreach ($transactions_data as $value) {
			$transaction_vs = $this->transactions->get_vs_transaction($value->transaction_referrence, $login_data->customer_id);
			if (!$transaction_vs) {
				$transaction[$i]['name']			= "IMME";
				$transaction[$i]['picture']			= "http://imme.duckdns.org/ic_launcher.png";
			} else {
				$customers_data = $this->customers->get_by_id($transaction_vs->customer_id);

				if ($value->transaction_type_id == "4") {
					$accounts_data = $this->accounts->get_by_customer_id($transaction_vs->customer_id);
					$merchants_data = $this->merchants->get_by_id($accounts_data->merchant_id);
					$transaction[$i]['name']		= $merchants_data->name;
					$transaction[$i]['picture']		= $customers_data->picture_url;
				} else {
					$transaction[$i]['name']		= $customers_data->name;
					$transaction[$i]['picture']		= $customers_data->picture_url;
				}
			}
			

			$transaction_type_data = $this->transaction_types->get_by_id($value->transaction_type_id);

			$transaction[$i]['type']			= $transaction_type_data->name;
			$transaction[$i]['amount']			= "Rp".number_format($value->amount, 0, ',', '.');
			$transaction[$i]['description']		= $value->description;
			$transaction[$i]['date']			= date("d M Y", strtotime($value->transaction_date));
			$transaction[$i]['transaction_refference']	= $value->transaction_referrence;
			$i++;
		}

		$feedback['data']['transactions']	= $transaction;
		$this->write->feedback($feedback);
	}

	public function transaction_detail() {
		$login_data = $this->auth->login_key_v2();
		$input = $this->auth->input_v2();

		$transactions_data = $this->transactions->get_by_reference($input->transaction_refference);
		if (!$transactions_data) {
			$this->write->error("Tidak ada transaksi");
		}


		$transaction_vs = $this->transactions->get_vs_transaction($input->transaction_refference, $login_data->customer_id);
		$transaction_my = $this->transactions->get_my_transaction($input->transaction_refference, $login_data->customer_id);
		$this->load->model("transaction_types");
		$transaction_type_data = $this->transaction_types->get_by_id($transaction_my->transaction_type_id);

		if (!$transaction_vs) {
			$transaction['name']			= "IMME";
			$transaction['picture']			= "http://imme.duckdns.org/ic_launcher.png";
		} else {
			$customers_data = $this->customers->get_by_id($transaction_vs->customer_id);

			if ($transaction_my->transaction_type_id == "4") {
				$accounts_data 		= $this->accounts->get_by_customer_id($transaction_vs->customer_id);
				$merchants_data 	= $this->merchants->get_by_id($accounts_data->merchant_id);
				$transaction['name']		= $merchants_data->name;
				$transaction['picture']		= $customers_data->picture_url;
			} else {
				$transaction['name']		= $customers_data->name;
				$transaction['picture']		= $customers_data->picture_url;
			}
		}

		$transaction['type']		= $transaction_type_data->name;
		$transaction['amount']		= "Rp".number_format($transaction_my->amount, 0, ',', '.');
		$transaction['description']	= $transaction_my->description;
		$transaction['date']		= date("d M Y H:i", strtotime($transaction_my->transaction_date));

		$feedback['data']	= $transaction;
		$this->write->feedback($feedback);
	}

	public function get_payment() {
		$login_data = $this->auth->login_key();

		$payment_data = $this->payment->get_by_account_id($login_data->account_id);
		if (!$payment_data) {
			$this->write->error("Anda tidak memiliki tagihan");
		}

		$i = 0;
		foreach ($payment_data as $value) {
			$merchants_data = $this->merchants->get_by_id($value->merchant_id);
			$payment[$i]['merchant_name']	= $merchants_data->name;
			$payment[$i]['description']		= $value->description;
			$payment[$i]['amount']			= "Rp".number_format($value->amount, 0, ',', '.');
			$payment[$i]['date']			= date("d M Y", strtotime($value->date));
			$payment[$i]['payment_key']		= $value->payment_key;
			$i++;
		}

		$feedback['data']['payment']	= $payment;
		$this->write->feedback($feedback);
	}

	public function check_payment() {
		$login_data = $this->auth->login_key();

		$check = array( 'payment_key' => 'required');
    	$input = $this->auth->input($check);

		$payment_data = $this->payment->get_by_payment_key($input['payment_key']);
		if (!$payment_data) {
			$this->write->error("Payment key error");
		}

		$merchants_data = $this->merchants->get_by_id($payment_data->merchant_id);

		$feedback['data']['merchant_name']	= $merchants_data->name;
		$feedback['data']['description']	= $payment_data->description;
		$feedback['data']['amount']			= "Rp".number_format($payment_data->amount, 0, '', '.');
		$this->write->feedback($feedback);
	}

	// -------- Transaction -------- //
	public function payment() {
		$login_data = $this->auth->login_key();

		$check = array(
			'pin' => 'required',
			'payment_key' => 'required' );
    	$input = $this->auth->input($check);

    	$accounts_data = $this->accounts->match_pin_by_id($login_data->account_id, $input['pin']);
    	if (!$accounts_data) {
    		$this->write->error("PIN Salah");
    	}

		$payment_data = $this->payment->get_by_payment_key($input['payment_key']);
		if (!$payment_data) {
			$this->write->error("Payment key error");
		}

		$customer_transaction_balance = $accounts_data->balance - $payment_data->amount;
		if ($customer_transaction_balance < 0) {
			$this->write->error("Insufficient Balance");
		}

		$accounts['balance'] = $customer_transaction_balance;
		$this->accounts->update($login_data->account_id, $accounts);

		$merchants_account_data = $this->accounts->get_by_merchant_id($payment_data->merchant_id);
		$merchant_transaction_balance = $merchants_account_data->balance + $payment_data->amount;

		$accounts['balance'] = $merchant_transaction_balance;
		$this->accounts->update($merchants_account_data->account_id, $accounts);

		$referrence_code = md5(time()."trans");
		// Customer
		$transactions['customer_id']			= $login_data->customer_id;
		$transactions['amount']					= $payment_data->amount;
		$transactions['transaction_type_id']	= 4;
		$transactions['balance']				= $customer_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $payment_data->description;
		$this->transactions->insert($transactions);

		// Merchant
		$transactions['customer_id']			= $merchants_account_data->customer_id;
		$transactions['amount']					= $payment_data->amount;
		$transactions['transaction_type_id']	= 2;
		$transactions['balance']				= $merchant_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $payment_data->description;
		$this->transactions->insert($transactions);

		$feedback['data']['balance']			= "Rp".number_format($customer_transaction_balance, 0, '', '.');

		$payment['payment_status_id']			= 1;
		$this->payment->update($payment_data->payment_id, $payment);

		// Notify Merchant Server
		$merchants_data = $this->merchants->get_by_id($payment_data->merchant_id);
		$ch = curl_init();

        $post_data['payment_number']    = $payment_data->payment_number;
        $post_data['status']    		= "Lunas";
        $post_data['status_id']    		= 2;
        $post_data['referrence_code']    = $referrence_code;

        curl_setopt($ch, CURLOPT_URL, $merchants_data->callback_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_exec($ch);
        curl_close ($ch);

		// Status
		$this->write->feedback($feedback);
	}

	public function transfer() {
		$login_data = $this->auth->login_key();

		$check = array(
			'search_id' => 'required',
			'pin' => 'required',
			'amount' => 'required',
			'description' => 'required' );
    	$input = $this->auth->input($check);

    	if ($input['amount'] < 1) {
    		$this->write->error("Jumlah transfer anda tidak kami kenali");
    	}

    	$accounts_data = $this->accounts->match_pin_by_id($login_data->customer_id, $input['pin']);
    	if (!$accounts_data) {
    		$this->write->error("PIN Salah");
    	}

		$payee_customers_data = $this->customers->get_by_search_id($input['search_id']);
		if (!$payee_customers_data) {
			$this->write->error("No valid receiver");
		} 

		if ($payee_customers_data->customer_id == $login_data->customer_id) {
			$this->write->error("Anda tidak dapat mentransfer ke rekening ini");
		}

		$payee_accounts_data = $this->accounts->get_by_customer_id($payee_customers_data->customer_id);

		$customer_transaction_balance = $accounts_data->balance - $input['amount'];
		if ($customer_transaction_balance < 0) {
			$this->write->error("Insufficient Balance");
		}

		$accounts['balance'] = $customer_transaction_balance;
		$this->accounts->update($login_data->account_id, $accounts);

		$payee_transaction_balance 	= $payee_accounts_data->balance + $input['amount'];

		$accounts['balance'] = $payee_transaction_balance;
		$this->accounts->update($payee_accounts_data->account_id, $accounts);

		$referrence_code = md5(time()."trans");
		// Customer
		$transactions['customer_id']			= $login_data->customer_id;
		$transactions['amount']					= $input['amount'];
		$transactions['transaction_type_id']	= 1;
		$transactions['balance']				= $customer_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $input['description'];
		$this->transactions->insert($transactions);

		// Merchant
		$transactions['customer_id']			= $payee_accounts_data->customer_id;
		$transactions['amount']					= $input['amount'];
		$transactions['transaction_type_id']	= 2;
		$transactions['balance']				= $payee_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $input['description'];
		$this->transactions->insert($transactions);


		$feedback['data']['balance']			= "Rp".number_format($customer_transaction_balance, 0, '', '.');
		$this->write->feedback($feedback);
	}

	// -------- GET Barcode -------- //
	public function qrcode() {
		$login_data = $this->auth->login_key_v2();
    	$input = $this->auth->input_v2();

    	if(strpos($input->qr_code, "v") !== false) { // Voucher
    		$voucher_code = str_replace("v", "", $input->qr_code);
    		$this->load->model('deposit_vouchers');

    		$voucher = $this->deposit_vouchers->get_active_by_voucher_code($voucher_code);
    		if (!$voucher) {
    			$this->write->error("Kode voucher tidak aktif");
    		}

    		$account_data 			= $this->accounts->get_by_id($login_data->account_id);
			$customer_balance		= $account_data->balance + $voucher->amount;
			$accounts['balance']	= $customer_balance;
			$this->accounts->update($login_data->account_id, $accounts);

    		$payee_account_data		= $this->accounts->get_by_merchant_id($voucher->merchant_id);
			$payee_balance 			= $payee_account_data->balance - $voucher->amount;
			$accounts['balance'] 	= $payee_balance;
			$this->accounts->update($payee_account_data->account_id, $accounts);

			$referrence_code = md5(time()."voucher");
			// Customer
			$transactions['customer_id']			= $login_data->customer_id;
			$transactions['amount']					= $voucher->amount;
			$transactions['transaction_type_id']	= 5;
			$transactions['balance']				= $customer_balance;
			$transactions['transaction_date']		= date("Y-m-d H:i:S");
			$transactions['transaction_referrence']	= $referrence_code;
			$transactions['description']			= $voucher->description;
			$this->transactions->insert($transactions);

			// Merchant
			$transactions['customer_id']			= $payee_account_data->customer_id;
			$transactions['amount']					= $voucher->amount;
			$transactions['transaction_type_id']	= 6;
			$transactions['balance']				= $payee_balance;
			$transactions['transaction_date']		= date("Y-m-d H:i:S");
			$transactions['transaction_referrence']	= $referrence_code;
			$transactions['description']			= $voucher->description;
			$this->transactions->insert($transactions);


			$vouchers['is_used']		= "1";
			$vouchers['used_date']		= date("Y-m-d H:i:S");
			$vouchers['customer_id']	= $login_data->customer_id;
			$this->deposit_vouchers->update_by_id($voucher->voucher_id, $vouchers);
    		
    		$feedback['data']['type'] 				= 1;
			$feedback['data']['success_message']	= "Saldo Voucher Rp".number_format($voucher->amount, 0, '', '.').
									  " berhasil";
									  
			$this->write->feedback($feedback);
    	} elseif (strpos($input->qr_code, "p") !== false) { // Payment
    		// Search payment
    		// feedbackdetail payment
    	} elseif (strpos($input->qr_code, "t") !== false) { // Treasure Hunt
    		// Search treasure hunt last date
    		// check treasure number
    		// add database

    	} else { // Transfer
			$customers_data = $this->customers->get_by_search_id($input->qr_code);
	    	if ($customers_data) {
	    		$feedback['data']['type'] 		= 4;
	    		$feedback['data']['search_id'] 	= $customers_data->search_id;
	    		$this->write->feedback($feedback);
	    	}
	    	$this->write->error("QR Code tidak ditemukan");
    	}
	}

	public function check_user() {
		$login_data = $this->auth->login_key();
		
		$check = array(
			'search_id' => 'required');
		$input = $this->auth->input($check);

		$customers_data = $this->customers->get_by_search_id($input['search_id']);
    	if (!$customers_data) {
			$this->write->error("Customer tidak ditemukan");
		}

		$feedback['data']['type'] 		= 1;
		$feedback['data']['search_id'] 	= $customers_data->search_id;
		$feedback['data']['name'] 		= $customers_data->name;
		$feedback['data']['email'] 		= $customers_data->email;
		$feedback['data']['picture'] 	= $customers_data->picture_url;
		$this->write->feedback($feedback);
	}

	public function send_feedback() {
    	$login_data = $this->auth->login_key();

    	$check = array('feedback' => 'required');
    	$input = $this->auth->input($check);

    	$this->load->model("feedback");
		$feedback['date']			= date("Y-m-d H:i:s");
		$feedback['customer_id']	= $login_data->customer_id;
		$feedback['description']	= $input['feedback'];
		$this->feedback->insert($feedback);

		$feedback['error'] 			= false;
    	$this->write->feedback($feedback);
    }

    public function get_products() {
    	$login_data = $this->auth->login_key();

    	$this->load->model("products");

		$products_data = $this->products->get();
		if (!$products_data) {
			$this->write->error("Tidak ada product");
		}

		$i = 0;
		foreach ($products_data as $value) {
			$product[$i]['product_name']	= $value->product_name;
			$product[$i]['price']			= "Rp".number_format($value->price, 0, ',', '.');
			$product[$i]['image']			= $value->image;
			$product[$i]['product_key']		= $value->product_key;
			$i++;
		}

		$feedback['data']['products']	= $product;
		$this->write->feedback($feedback);
    }

    public function check_product() {
    	$login_data = $this->auth->login_key_v2();
    	$input = $this->auth->input_v2();

    	$this->load->model("products");

		$accounts_data = $this->accounts->get_by_id($login_data->account_id);
		if (!$accounts_data) {
			$this->write->error("Your account was deleted");
		}

		$products_data = $this->products->get_by_product_key($input->product_key);
		$result_balance = $accounts_data->balance - $products_data->price;

		if ($result_balance < 0) {
			$this->write->error("Saldo anda tidak mencukupi");
		}

		$customers_data = $this->customers->get_by_id($login_data->customer_id);

		$feedback['data']['message']	= "Anda dapat membeli produk ini";
		$feedback['data']['phone']		= $customers_data->phone;
		$this->write->feedback($feedback);
    }

    public function buy_product() {
    	$login_data = $this->auth->login_key_v2();
    	$input = $this->auth->input_v2();

    	$this->load->model("products");

    	$accounts_data = $this->accounts->match_pin_by_id($login_data->account_id, $input->pin);
    	if (!$accounts_data) {
    		$this->write->error("PIN Salah");
    	}

    	$products_data = $this->products->get_by_product_key($input->product_key);
    	if (!$products_data) {
			$this->write->error("Product key error");
		}

		$customer_transaction_balance = $accounts_data->balance - $products_data->price;
		if ($customer_transaction_balance < 0) {
			$this->write->error("Insufficient Balance");
		}

		$accounts['balance'] = $customer_transaction_balance;
		$this->accounts->update($login_data->account_id, $accounts);

		$merchants_account_data = $this->accounts->get_by_merchant_id($products_data->merchant_id);
		$merchant_transaction_balance = $merchants_account_data->balance + $products_data->price;

		$accounts['balance'] = $merchant_transaction_balance;
		$this->accounts->update($merchants_account_data->account_id, $accounts);

		$referrence_code = md5(time()."trans");
		$voucher_code = rand(1000, 9999).rand(1000, 9999);
		// Customer
		$transactions['customer_id']			= $login_data->customer_id;
		$transactions['amount']					= $products_data->price;
		$transactions['transaction_type_id']	= 4;
		$transactions['balance']				= $customer_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $products_data->product_name." - Kode Voucher ".$voucher_code;
		$this->transactions->insert($transactions);

		// Merchant
		$transactions['customer_id']			= $merchants_account_data->customer_id;
		$transactions['amount']					= $products_data->price;
		$transactions['transaction_type_id']	= 2;
		$transactions['balance']				= $merchant_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $products_data->product_name.". - Kode Voucher ".$voucher_code;
		$this->transactions->insert($transactions);

		$accounts_data = $this->accounts->get_by_id($login_data->account_id);

		$feedback['data']['balance'] 	= "Rp".number_format($accounts_data->balance, 0, ',', '.');
		$feedback['data']['message']	= $products_data->product_name." - Kode Voucher ".$voucher_code;
		$this->write->feedback($feedback);
    }

    // Pengaturan
    public function change_pin() {
    	$login_data = $this->auth->login_key_v2();
    	$input = $this->auth->input_v2();

    	$accounts_data = $this->accounts->match_pin_by_id($login_data->customer_id, $input->pin);
    	if (!$accounts_data) {
    		$this->write->error("PIN Lama Salah");
    	}

		$accounts['pin'] = md5($input->new_pin);
		$this->accounts->update($login_data->account_id, $accounts);

		$this->write->feedback();
    }

    public function get_settings() {

    }

}
