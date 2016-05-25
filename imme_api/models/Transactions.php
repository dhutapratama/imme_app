<?php

/*
$transactions['transaction_id']
$transactions['customer_id']
$transactions['amount']
$transactions['transaction_type_id']
$transactions['balance']
$transactions['transaction_date']
$transactions['transaction_reference']
*/

class transactions extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_transactions', $data);
		return $query;
	}

	public function select() {
		$query = $this->db->get('imme_transactions');
		return $query->result();
	}

	public function delete($id = '') {
		$query = $this->db->delete('imme_transactions')
				->where('transaction_id', $id);
		return $query;
	}

	public function get_by_id($id = '') {
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_id', $id)
				->get();
		return $query->row();
	}

	public function update($id = '', $data = array()) {
		$this->db->where('transaction_id', $id);
		$query = $this->db->update('imme_transactions', $data);
		return $query;
	}

	public function get_by_customer_id($customer_id = '') {
		$query = $this->db->select('*')->from('imme_transactions')
				->where('customer_id', $customer_id)
				->where('transaction_type_id !=', 8)
				->order_by("transaction_id", "desc")
				->get();
		return $query->result();
	}

	public function get_vs_transaction($transaction_reference = '', $customer_id = '') {
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_referrence', $transaction_reference)
				->where('customer_id !=', $customer_id)
				->get();
		return $query->row();
	}

	public function get_my_transaction($transaction_reference = '', $customer_id = '') {
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_referrence', $transaction_reference)
				->where('customer_id', $customer_id)
				->get();
		return $query->row();
	}

	public function get_by_reference($transaction_referrence = '') {
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_referrence', $transaction_referrence)
				->get();
		return $query->result();
	}
}