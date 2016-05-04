<?php

/*
$transaction_type['transaction_type_id']
$transaction_type['transaction_type_name']
*/

class Transaction_types extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_transaction_types', $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get('imme_transaction_types');
		return $query->result();
	}

	public function delete($transaction_type_id = '')
	{
		$this->db->where('transaction_type_id', $transaction_type_id);
		$this->db->delete('imme_transaction_types');
	}

	public function get_by_id($transaction_type_id = '')
	{
		$query = $this->db->select('*')->from('imme_transaction_types')
				->where('transaction_type_id', $transaction_type_id)
				->get();
		return $query->row();
	}

	public function update($transaction_type_id = '', $data = array()) {
		$this->db->where('transaction_type_id', $transaction_type_id);
		$this->db->update('imme_transaction_types', $data);
	}
}