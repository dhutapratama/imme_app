<?php

/*
$device['device_id']
$device['physical_device_id']
$device['device_type']
$device['device_ip']
$device['public_ip']
$device['client_version']
$device['registered_date']
$device['device_date']
$device['different_date']
$device['user_agent']
*/

class Devices extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_devices', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_devices');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_devices')
				->where('device_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_devices')
				->where('device_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('device_id', $id);
		$query = $this->db->update('imme_devices', $data);
		return $query;
	}

	public function check_device($physical_device_id = '')
	{
		$query = $this->db->select('*')->from('imme_devices')
				->where('physical_device_id', $physical_device_id)
				->get();
		return $query->result();
	}

	public function update_by_physical($physical_id = '', $data = array())
	{
		$this->db->where('physical_device_id', $physical_id);
		$query = $this->db->update('imme_devices', $data);
		return $query;
	}

	public function count_devices()
	{
		$query = $this->db->count_all_results('imme_devices');
		return $query;
	}

	public function check_user_agent($user_agent = '')
	{
		$query = $this->db->select('*')->from('imme_devices')
				->where('user_agent', $user_agent)
				->get();
		return $query->result();
	}
}