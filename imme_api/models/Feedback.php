<?php

/*
$feedback['feedback_id']
$feedback['date']
$feedback['customer_id']
$feedback['description']
*/

class Feedback extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	public function insert($data = array()){
		$this->db->insert('imme_feedback', $data);
	}

	public function get(){
		$query = $this->db->get('imme_feedback');
		return $query->result();
	}

	public function get_by_id($id = ''){
		$query = $this->db->select('*')->from('imme_feedback')
				->where('feedback_id', $id)
				->get();
		return $query->row();
	}

	public function delete($id = ''){
		$this->db->where('feedback_id', $id);
		$this->db->delete('imme_feedback');
	}

	public function update($id = '', $data = array()){
		$this->db->where('feedback_id', $id);
		$this->db->update('imme_feedback', $data);
	}
}