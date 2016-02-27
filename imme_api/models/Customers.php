<?php

/*
$customers['customer_id']
$customers['full_name']
$customers['email']
$customers['password']
$customers['idcard_number']
$customers['idcard_type_id']
$customers['id_card_image']
$customers['address']
$customers['city']
$customers['province']
$customers['country']
$customers['postal_code']
$customers['phone_number']
$customers['customer_type_id']
$customers['picture_url']
$customers['picture_path']
$customers['email_verification_code']
$customers['phone_verification_code']
$customers['is_email_verified']
$customers['is_phone_verified']
$customers['is_blocked']
$customers['created_date']
$customers['use_simulation']
$customers['referral_code']
$customers['search_id']
*/

class Customers extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_customers', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_customers');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_customers')
				->where('customer_id', $id)
				->get();
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('customer_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('customer_id', $id);
		$query = $this->db->update('imme_customers', $data);
		return $query;
	}

	public function select_by_email($email = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('email', $email)
				->get();
		return $query->result();
	}

	public function select_by_search_id($search_id = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('search_id', $search_id)
				->get();
		return $query->result();
	}
}