<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->model(array('login_sessions', 'notifications', 'ads_publish', 'ads_content'));
     }

	public function index()
	{
		$this->form_validation
			->set_rules('session_key', 'Session key', 'required');
		
		if ($this->form_validation->run()) {
			$session_key	= $this->input->post('session_key');
		} else {
			$this->_error('138', 'Get notification failed');
		}

		$login_data = $this->login_sessions->select_by_session_key($session_key);
		if (!$login_data) 
		{
			$this->_error('108', 'Expired login session');
		}

		$notification = $this->notifications->check_notification($login_data[0]->customer_id);
		$ads_publish = $this->ads_publish->check_ads_publish($login_data[0]->customer_id);
		if ($ads_publish) {
			$ads_content = $this->ads_content->select_by_id($ads_publish[0]->ads_content_id);
		}

		if ($notification || $ads_publish) {
			$available_notification = true;

			$i = 0;
			if ($notification) {
				foreach ($notification as $value) {
					$update_notification['sent_date']	= date("Y-m-d H:i:s");
					$update_notification['is_sent']		= 1;
					$this->notifications->update_by_id($notification[$i]->notification_id, $update_notification);

					$notification_data[$i] = new Notif();

					$notification_data[$i]->id		= '111'.$value->notification_id;
					$notification_data[$i]->type	= 'notification';
					$notification_data[$i]->text	= $value->notification_text;
					$i++;
				}
			}
			

			if ($ads_publish) {
				$update_publish['published_date']	= date("Y-m-d H:i:s");
				$update_publish['is_published']		= 1;
				$this->ads_publish->update_by_id($ads_publish[0]->ads_publish_id, $update_publish);

				$notification_data[$i] = new Notif();

				$notification_data[$i]->id		= '222'.$ads_content[0]->ads_content_id;
				$notification_data[$i]->type	= 'advertising';
				$notification_data[$i]->text	= $ads_content[0]->notification_text;
			}
		} else {
			$available_notification = false;
		}

		$feedback['error'] 					= false;
		$feedback['available_notification']	= $available_notification;
		$feedback['notification']			= (isset($notification_data))?$notification_data:false;

		$this->_feedback($feedback);
	}

	private function _error($code = '100', $message = 'Unknown error')
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$output['data'] = $array_data;
		$this->load->view('make_json', $output);
	}
}

class Notif {
    var $id = 0;
    var $type = '';
    var $text = '';
}