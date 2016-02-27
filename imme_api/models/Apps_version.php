<?php

/*
$apps_version['version_id']
$apps_version['client_version']
$apps_version['authentication_code']
*/

class Apps_version extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_apps_version', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_apps_version');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_apps_version')
				->where('version_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_apps_version')
				->where('version_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data)
	{
		$this->db->where('version_id', $id);
		$query = $this->db->update('imme_apps_version', $data);
		return $query;
	}

	public function check_authentication_code($version = '', $decoded_auth = '')
	{
		$query = $this->db->select('*')->from('imme_apps_version')
				->where('client_version', $version)
				->where('authentication_code', $decoded_auth)
				->get();
		return $query->result();
	}
}