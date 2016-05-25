<?php

/*
$deposit_vouchers['voucher_id']
$deposit_vouchers['voucher_code']
$deposit_vouchers['amount']
$deposit_vouchers['account_id']
$deposit_vouchers['created_date']
$deposit_vouchers['merchant_id']
$deposit_vouchers['used_date']
$deposit_vouchers['is_used']
*/

class Deposit_vouchers extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

	public function insert($data = array())
	{
		$query = $this->db->insert('imme_deposit_vouchers', $data);
		return $query;
	}

	public function get()
	{
		$query = $this->db->get('imme_deposit_vouchers');
		return $query->result();
	}

	public function delete($id = '')
	{
		$query = $this->db->delete('imme_deposit_vouchers')
				->where('voucher_id', $id);
		return $query;
	}

	public function get_by_id($id = '')
	{
		$query = $this->db->select('*')->from('imme_deposit_vouchers')
				->where('voucher_id', $id)
				->get();
		return $query->row();
	}

	public function update_by_id($id = '', $data = array())
	{
		$this->db->where('voucher_id', $id);
		$query = $this->db->update('imme_deposit_vouchers', $data);
		return $query;
	}

	public function get_by_voucher_code($voucher_code = '')
	{
		$query = $this->db->select('*')->from('imme_deposit_vouchers')
				->where('voucher_code', $voucher_code)
				->get();
		return $query->row();
	}

	public function get_active_by_voucher_code($voucher_code = '')
	{
		$query = $this->db->select('*')->from('imme_deposit_vouchers')
				->where('voucher_code', $voucher_code)
				->where('is_used', 0)
				->get();
		return $query->row();
	}
}