<?php

/*
$security_algorithm['algorithm_id']
$security_algorithm['customer_id']
$security_algorithm['account_id']
$security_algorithm['balance_id']
$security_algorithm['tfa_algorithm']
$security_algorithm['tba_algorithm']
$security_algorithm['tba_diff']
$security_algorithm['cba_algorithm']
$security_algorithm['cba_counter']
$security_algorithm['created_date']
$security_algorithm['updated_date']
*/

class Security_algorithm extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_security_algorithm', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_security_algorithm');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_security_algorithm')
				->where('algorithm_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_security_algorithm')
				->where('algorithm_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('algorithm_id', $id);
		$query = $this->db->update('imme_security_algorithm', $data);
		return $query;
	}

	public function select_by_customer_id($customer_id = '')
	{
		$query = $this->db->select('*')->from('imme_security_algorithm')
				->where('customer_id', $customer_id)
				->get();
		return $query->result();
	}
}