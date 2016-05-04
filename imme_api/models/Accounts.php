<?php

/*
$accounts['account_id']
$accounts['customer_id']
$accounts['merchant_id']
$accounts['account_number']
$accounts['pin']
$accounts['balance']
$accounts['in_transaction']
*/

class Accounts extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_accounts', $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get('imme_accounts');
		return $query->result();
	}

	public function delete($account_id = '') {
		$this->db->where('account_id', $account_id);
		$this->db->delete('imme_accounts');
	}

	public function get_by_id($account_id = '') {
		$query = $this->db->select('*')->from('imme_accounts')
				->where('account_id', $account_id)
				->get();
		return $query->row();
	}

	public function update($account_id = '', $data = array()) {
		$this->db->where('account_id', $account_id);
		$query = $this->db->update('imme_accounts', $data);
		return $query;
	}

	public function get_by_customer_id($customer_id = '') {
		$query = $this->db->select('*')->from('imme_accounts')
				->where('customer_id', $customer_id)
				->get();
		return $query->row();
	}

	public function get_by_merchant_id($merchant_id = '') {
		$query = $this->db->select('*')->from('imme_accounts')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->row();
	}

	public function match_pin_by_id($account_id = '', $pin) {
		$query = $this->db->select('*')->from('imme_accounts')
				->where('account_id', $account_id)
				->where('pin', md5($pin))
				->get();
		return $query->row();
	}
}