<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class V2_server extends CI_Controller {

	private $req_data;
	private $date;

	public function __construct() {
        parent::__construct();
		$this->load->model('sms_devices');
		$this->req_data = $input = $this->auth->input_v2();
		$this->date = date("Y-m-d H:i:s");
     }

	public function index()
	{
		$this->write->error("Are you trying to hack this server?");
	}
	
	public function sms_register_device() {
		$device_id 	= $this->req_data->device_id;
		$model 		= $this->req_data->model;
		$token	 	= $this->req_data->token;

		$devices = $this->sms_devices->get_by_id($device_id);
		if (!$devices) {
			$device['device_id']		= $device_id;
			$device['model']			= $model;
			$device['device_name']		= $model;
			$device['is_registered']	= 0;
			$device['number']			= "unknown";
			$device['operator_id']		= 0;
			$device['quota_fellow']		= 0;
			$device['quota_all']		= 0;
			$device['token']			= $token;
			$this->sms_devices->insert($device);
		} else {
			$device['token']			= $token;
			$this->sms_devices->update($device);
		}

		$this->write->feedback();
	}


	public function sms_ping() {
		$device_id 	= $this->req_data->device_id;

		$devices = $this->sms_devices->get_by_id($device_id);
		if (!$devices) {
			$this->write->error("Device not registered");
		}
		
		$device['last_update']	= $this->date;
		$this->sms_devices->update($devices->device_id, $device);
		
		$this->write->feedback();
	}

	public function sms_sent() {
		$this->load->model('sms_message');
		$message_token	= $this->req_data->message_token;

		$message['is_sent']		= 1;
		$message['sent_date']	= $this->date;
		$this->sms_message->update_by_token($message_token, $message);

		$this->write->feedback();
	}
}