<?php

/*
$send['DestinationNumber']
$send['TextDecoded']
$send['CreatorID']
*/

class Gammu extends CI_Model {

	protected $gammu_db;

	public function __construct()
	{
		parent::__construct();
		$this->gammu_db = $this->load->database('gammu', true);
	}

	public function send($data = array())
	{
		$this->gammu_db->insert('outbox', $data);
		return $this->gammu_db->insert_id();
	}

	public function v2_send($data = array())
	{
		exec ('D:\gammu\bin\gammu-smsd-inject.exe -c D:\gammu\bin\smsdrc TEXT '
			.$data['DestinationNumber']
			.' -text "'
			.$data['TextDecoded']
			.'"', $execution_data);
		return $execution_data;
	}

	public function get_by_id($id = '')
	{
		$query = $this->gammu_db->select('*')->from('outbox')
				->where('ID', $id)
				->get();
		return $query->row();
	}

	public function get_by_prefix_not_processed($prefix) {
		$query = $this->gammu_db->select('*')->from('inbox')
				->like('TextDecoded', $prefix . "#")
				->where('Processed', "false")
				->get();
		return $query->result();
	}

	public function update($id = '', $data = array())
	{
		$this->gammu_db->where('ID', $id);
		$this->gammu_db->update('inbox', $data);
	}
}