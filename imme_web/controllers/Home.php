<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->library("imme");
		$this->load->model(array("web_tractions", "web_city", "web_city_vote"));
		$this->load->helper(array('cookie', 'url'));

		$session_id = get_cookie("imme_traction_id");
		if (!$session_id) {
			$session_id = md5(time().rand(1000, 9999));
			set_cookie("imme_traction_id", $session_id, 7 * 24 * 3600);
			$tractions['session_id'] = $session_id;
        	$this->web_tractions->insert($tractions);
		} else {
			$session_param = $this->web_tractions->select_by_session_id($session_id);
			if (!$session_param) {
				$session_id = md5(time().rand(1000, 9999));
				set_cookie("imme_traction_id", $session_id, 7 * 24 * 3600);
				$tractions['session_id'] = $session_id;
	        	$this->web_tractions->insert($tractions);
			}
		}
	}
	
	public function index()
	{
		$session_id = get_cookie("imme_traction_id");
		$tractions = $this->web_tractions->select_by_session_id($session_id);

		if ($tractions) {
			$output['email']	= $tractions[0]->email;
			$output['is_support_city'] = $tractions[0]->is_support_city;
			$output['is_follow'] = $tractions[0]->is_support_city;
		} else {
			$output['email']						= "";
			$output['is_support_city'] 	= false;
			$output['is_follow'] 				= false;
		}
		
		$output['citys']	= $this->web_city->select();

		if ($output['is_support_city']) {
			$thisty = $this->web_city_vote->select_by_email($output['email']);
			$thisty = $thisty[0]->city_id;
			$thisty = $this->web_city->select_by_id($thisty);
			$output['city'] = $thisty[0]->city_name;
		}

		$this->load->view("template/header", $output);
		$this->load->view("home");
		$this->load->view("template/footer");
	}

	public function try_imme()
	{
		$amount 	 = 500;
		$merchant_id = 1;

		$sim_voucher = $this->imme->sim_voucher($amount, $merchant_id);
		$sim_payment = $this->imme->sim_payment();
		$sim_collect = $this->imme->sim_collect();

		$output['voucher'] = $sim_voucher['url'];
		$output['payment'] = $sim_payment['url'];
		$output['collect'] = $sim_collect['url'];

		$this->load->view("template/header", $output);
		$this->load->view("try");
		$this->load->view("template/footer");
	}

	
}
