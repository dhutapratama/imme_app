<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traction extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library(array("form_validation", "email"));
		$this->load->model(array("web_tractions", "web_city_vote", "web_city", "web_follower", "file_versions", "web_download_url", "web_question"));
		$this->load->helper(array("cookie", "url"));
	}

	public function index()
	{
		$feedback['error']		= true;
		$feedback['message']	= 'You are not allowed accessing this page. Go back bro!';
		$this->_feedback($feedback);
	}

	public function download()
	{
		$this->form_validation
			->set_rules('email', 'Email', 'required|valid_email'); // is_unique[imme_customers.email]

		if ($this->form_validation->run())
        {
        	$tractions['email'] = $this->input->post("email");
        	$tractions['is_download'] = 1;
        	$this->web_tractions->update_by_session_id(get_cookie("imme_traction_id"), $tractions);

        	$file_versions	= $this->file_versions->select();

        	$download_url['download_code'] 	= md5(time().rand(1000, 9999));
			$download_url['session_id']		= get_cookie('imme_traction_id');
			$download_url['version_id']		= $file_versions[0]->version_id;
			$download_url['created_date']	= date("Y-m-d H:i:s");
			$this->web_download_url->insert($download_url);
			
			$output['download_code']	= $download_url['download_code'];
			$output['version_name']		= $file_versions[0]->version_name;

        	$send_to = $this->input->post("email");
			$subject = 'Download IMME Android';
			$message = $this->load->view("email/download", $output, TRUE);

			$this->email->send_email($send_to, $subject, $message);

			$feedback['error']		= false;
			$feedback['message']	= 'We\'ve send download link to your email.';
			$this->_feedback($feedback);
		} else {
			$feedback['error']		= true;
			$feedback['message']	= 'Please input valid email';
			$this->_feedback($feedback);
		}
	}

	public function support_city() {
		$session_id = get_cookie("imme_traction_id");

		if ($this->input->post('email')) {
			$tractions['email'] = $this->input->post('email');
			$this->web_tractions->update_by_session_id($session_id, $tractions);
		}

		$session = $this->web_tractions->select_by_session_id($session_id);
		if ($session[0]->email) {
				$this->form_validation
				->set_rules('city_id', 'City', 'required|numeric');

			if ($this->form_validation->run())
	        {
				$tractions['is_support_city'] = 1;
		        $this->web_tractions->update_by_session_id($session_id, $tractions);

		        $city_vote['email']		= $session[0]->email;
				$city_vote['city_id']	= $this->input->post('city_id');
				$city_vote['date']		= date("Y-m-d H:i:s");
				$this->web_city_vote->insert($city_vote);

				$city = $this->web_city->select_by_id($city_vote['city_id']);

				$feedback['error']		= false;
				$feedback['message']	= $city[0]->city_name;
				$this->_feedback($feedback);
			} else {
				$feedback['error']		= true;
				$feedback['message']	= 'You are not allowed accessing this page. Taubat bro!';

				$this->_feedback($feedback);
			}
		} else {
			$feedback['error']		= true;
			$feedback['message']	= 'Need email verification';
			$feedback['get']		= 'email';
			$this->_feedback($feedback);
		}
	}

	public function follow() {
		$session_id = get_cookie("imme_traction_id");
		$session = $this->web_tractions->select_by_session_id($session_id);

		$this->form_validation
				->set_rules('email', 'Email', 'required|valid_email');

		if ($this->form_validation->run())
        {
        	if (!$session[0]->email) 
        	{
				$tractions['email']	= $this->input->post('email');
			    $this->web_tractions->update_by_session_id($session_id, $tractions);
			}

        	$follower['email']			= $this->input->post('email');
			$follower['created_date']	= date("Y-m-d H:i:s");
			$this->web_follower->insert($follower);

			$feedback['error']		= false;
			$feedback['message']	= $this->input->post('email');
			$this->_feedback($feedback);
		} else {
			$feedback['error']		= true;
			$feedback['message']	= 'Please send valid email.';

			$this->_feedback($feedback);
		}
	}

	public function registration()
	{
		$this->form_validation
			->set_rules('email', 'Email', 'required|valid_email'); // is_unique[imme_customers.email]

		if ($this->form_validation->run())
        {
        	$send_to = $this->input->post("email");
			$subject = 'IMME Account';
			$message = $this->load->view("email/registration", "", TRUE);

			$this->email->send_email($send_to, $subject, $message);

			$feedback['error']		= false;
			$feedback['message']	= 'We\'ve send registration detail to your email.';
			$this->_feedback($feedback);
		} else {
			$feedback['error']		= true;
			$feedback['message']	= 'Please input valid email';
			$this->_feedback($feedback);
		}
	}

	public function question()
	{
		$session_id = get_cookie("imme_traction_id");

		if ($this->input->post('email')) {
			$tractions['email'] = $this->input->post('email');
			$this->web_tractions->update_by_session_id($session_id, $tractions);
		}

		$session = $this->web_tractions->select_by_session_id($session_id);
		if ($session[0]->email) {
			$this->form_validation
				->set_rules('question', 'Question', 'required');

			if ($this->form_validation->run())
	        {
				$tractions['is_support_city'] = 1;
		        $this->web_tractions->update_by_session_id($session_id, $tractions);

				$question['email']			= $session[0]->email;
				$question['message']		= $this->input->post("question");
				$question['created_date']	= date("Y-m-d H:i:s");
				$this->web_question->insert($question);

				$output['question'] = $question['message'];

				$send_to = $this->input->post("email");
				$subject = 'Question';
				$message = $this->load->view("email/question", $output, TRUE);
				$this->email->send_email($send_to, $subject, $message);

				$feedback['error']		= false;
				$feedback['message']	= "Thank You! Your question is now on our desk.";
				$this->_feedback($feedback);
			} else {
				$feedback['error']		= true;
				$feedback['message']	= 'You are not allowed accessing this page. Taubat bro!';

				$this->_feedback($feedback);
			}
		} else {
			$feedback['error']		= true;
			$feedback['message']	= 'Need email verification';
			$feedback['get']		= 'email';
			$this->_feedback($feedback);
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
