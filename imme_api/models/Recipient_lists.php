<?php

/*
$recipient_list['recipient_list_id']
$recipient_list['customer_id']
$recipient_list['search_id']
$recipient_list['add_date']
*/

class Recipient_lists extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_recipient_lists', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_recipient_lists');
		return $query->result();
	}

	public function delete($recipient_list_id = '')
	{
		$this->db->where('recipient_list_id', $recipient_list_id);
		$query = $this->db->delete('imme_recipient_lists');
		return $query;
	}

	public function select_by_id($recipient_list_id = '')
	{
		$query = $this->db->select('*')->from('imme_recipient_lists')
				->where('recipient_list_id', $recipient_list_id)
				->get();
		return $query->result();
	}

	public function update_by_id($recipient_list_id = '', $data = array())
	{
		$this->db->where('recipient_list_id', $recipient_list_id);
		$query = $this->db->update('imme_recipient_lists', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_recipient_lists')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}

	public function select_recipient_by_search_id($customer_id = '', $search_id = '')
	{
		$query = $this->db->select('*')->from('imme_recipient_lists')
				->where('customer_id', $customer_id)
				->where('search_id', $search_id)
				->get();
		return $query->result();
	}
}