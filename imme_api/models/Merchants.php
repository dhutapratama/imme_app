<?php

/*
$merchant['merchant_id']
$merchant['merchant_name']
$merchant['longitude']
$merchant['latitude']
*/

class Merchants extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_merchants', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_merchants');
		return $query->result();
	}

	public function delete($merchant_id = '')
	{
		$query = $this->db->delete('imme_merchants')
				->where('merchant_id', $merchant_id)
				->get();
		return $query;
	}

	public function select_by_id($merchant_id = '')
	{
		$query = $this->db->select('*')->from('imme_merchants')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->result();
	}

	public function update_by_id($merchant_id = '', $data = array())
	{
		$this->db->where('merchant_id', $merchant_id);
		$query = $this->db->update('imme_merchants', $data);
		return $query;
	}
}