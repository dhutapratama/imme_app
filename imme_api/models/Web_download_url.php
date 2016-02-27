<?php

/*
$download_url['download_code']
$download_url['session_id']
$download_url['version_id']
$download_url['created_date']
$download_url['download_count']
$download_url['last_download_date']
*/

class web_download_url extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_download_url', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->select('*')->from('web_download_url')
				->get();
		return $query->result();
	}

	public function delete($download_code = '')
	{
		$query = $this->db->delete('web_download_url')
				->where('download_code', $download_code)
				->get();
		return $query;
	}

	public function select_by_download_code($download_code = '')
	{
		$query = $this->db->select('*')->from('web_download_url')
				->where('download_code', $download_code)
				->get();
		return $query->result();
	}

	public function update_by_download_code($download_code = '', $data = array())
	{
		$this->db->where('download_code', $download_code);
		$query = $this->db->update('web_download_url', $data);
		return $query;
	}
}