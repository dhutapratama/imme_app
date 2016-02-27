<?php

/*
$account_type['account_type_id']
$account_type['account_type_name']
*/

class Account_types extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_account_types', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_account_types');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_account_types')
				->where('account_type_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_account_types')
				->where('account_type_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('account_type_id', $id);
		$query = $this->db->update('imme_account_types', $data);
		return $query;
	}
}