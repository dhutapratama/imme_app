<?php

/*
$file_version['version_id']
$file_version['version_name']
$file_version['file_name']
$file_version['created_date']
$file_version['total_download']
*/

class File_versions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_file_versions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->select('*')->from('imme_file_versions')
				->order_by('version_id', 'desc')
				->get();
		return $query->result();
	}

	public function delete($version_id = '')
	{
		$query = $this->db->delete('imme_file_versions')
				->where('version_id', $version_id)
				->get();
		return $query;
	}

	public function select_by_id($version_id = '')
	{
		$query = $this->db->select('*')->from('imme_file_versions')
				->where('version_id', $version_id)
				->get();
		return $query->result();
	}

	public function update_by_id($version_id = '', $data = array())
	{
		$this->db->where('version_id', $version_id);
		$query = $this->db->update('imme_file_versions', $data);
		return $query;
	}
}