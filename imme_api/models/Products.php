<?php

/*
$product['product_id']
$product['product_name']
$product['price']
$product['image']
*/

class Products extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

	public function insert($data = array()) {
		$this->db->insert('imme_products', $data);
	}

	public function get() {
		$query = $this->db->get('imme_products');
		return $query->result();
	}

	public function get_by_id($product_id = '') {
		$query = $this->db->select('*')->from('imme_products')
				->where('product_id', $product_id)
				->get();
		return $query->row();
	}

	public function delete($product_id = '') {
		$this->db->where('product_id', $product_id);
		$this->db->delete('imme_products');
	}

	public function update($product_id = '', $data = array()) {
		$this->db->where('product_id', $product_id);
		$this->db->update('imme_products', $data);
	}

	public function get_by_product_key($product_key = '') {
		$query = $this->db->select('*')->from('imme_products')
				->where('product_key', $product_key)
				->get();
		return $query->row();
	}
}