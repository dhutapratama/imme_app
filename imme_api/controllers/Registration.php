<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Untuk meregistrasikan customer
 * 
 * POST Param :
 * [fullname]
 * [email]
 * [password]
 * [confirmp]
 * [phone]
 *
 * Feedback :
 * [error]
 * [message]
 */

class Registration extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation','email'));
		$this->load->helper(array('form', 'url'));
		$this->load->model(array('customers', 'accounts', 'balances', 'security_algorithm', 'settings'));
     }

	public function index()
	{
		$this->form_validation
			->set_rules('fullname', 'Full Name', 'required')
			->set_rules('email', 'Email', 'required|is_unique[imme_customers.email]|valid_email')  // Must to be md5 on beta
			->set_rules('password', 'Password', 'required|min_length[8]') // Must to be md5 on beta
			->set_rules('confirmp', 'Password Confirmation', 'required|matches[password]') // Must to be md5 on beta
			->set_rules('phone', 'Phone Number', 'required|numeric');

		$customer_data = $this->customers->select_by_email($this->input->post('email'));
		if ($customer_data) {
			$this->_error('116', 'You cant use this email');
		}

		if ($this->form_validation->run())
        {
            // Registering Customer Data
            $referral_code = substr(strtolower(str_replace(" ", "", $this->input->post('fullname'))), 0, 3) . rand(100, 999);
			$customer['full_name']					= $this->input->post('fullname');
			$customer['email']						= $this->input->post('email');
			$customer['password']					= md5($this->input->post('password'));
			$customer['phone_number']				= $this->input->post('phone');
			$customer['email_verification_code']	= md5(rand(1000000, 9999999));
			$customer['phone_verification_code']	= rand(10000, 99999);
			$customer['customer_type_id']			= '1';
			$customer['created_date']				= date('Y-m-d H:i:s');
			$customer['referral_code']				= $referral_code;
			$customer['search_id']					= md5(time().rand(1000, 9999));
			$this->customers->insert($customer);

			$customer_id = $this->db->insert_id();
			$pin_1	= rand(1000,9999);
			$pin_2	= rand(100000,999999);
			$pin_3	= rand(10000000,99999999);

			$account['customer_id']				= $customer_id;
			$account['account_number']			= rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999); // Must duplicate check in beta
			$account['pin_1']					= md5($pin_1);
			$account['pin_2']					= md5($pin_2);
			$account['pin_3']					= md5($pin_3);
			$account['account_type_id']			= '1'; // Parent Account
			$account['account_card_type_id']	= '1'; // Basic Card
			$account['created_date']			= $customer['created_date'];
			$this->accounts->insert($account);

			$account_id = $this->db->insert_id();

			$balance['customer_id']	= $customer_id;
			$balance['account_id']	= $account_id;
			$balance['balance']		= '0'; // Initial Balance
			$this->balances->insert($balance);

			$balance_id = $this->db->insert_id();

			$security['customer_id']		= $customer_id;
			$security['account_id']			= $account_id;
			$security['balance_id']			= $balance_id;
			$security['imme_algorithm']		= rand(1111111111,9999999999);

			$tba_algorithm = '';
			$cba_algorithm = '';
			for ($i = 0; $i < 5; $i++) { 
				$tba_algorithm = $tba_algorithm.rand(1,3);
				$cba_algorithm = $cba_algorithm.rand(1,2);
			}

			//$device = $this->devices->check_user_agent($_SERVER['HTTP_USER_AGENT']);
			$security['tba_algorithm']		= $tba_algorithm.rand(11111, 99999);
			$security['tba_diff']			= '0'; //$device[0]->different_date;
			$security['cba_algorithm']		= $cba_algorithm.rand(11111, 99999);
			$security['cba_counter']		= '1';
			$security['created_date']		= $customer['created_date'];

			$this->security_algorithm->insert($security);

			$settings['customer_id']		= $customer_id;
			$settings['track_transaction']	= '0';
			$settings['color_security']		= '0';
			$this->settings->insert($settings);

			// Verification email 1
			$subject = 'Confirmation Data';
			$message = 'Name : '.$customer['full_name'].'<br />';
			$message .= 'Your Account Number : '.$account['account_number'].'<br />';
			$message .= 'Your PIN 1 : '.$pin_1.'<br />';
			$message .= 'Your PIN 2 : '.$pin_2.'<br />';
			$message .= 'http://verify.imme.asia/'.$customer['email_verification_code'].'<br />';

			if ($_SERVER['HTTP_HOST'] !== 'api1.imme.asia') {
				$this->load->config('imme_email');
				$url = $this->config->item('email_server');
				$fields = array(
					'email' => urlencode($customer['email']),
					'subject' => urlencode($subject),
					'message' => urlencode($message)
				);

				//url-ify the data for the POST
				$fields_string = '';
				foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
				rtrim($fields_string, '&');

				//open connection
				$ch = curl_init();

				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				//execute post
				curl_exec($ch);

				//close connection
				curl_close($ch);
			} else {
				$this->email->send_email($customer['email'], $subject, $message);
			}
			
			// Email 2
			// $pin_3
			$subject = 'PIN 3 Security Agreement';
			$message = 'Your PIN 3 : '.$pin_3.'<br />';

			if ($_SERVER['HTTP_HOST'] !== 'api1.imme.asia') {
				$this->load->config('imme_email');
				$url = $this->config->item('email_server');
				$fields = array(
					'email' => urlencode($customer['email']),
					'subject' => urlencode($subject),
					'message' => urlencode($message)
				);

				//url-ify the data for the POST
				$fields_string = '';
				foreach($fields as $key=>$value) 
				{ $fields_string .= $key.'='.$value.'&'; }

				rtrim($fields_string, '&');

				//open connection
				$ch = curl_init();

				//set the url, number of POST vars, POST data
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, count($fields));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);

				//execute post
				curl_exec($ch);

				//close connection
				curl_close($ch);
			} else {
				$this->email->send_email($customer['email'], $subject, $message);
			}
			
			// Verification phone
			// $customer['phone_verification_code']		

			$feedback['error']		= false;
			$feedback['message']	= 'You are registered on IMME account';
			$this->_feedback($feedback);
        }
        else
        {
        	$this->_error('102', 'Registration new customer failed'.validation_errors(" ",","));
        }
	}

	public function please_send() {
		if ($_SERVER['HTTP_HOST'] == 'api1.imme.asia') {
			$email		= $this->input->post('email');
			$subject	= $this->input->post('subject');
			$message	= $this->input->post('message');

			$this->form_validation
				->set_rules('email', 'Email', 'required|valid_email')
				->set_rules('subject', 'Subject', 'required')
				->set_rules('message', 'Message', 'required');

			if ($this->form_validation->run())
        	{
				$this->email->send_email($email, $subject, $message);

				$feedback['error']		= false;
				$feedback['message']	= 'Email Sent';
				$feedback['email_address']	= $email;
				$feedback['email_subject']	= $subject;
				$feedback['email_message']	= $message;

				//$this->_feedback($feedback);
        	} else {
				$this->_error('132', 'Email parameter is not complete');
        	}

		} else {
			$this->_error('131', 'Unknown email server');
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
