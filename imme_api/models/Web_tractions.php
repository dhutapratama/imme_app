<?php

/*
$tractions['session_id']
$tractions['email']
$tractions['is_download']
$tractions['support_city']
$tractions['idcard_type_id']
$tractions['is_follow']
$tractions['is_using_alpha_voucher']
$tractions['is_try_to_buy']
$tractions['is_try_to_scan_coin']
*/

class Web_tractions extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('web_tractions', $data);
		return $query;
	}

	public function select()
	{
		$query = $this->db->get('web_tractions');
		return $query->result();
	}

	public function delete($session_id = '')
	{
		$query = $this->db->delete('web_tractions')
				->where('session_id', $session_id)
				->get();
		return $query;
	}

	public function select_by_session_id($session_id = '')
	{
		$query = $this->db->select('*')->from('web_tractions')
				->where('session_id', $session_id)
				->get();
		return $query->result();
	}

	public function update_by_session_id($session_id = '', $data = array())
	{
		$this->db->where('session_id', $session_id);
		$query = $this->db->update('web_tractions', $data);
		return $query;
	}

	public function select_by_email($email = '')
	{
		$query = $this->db->select('*')->from('web_tractions')
				->where('email', $email)
				->get();
		return $query->result();
	}
}