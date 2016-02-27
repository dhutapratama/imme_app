<?php

/*
$accounts['account_id']
$accounts['customer_id']
$accounts['account_number']
$accounts['pin_1']
$accounts['pin_2']
$accounts['pin_3']
$accounts['account_type_id']
$accounts['account_card_type_id']
$accounts['is_freezed']
$accounts['created_date']
$accounts['updated_date']
*/

class accounts extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_accounts', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_accounts');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_accounts')
				->where('account_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_accounts')
				->where('account_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('account_id', $id);
		$query = $this->db->update('imme_accounts', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_accounts')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_by_merchant_id($merchant_id = '')
	{
		$query = $this->db->select('*')->from('imme_accounts')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->result();
	}

	public function match_pin1_by_id($id = '', $pin_1)
	{
		$query = $this->db->select('*')->from('imme_accounts')
				->where('account_id', $id)
				->where('pin_1', $pin_1)
				->get();
		return $query->result();
	}
}