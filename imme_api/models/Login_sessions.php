<?php

/*
$login_sessions['login_session_id']		= '';
$login_sessions['session_key']			= '';
$login_sessions['start_session_date']	= '';
$login_sessions['expired_session_date']	= '';
$login_sessions['customer_id']			= '';
$login_sessions['account_id']			= '';
$login_sessions['balance_id']			= '';
$login_sessions['setting_id']			= '';
$login_sessions['device_id']			= '';
$login_sessions['device_ip']			= '';
$login_sessions['public_ip']			= '';
*/

class login_sessions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_login_sessions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('imme_login_sessions');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_login_sessions')
				->where('login_session_id', $id);
		return $query;
	}

	public function select_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_login_sessions')
				->where('login_session_id', $id)
				->get();
		return $query->result();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('login_session_id', $id);
		$query = $this->db->update('imme_login_sessions', $data);
		return $query;
	}

	public function delete_session_key($session_key = '')
	{
		$this->db->where('session_key', $session_key);
		$this->db->or_where('expired_session_date <=', date("Y-m-d H:i:s"));
		$query = $this->db->delete('imme_login_sessions');
		return $query;
	}

	public function select_by_session_key($session_key = '')
	{
		$query = $this->db->select('*')->from('imme_login_sessions')
				->where('session_key', $session_key)
				->get();
		return $query->result();
	}
}