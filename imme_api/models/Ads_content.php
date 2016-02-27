<?php

/*
$ads_content['ads_content_id']
$ads_content['merchant_id']
$ads_content['notification_text']
$ads_content['image_url']
$ads_content['title']
$ads_content['content']
$ads_content['publishment_date']
$ads_content['target_user']
$ads_content['total_published']
*/

class Ads_content extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('ads_content', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('ads_content');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('ads_content')
				->where('ads_content_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('ads_content')
				->where('ads_content_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('ads_content_id', $id);
		$query = $this->db->update('ads_content', $data);
		return $query;
	}

	public function spread_ads_content($customer_id = '')
	{
		$query = $this->db->select('*')->from('ads_content')
				->where("customer_id", $customer_id)
				->get();
		return $query->result();
	}
}