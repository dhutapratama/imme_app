<?php

/*
$question['question_id']
$question['message']
$question['created_date']
*/

class Web_question extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_question', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->select('*')->from('web_question')
				->order_by('question_id', 'desc')
				->get();
		return $query->result();
	}

	public function delete($question_id = '')
	{
		$query = $this->db->delete('web_question')
				->where('question_id', $question_id)
				->get();
		return $query;
	}

	public function select_by_id($question_id = '')
	{
		$query = $this->db->select('*')->from('web_question')
				->where('question_id', $question_id)
				->get();
		return $query->result();
	}

	public function update_by_id($question_id = '', $data = array())
	{
		$this->db->where('question_id', $question_id);
		$query = $this->db->update('web_question', $data);
		return $query;
	}
}