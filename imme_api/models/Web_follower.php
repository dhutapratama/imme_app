<?php

/*
$follower['follower_id']
$follower['email']
$follower['created_date']
*/

class Web_follower extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_follower', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->select('*')->from('web_follower')
				->get();
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('web_follower')
				->where('follower_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('web_follower')
				->where('follower_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_follower_id($id = '', $data = array())
	{
		$this->db->where('follower_id', $id);
		$query = $this->db->update('web_follower', $data);
		return $query;
	}
}