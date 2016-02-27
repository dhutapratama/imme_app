<?php

/*
$idcard_type['idcard_type_id']
$idcard_type['date']
$idcard_type['custommer_id']
$idcard_type['message']
*/

class Idcard_types extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_idcard_types', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_idcard_types');
		return $query->result();
	}

	public function delete($idcard_type_id = '')
	{
		$query = $this->db
				->where('idcard_type_id', $idcard_type_id)
				->delete('imme_idcard_types');
		return $query;
	}

	public function select_by_id($idcard_type_id = '')
	{
		$query = $this->db->select('*')->from('imme_idcard_types')
				->where('idcard_type_id', $idcard_type_id)
				->get();
		return $query->result();
	}

	public function update_by_id($idcard_type_id = '', $data = array())
	{
		$this->db->where('idcard_type_id', $idcard_type_id);
		$query = $this->db->update('imme_idcard_types', $data);
		return $query;
	}
}