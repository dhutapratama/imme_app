<?php

/*
$notification['notification_id']
$notification['customer_id']
$notification['notification_text']
$notification['created_date']
$notification['sent_date']
$notification['is_sent']
*/

class Notifications extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_notifications', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_notifications');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_notifications')
				->where('notification_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_notifications')
				->where('notification_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('notification_id', $id);
		$query = $this->db->update('imme_notifications', $data);
		return $query;
	}

	public function check_notification($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_notifications')
				->where("customer_id", $customer_id)
				->where('is_sent', 0)
				->get();
		return $query->result();
	}
}