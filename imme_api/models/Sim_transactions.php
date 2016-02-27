<?php

/*
$sim_transactions['transaction_id']
$sim_transactions['customer_id']
$sim_transactions['account_id']
$sim_transactions['balance_id']
$sim_transactions['amount']
$sim_transactions['transaction_type_id']
$sim_transactions['balance']
$sim_transactions['transaction_date']
$sim_transactions['transaction_reference']
*/

class sim_transactions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_sim_transactions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_sim_transactions');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_sim_transactions')
				->where('transaction_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_sim_transactions')
				->where('transaction_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('transaction_id', $id);
		$query = $this->db->update('imme_sim_transactions', $data);
		return $query;
	}

	public function insert_batch($data = array())
	{
		$query = $this->db->insert_batch('imme_sim_transactions', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_sim_transactions')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_by_merchant_id($merchant_id = '')
	{
		$query = $this->db->select('*')->from('imme_sim_transactions')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->result();
	}

	public function select_by_transaction_reference($transaction_reference = '')
	{
		$query = $this->db->select('*')->from('imme_sim_transactions')
				->where('transaction_reference', $transaction_reference)
				->get();
		return $query->result();
	}
}