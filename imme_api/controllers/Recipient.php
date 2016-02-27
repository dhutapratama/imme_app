<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recipient extends CI_Controller {
	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'recipient_lists', 'customers'));
     }

	public function index()
	{
		$this->_error('114', 'Error request URL');
	}

	public function get_list() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
		} else {
			$this->_error('-', 'Get recipient list failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$recipient_list = $this->recipient_lists->select_by_customer_id($login_data[0]->customer_id);
		if (!$recipient_list) {
			$feedback['data_available']	= false;
		} else {
			$feedback['data_available']	= true;
			$i = 0;
			foreach ($recipient_list as $value) {
				$recipient = $this->customers->select_by_search_id($value->search_id);
				$feedback['recipient_list'][$i]['name']			= $recipient[0]->full_name;
				$feedback['recipient_list'][$i]['search_id']	= $value->search_id;
				// User Picture belum dimasukan
				$i++;
			}
		}

		$feedback['error'] 		= false;

		$this->_feedback($feedback);
	}

	public function search_recipient() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('recipient_email', 'Recipient Email', 'required');
		
		if ($this->form_validation->run()) {
			$session_key		= $this->input->post('session_key');
			$recipient_email	= $this->input->post('recipient_email');
		} else {
			$this->_error('-', 'Search recipient failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$recipient = $this->customers->select_by_email($recipient_email);
		if (!$recipient) {
			$this->_error('-', 'Recipient not found');
		}


		$feedback['error'] 		= false;
		$feedback['name']		= $recipient[0]->full_name;
		$feedback['search_id']	= $recipient[0]->search_id;

		$availability = $this->recipient_lists->select_recipient_by_search_id($login_data[0]->customer_id, $recipient[0]->search_id);
		if ($availability || ($recipient[0]->customer_id == $login_data[0]->customer_id)) {
			$feedback['already'] = true;
		} else {
			$feedback['already'] = false;
		}

		$this->_feedback($feedback);
	}

	public function add_recipient() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('search_id', 'Search ID', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$search_id		= $this->input->post('search_id');
		} else {
			$this->_error('-', 'Add recipient failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$recipient_lookup = $this->customers->select_by_search_id($search_id);
		
		if (!$recipient_lookup) {
			$this->_error('-', 'Invalid search id');
		}

		$availability = $this->recipient_lists->select_recipient_by_search_id($login_data[0]->customer_id, $search_id);
		if ($availability || ($recipient_lookup[0]->customer_id == $login_data[0]->customer_id)) {
			$this->_error('-', 'Cannot add this account');
		} else {
			$recipient_list['customer_id']	= $login_data[0]->customer_id;
			$recipient_list['search_id']	= $search_id;
			$recipient_list['add_date']		= date("Y-m-d H:i:s");
			$add_recipient = $this->recipient_lists->insert($recipient_list);
		}

		$feedback['error'] 		= false;
		$feedback['message']	= "Add success";

		$this->_feedback($feedback);
	}

	public function get_recipient() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('search_id', 'Search ID', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$search_id		= $this->input->post('search_id');
		} else {
			$this->_error('-', 'Add recipient failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$recipient_lookup = $this->customers->select_by_search_id($search_id);
		
		if (!$recipient_lookup) {
			$this->_error('-', 'Invalid search id');
		}

		$availability = $this->recipient_lists->select_recipient_by_search_id($login_data[0]->customer_id, $search_id);
		if ($availability || ($recipient_lookup[0]->customer_id == $login_data[0]->customer_id)) {
			$feedback['name']			= $recipient_lookup[0]->full_name;
			// Profile Picture
		} else {
			$this->_error('-', 'Cannot get account data');
		}

		$feedback['error'] 		= false;

		$this->_feedback($feedback);
	}

	public function remove_recipient() {
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required')
			->set_rules('search_id', 'Search ID', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
			$search_id		= $this->input->post('search_id');
		} else {
			$this->_error('-', 'Add recipient failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$recipient_lookup = $this->customers->select_by_search_id($search_id);
		
		if (!$recipient_lookup) {
			$this->_error('-', 'Invalid search id');
		}

		$availability = $this->recipient_lists->select_recipient_by_search_id($login_data[0]->customer_id, $search_id);
		if ($availability || ($recipient_lookup[0]->customer_id == $login_data[0]->customer_id)) {
			$this->recipient_lists->delete($availability[0]->recipient_list_id);
		} else {
			$this->_error('-', 'Cannot remove recipient');
		}

		$feedback['error'] 		= false;
		$feedback['message']	= "Remove account success";

		$this->_feedback($feedback);
	}

	//remove

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
