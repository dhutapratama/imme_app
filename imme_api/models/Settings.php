<?php

/*
$settings['setting_id']
$settings['customer_id']
$settings['track_transaction']
$settings['color_security']
*/

class settings extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_settings', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_settings');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_settings')
				->where('setting_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_settings')
				->where('setting_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('setting_id', $id);
		$query = $this->db->update('imme_settings', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_settings')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function update_by_customer_id($customer_id = '', $data = array())
	{
		$this->db->where('customer_id', $customer_id);
		$query = $this->db->update('imme_settings', $data);
		return $query;
	}
}