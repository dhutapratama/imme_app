<?php

/*
$merchant['merchant_id']
$merchant['merchant_name']
$merchant['longitude']
$merchant['latitude']
*/

class Merchants extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_merchants', $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get('imme_merchants');
		return $query->result();
	}

	public function delete($merchant_id = '') {
		$this->db->where('merchant_id', $merchant_id);
		$this->db->delete('imme_merchants');
	}

	public function get_by_id($merchant_id = '') {
		$query = $this->db->select('*')->from('imme_merchants')
				->where('merchant_id', $merchant_id)
				->get();
		return $query->row();
	}

	public function update_by_id($merchant_id = '', $data = array()) {
		$this->db->where('merchant_id', $merchant_id);
		$query = $this->db->update('imme_merchants', $data);
		return $query;
	}

	public function get_by_merchant_key($merchant_key = '')
	{
		$query = $this->db->select('*')->from('imme_merchants')
				->where('merchant_key', $merchant_key)
				->get();
		return $query->row();
	}
}