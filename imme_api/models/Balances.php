<?php

/*
$balances['balance_id']
$balances['customer_id']
$balances['account_id']
$balances['balance']
$balances['updated_date']
$balances['in_transaction']
*/

class Balances extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_balances', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_balances');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_balances')
				->where('balance_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_balances')
				->where('balance_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('balance_id', $id);
		$query = $this->db->update('imme_balances', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_balances')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_by_merchant_id($merchant_id = '')
	{
		$query = $this->db->select('*')->from('imme_balances')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->result();
	}

	public function in_transaction($sender_id = '', $receiver_id = '') {
		$status['in_transaction'] = '1';
		$this->update_by_id($sender_id, $status);
		if ($receiver_id != '') {
			$this->update_by_id($receiver_id, $status);
		}
		return true;
	}

	public function close_transaction($sender_id = '', $receiver_id = '') {
		$status['in_transaction'] = '0';
		$this->update_by_id($sender_id, $status);
		if ($receiver_id != '') {
			$this->update_by_id($receiver_id, $status);
		}
		return true;
	}
}