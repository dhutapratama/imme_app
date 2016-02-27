<?php

/*
$receive_sessions['receive_session_id']		= 
$receive_sessions['transaction_code']		= 
$receive_sessions['apply_code']				= 
$receive_sessions['customer_id']			= 
$receive_sessions['merchant_id']			= 
$receive_sessions['account_id']				= 
$receive_sessions['balance_id']				= 
$receive_sessions['amount']					= 
$receive_sessions['transaction_type_id']	= 
$receive_sessions['created_date']			= 
$receive_sessions['expired_date']			= 
$receive_sessions['payer_customer_id']		= 
$receive_sessions['transaction_status_id']	= 
$receive_sessions['is_simulation']			= 
*/

class Receive_sessions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_receive_sessions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_receive_sessions');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_receive_sessions')
				->where('receive_session_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('receive_session_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('receive_session_id', $id);
		$query = $this->db->update('imme_receive_sessions', $data);
		return $query;
	}
	
	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_by_transaction_code($transaction_code = '')
	{
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('transaction_code', $transaction_code)
				->where('transaction_status_id', 1)
				->get();
		return $query->result();
	}

	public function select_new_receive_by_transaction_code($transaction_code = '')
	{
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('transaction_code', $transaction_code)
				->where('transaction_status_id', 1)
				->get();
		return $query->result();
	}

	public function select_by_payer_apply_code($apply_code = '', $customer_id)
	{
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('apply_code', $apply_code)
				->where('payer_customer_id', $customer_id)
				->where('transaction_status_id', 6)
				->get();
		return $query->result();
	}

	public function check_scanned($balance_id, $transaction_code) {
		$query = $this->db->select('*')->from('imme_receive_sessions')
				->where('balance_id', $balance_id)
				->where('transaction_code', $transaction_code)
				->get();
		$result = $query->result();

		return $result;
	}

	public function delete_by_transaction_code($transaction_code = '')
	{
		$this->db->where('transaction_code', $transaction_code);
		$query = $this->db->delete('imme_receive_sessions');
		return $query;
	}
}