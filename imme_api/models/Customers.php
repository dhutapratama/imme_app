<?php

/*
$customers['customer_id']
$customers['google_user_id']
$customers['name']
$customers['email']
$customers['phone']
$customers['picture_url']
$customers['email_verify_code']
$customers['phone_verify_code']
$customers['is_email_verified']
$customers['is_phone_verified']
$customers['is_blocked']
$customers['created_date']
$customers['referral_code']
$customers['search_id']
$customers['id_token']
$customers['gcm_token']
*/

class Customers extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert('imme_customers', $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get('imme_customers');
		return $query->result();
	}

	public function delete($customer_id = '') {
		$this->db->where('customer_id', $customer_id);
		$this->db->delete('imme_customers');
	}

	public function get_by_id($customer_id = '') {
		$query = $this->db->select('*')->from('imme_customers')
				->where('customer_id', $customer_id)
				->get();
		return $query->row();
	}

	public function update($customer_id = '', $data = array())
	{
		$this->db->where('customer_id', $customer_id);
		$this->db->update('imme_customers', $data);
	}

	public function get_by_email($email = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('email', $email)
				->get();
		return $query->row();
	}

	public function get_by_search_id($search_id = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('search_id', $search_id)
				->get();
		return $query->row();
	}

	public function get_by_google_user_id($google_user_id = '')
	{
		$query = $this->db->select('*')->from('imme_customers')
				->where('google_user_id', $google_user_id)
				->get();
		return $query->row();
	}
}