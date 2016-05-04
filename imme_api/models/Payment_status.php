<?php

/*
$payment_status['payment_status_id']
$payment_status['name']
*/

class Payment_status extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$this->db->insert('imme_payment_status', $data);
	}

	public function get() {
		$query = $this->db->get('imme_payment_status');
		return $query->result();
	}

	public function get_by_id($payment_status_id = '') {
		$query = $this->db->select('*')->from('imme_payment_status')
				->where('payment_status_id', $payment_status_id)
				->get();
		return $query->row();
	}

	public function delete($payment_status_id = '') {
		$this->db->where('payment_status_id', $payment_status_id);
		$this->db->delete('imme_payment_status');
	}

	public function update($payment_status_id = '', $data = array()) {
		$this->db->where('payment_status_id', $payment_status_id);
		$this->db->update('imme_payment_status', $data);
	}
}