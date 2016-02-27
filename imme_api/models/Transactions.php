<?php

/*
$transactions['transaction_id']
$transactions['customer_id']
$transactions['account_id']
$transactions['balance_id']
$transactions['amount']
$transactions['transaction_type_id']
$transactions['balance']
$transactions['transaction_date']
$transactions['transaction_reference']
*/

class transactions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_transactions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_transactions');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_transactions')
				->where('transaction_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('transaction_id', $id);
		$query = $this->db->update('imme_transactions', $data);
		return $query;
	}

	public function insert_batch($data = array())
	{
		$query = $this->db->insert_batch('imme_transactions', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_by_merchant_id($merchant_id = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->result();
	}

	public function select_by_transaction_reference($transaction_reference = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_reference', $transaction_reference)
				->get();
		return $query->result();
	}

	public function select_vs_by_transaction_reference($transaction_reference = '', $customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('transaction_reference', $transaction_reference)
				->where('customer_id !=', $customer_id)
				->get();
		return $query->result();
	}

	public function select_last_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_transactions')
				->where('customer_id', $customer_id)
				->order_by('transaction_date', 'desc')
				->limit(30)
				->get();
		return $query->result();
	}
}