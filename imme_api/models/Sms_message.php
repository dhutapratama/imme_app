<?php

/*
	$message['message_id']
	$message['message_token']
	$message['device_id']
	$message['destination']
	$message['prefix_id']
	$message['message']
	$message['is_sent']
	$message['sent_date']
*/

class Sms_message extends CI_Model {

	private $table_name = "sms_message";
	private $mandatory = "message_id";

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

	public function update_by_token($mandatory = '', $data = array()) {
		$this->db->where("message_token", $mandatory);
		$query = $this->db->update($this->table_name, $data);
		return $query;
	}
}