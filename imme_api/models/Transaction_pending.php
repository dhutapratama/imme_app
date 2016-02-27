<?php

/*
$transaction_pending['pending_id']
$transaction_pending['transaction_code']
$transaction_pending['transaction_date']
$transaction_pending['last_processed_date']
$transaction_pending['customer_id']
$transaction_pending['account_id']
$transaction_pending['balance_id']
$transaction_pending['amount']
$transaction_pending['transaction_type_id']
$transaction_pending['transaction_status']
*/

class transaction_pending extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_transaction_pending', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_transaction_pending');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_transaction_pending')
				->where('transaction_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_transaction_pending')
				->where('transaction_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('transaction_id', $id);
		$query = $this->db->update('imme_transaction_pending', $data);
		return $query;
	}

	public function insert_batch($data = array())
	{
		$query = $this->db->insert_batch('imme_transaction_pending', $data);
		return $query;
	}
}