<?php

/*
$login['login_key']				= '';
$login['customer_id']			= '';
$login['account_id']			= '';
$login['merchant_id']			= '';
*/

class Login extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_login', $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get('imme_login');
		return $query->result();
	}

	public function delete($login_key = '') {
		$this->db->where('login_key', $login_key);
		$this->db->delete('imme_login');
	}

	public function get_by_login_key($login_key = '') {
		$query = $this->db->select('*')->from('imme_login')
				->where('login_key', $login_key)
				->get();
		return $query->row();
	}
}