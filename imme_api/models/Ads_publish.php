<?php

/*
$ads_publish['ads_publish_id']
$ads_publish['ads_content_id']
$ads_publish['customer_id']
$ads_publish['created_date']
$ads_publish['published_date']
$ads_publish['is_published']
*/

class Ads_publish extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('ads_publish', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('ads_publish');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('ads_publish')
				->where('ads_publish_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('ads_publish')
				->where('ads_publish_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('ads_publish_id', $id);
		$query = $this->db->update('ads_publish', $data);
		return $query;
	}

	public function check_ads_publish($customer_id = '')
	{
		$query = $this->db->select('*')->from('ads_publish')
				->where("customer_id", $customer_id)
				->where('is_published', 0)
				->limit(1)
				->get();
		return $query->result();
	}
}