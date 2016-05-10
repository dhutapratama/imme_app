<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V1_customer extends CI_Controller {
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
    	$check = array(
    		'id_token' => 'required',
    		'gcm_token' => 'required');
    	$input = $this->auth->input($check);

    	$url = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=".$input['id_token'];
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
			$customer['id_token']			= $input['id_token'];
			$customer['gcm_token'] 			= $input['gcm_token'];
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
			$accounts['balance']		= 20;
			$accounts['in_transaction']	= 0;
			$this->accounts->insert($accounts);
    	} else {
	    	$customer['gcm_token'] 			= $input['gcm_token'];
	    	$this->customers->update($customers_data->customer_id, $customer);
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
		$login_data = $this->auth->login_key();

		$customers_data = $this->customers->get_by_id($login_data->customer_id);
		if (!$customers_data) {
			$this->write->error("Mohon login kembali");
		}
		$accounts_data = $this->accounts->get_by_id($login_data->account_id);
		if (!$accounts_data) {
			$this->write->error("Your account was deleted");
		}

		$feedback['data']['balance'] 			= number_format($accounts_data->balance, 2, ',', '.')."Ҝ";
		$feedback['data']['search_id'] 			= $customers_data->search_id;
		$feedback['data']['search_id_image'] 	= "http://".$_SERVER['HTTP_HOST']."/search_id/".$customers_data->search_id.".png";
    	$feedback['data']['is_phone_verified']	= $customers_data->is_phone_verified;
		$this->write->feedback($feedback);
	}

	public function transaction_history() {
		$login_data = $this->auth->login_key();

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
				$transaction[$i]['picture']			= "http://imme.duckdns.org/default.png";
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
			$transaction[$i]['amount']			= number_format($value->amount, 0, '', '.')."Ҝ";
			$transaction[$i]['description']		= $value->description;
			$transaction[$i]['date']			= date("d M Y", strtotime($value->transaction_date));
			$transaction[$i]['referrence_code']	= $value->transaction_referrence;
			$i++;
		}

		$feedback['data']['transactions']	= $transaction;
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
			$payment[$i]['amount']			= number_format($value->amount, 0, '', '.')."Ҝ";
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
		$feedback['data']['amount']			= number_format($payment_data->amount, 0, '', '.')."Ҝ";
		$this->write->feedback($feedback);
	}

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

		$feedback['data']['balance']			= number_format($customer_transaction_balance, 0, '', '.')."Ҝ";

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


		$feedback['data']['balance']			= number_format($customer_transaction_balance, 0, '', '.')."Ҝ";
		$this->write->feedback($feedback);
	}

	// -------- GET Barcode -------- //
	public function qrcode() {
		$login_data = $this->auth->login_key();

    	$check = array(
    		'qrcode' => 'required');
    	$input = $this->auth->input($check);

    	$customers_data = $this->customers->get_by_search_id($input['qrcode']);
    	if ($customers_data) {
    		$feedback['data']['type'] 		= 1;
    		$feedback['data']['search_id'] 	= $customers_data->search_id;
    		$this->write->feedback($feedback);
    	}

    	$payment_data = $this->payment->get_by_payment_key($input['qrcode']);
    	if ($payment_data) {
    		$merchants_data = $this->merchants->get_by_id($payment_data->merchant_id);

    		$feedback['data']['type'] 			= 2;
    		$feedback['data']['payment_key']	= $payment_data->payment_key;
			$this->write->feedback($feedback);
    	}

    	$deposit_vouchers_data = $this->deposit_vouchers->get_by_vouchers_key($input['qrcode']);
    	if ($payment_data) {
    		$merchants_data = $this->merchants->get_by_id($payment_data->merchant_id);

    		$feedback['data']['type'] 			= 3;
			$feedback['data']['amount']			= number_format($payment_data->amount, 0, '', '.')."Ҝ";
			//balance add
			//total balance
			$this->write->feedback($feedback);
    	}

    	$this->write->error("QR Code tidak ditemukan");
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
			$product[$i]['price']			= number_format($value->price, 2, ',', '.')."Ҝ";
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
			$this->write->error("Anda tidak memiliki koin yang cukup");
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
		$transactions['description']			= $products_data->product_name." : ".$voucher_code;
		$this->transactions->insert($transactions);

		// Merchant
		$transactions['customer_id']			= $merchants_account_data->customer_id;
		$transactions['amount']					= $products_data->price;
		$transactions['transaction_type_id']	= 2;
		$transactions['balance']				= $merchant_transaction_balance;
		$transactions['transaction_date']		= date("Y-m-d H:i:S");
		$transactions['transaction_referrence']	= $referrence_code;
		$transactions['description']			= $products_data->product_name.". Kode Voucher : ".$voucher_code;
		$this->transactions->insert($transactions);

		$accounts_data = $this->accounts->get_by_id($login_data->account_id);

		$feedback['data']['balance'] 	= number_format($accounts_data->balance, 2, ',', '.')."Ҝ";
		$feedback['data']['message']	= $products_data->product_name." : ".$voucher_code;
		$this->write->feedback($feedback);
    }

}
