<?php

/*
	$device['device_id']
	$device['model']
	$device['device_name']
	$device['is_registered']
	$device['number']
	$device['operator_id']
	$device['quota_fellow']
	$device['quota_all']
	$device['last_update']
	$device['token']
*/

class Sms_devices extends CI_Model {

	private $table_name = "sms_devices";
	private $mandatory = "device_id";

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$query = $this->db->insert($this->table_name, $data);
		return $query;
	}

	public function get() {
		$query = $this->db->get($this->table_name);
		return $query->result();
	}

	public function delete($mandatory = '') {
		$this->db->where($this->mandatory, $mandatory);
		$this->db->delete($this->table_name);
	}

	public function get_by_id($mandatory = '') {
		$query = $this->db->select('*')->from($this->table_name)
				->where($this->mandatory, $mandatory)
				->get();
		return $query->row();
	}

	public function update($mandatory = '', $data = array()) {
		$this->db->where($this->mandatory, $mandatory);
		$query = $this->db->update($this->table_name, $data);
		return $query;
	}

	public function get_by_operator($mandatory = '') {
		$query = $this->db->select('*')->from($this->table_name)
				->where("operator_id", $mandatory)
				->get();
		return $query->row();
	}
}