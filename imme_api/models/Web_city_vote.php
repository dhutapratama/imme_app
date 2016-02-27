<?php

/*
$city_vote['email']
$city_vote['city_id']
$city_vote['date']
*/

class web_city_vote extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_city_vote', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('web_city_vote');
		return $query->result();
	}

	public function delete($email = '')
	{
		$query = $this->db->delete('web_city_vote')
				->where('email', $email)
				->get();
		return $query;
	}

	public function select_by_email($email = '')
	{
		$query = $this->db->select('*')->from('web_city_vote')
				->where('email', $email)
				->order_by('date', 'desc')
				->get();
		return $query->result();
	}

	public function update_by_email($email = '', $data = array())
	{
		$this->db->where('email', $email);
		$query = $this->db->update('web_city_vote', $data);
		return $query;
	}
}