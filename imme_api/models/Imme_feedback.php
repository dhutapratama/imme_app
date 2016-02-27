<?php

/*
$feedback['feedback_id']
$feedback['date']
$feedback['custommer_id']
$feedback['message']
*/

class Imme_feedback extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_feedback', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_feedback');
		return $query->result();
	}

	public function delete($feedback_id = '')
	{
		$query = $this->db->delete('imme_feedback')
				->where('feedback_id', $feedback_id)
				->get();
		return $query;
	}

	public function select_by_id($feedback_id = '')
	{
		$query = $this->db->select('*')->from('imme_feedback')
				->where('feedback_id', $feedback_id)
				->get();
		return $query->result();
	}

	public function update_by_id($feedback_id = '', $data = array())
	{
		$this->db->where('feedback_id', $feedback_id);
		$query = $this->db->update('imme_feedback', $data);
		return $query;
	}
}