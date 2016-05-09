<?php

/*
$sms['sms_id']
$sms['destination']
$sms['text']
$sms['status']
*/

class sms extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$this->db->insert('imme_sms', $data);
		return $this->db->insert_id();
	}

	public function get() {
		$query = $this->db->get('imme_sms');
		return $query->result();
	}

	public function get_by_id($sms_id = '') {
		$query = $this->db->select('*')->from('imme_sms')
				->where('sms_id', $sms_id)
				->get();
		return $query->row();
	}

	public function delete($sms_id = '') {
		$this->db->where('sms_id', $sms_id);
		$this->db->delete('imme_sms');
	}

	public function update($sms_id = '', $data = array()) {
		$this->db->where('sms_id', $sms_id);
		$this->db->update('imme_sms', $data);
	}

	public function get_by_outbox($sms_id = '') {
		$query = $this->db->select('*')->from('imme_sms')
				->where('status', "0")
				->get();
		return $query->result();
	}

	public function get_by_processed($sms_id = '') {
		$query = $this->db->select('*')->from('imme_sms')
				->where('sms_id', $sms_id)
				->where('status', "0")
				->get();
		return $query->row();
	}
}