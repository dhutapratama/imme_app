<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'imme_feedback'));
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	public function send() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('message', 'Message', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$message		= $this->input->post('message');
		} else {
			$this->_error('141', 'Send feedback failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$feedback['date']			= date("Y-m-d H:i:s");
		$feedback['custommer_id']	= $login_data[0]->customer_id;
		$feedback['message']		= $message;
		$this->imme_feedback->insert($feedback);

		unset($feedback);

		$feedback['error'] 		= false;
		$feedback['message']	= "Send feedback success";

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
