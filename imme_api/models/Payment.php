<?php

/*
$payment['payment_id']
$payment['merchant_id']
$payment['description']
$payment['amount']
$payment['date']
$payment['payment_status_id']
$payment['payment_key']
*/

class Payment extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$this->db->insert('imme_payment', $data);
	}

	public function get() {
		$query = $this->db->get('imme_payment');
		return $query->result();
	}

	public function get_by_id($payment_id = '') {
		$query = $this->db->select('*')->from('imme_payment')
				->where('payment_id', $payment_id)
				->get();
		return $query->row();
	}

	public function delete($payment_id = '') {
		$this->db->where('payment_id', $payment_id);
		$this->db->delete('imme_payment');
	}

	public function update($payment_id = '', $data = array()) {
		$this->db->where('payment_id', $payment_id);
		$this->db->update('imme_payment', $data);
	}

	public function get_by_payment_key($payment_key = '') {
		$query = $this->db->select('*')->from('imme_payment')
				->where('payment_key', $payment_key)
				->where('payment_status_id', "0")
				->get();
		return $query->row();
	}

	public function get_by_account_id($account_id = '') {
		$query = $this->db->select('*')->from('imme_payment')
				->where('account_id', $account_id)
				->where('payment_status_id', "0")
				->get();
		return $query->result();
	}
}