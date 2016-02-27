<?php

/*
$city['city_id']
$city['city_name']
$city['province_id']
*/

class Web_city extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_city', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->select('*')->from('web_city')
				->order_by('city_name', 'asc')
				->get();
		return $query->result();
	}

	public function delete($city_id = '')
	{
		$query = $this->db->delete('web_city')
				->where('city_id', $city_id)
				->get();
		return $query;
	}

	public function select_by_id($city_id = '')
	{
		$query = $this->db->select('*')->from('web_city')
				->where('city_id', $city_id)
				->get();
		return $query->result();
	}

	public function update_by_city_id($city_id = '', $data = array())
	{
		$this->db->where('city_id', $city_id);
		$query = $this->db->update('web_city', $data);
		return $query;
	}
}