<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms {

	private $server_key	= "AIzaSyDWhRuluTw8417-5BZgKVdZUc4LnyTB4cQ";
	private $message_token;

	public function __construct() {
		$CI =& get_instance();
		$this->message_token = md5("SMS".date("Y-m-d H:i:s").rand(10, 99));
	}


	public function send($number, $message) {
		$CI =& get_instance();
		$CI->load->model(array('sms_prefix', 'sms_message', 'sms_devices'));

		$number_prefix = substr($number, 0, 4);
		$prefix = $CI->sms_prefix->get_by_prefix($number_prefix);
		if (!$prefix) {
			$CI->write->error("Maaf, kami tidak bisa mengirimkan ke nomor ini.");
		}

		$fellow = true;
		$devices = $CI->sms_devices->get_by_operator($prefix->operator_id);
		if (!$devices) {
			
			$devices = $CI->sms_devices->get();

			$fellow = false;
			$device = false;
			foreach ($devices as $value) {
				if ($value->quota_all > 0) {
					$device = $value;
					break;
				}
			}

			if(!$device) {
				$CI->write->error("Maaf, server registrasi mengalami masalah, silahkan coba lagi nanti.");
			}
		} else {
			if ($devices->quota_fellow) {
				$device = $devices;
			} else {
				$devices = $CI->sms_devices->get();

				$fellow = false;
				$device = false;
				foreach ($devices as $value) {
					if ($value->quota_all > 0) {
						$device = $value;
						break;
					}
				}

				if(!$device) {
					$CI->write->error("Maaf, server registrasi mengalami masalah, silahkan coba lagi nanti.");
				}
			}
		}

		$push_result_json = $this->push_notification($device->token, "SEND SMS", $number, $message);

		$push_result = json_decode($push_result_json);
		$sending_success = $push_result->success;
		if(!$sending_success) {
			$CI->write->error("Maaf server kami mengalami gangguan, silahkan coba lagi nanti.");
			
			$devices = $CI->sms_devices->get();
			foreach ($devices as $value) {
				if ($value->quota_all > 0) {
					push_notification($value->token, "Token Not Work", "-", $value->device_name);
					break;
				}
			}
		}

		if ($fellow) {
			$upd_device['quota_fellow'] = $device->quota_fellow - 1;
		} else {
			$upd_device['quota_all'] = $device->quota_all - 1;
		}

		$CI->sms_devices->update($device->device_id, $upd_device);

		$ins_message['message_token']	= $this->message_token;
		$ins_message['device_id']		= $device->device_id;
		$ins_message['destination']		= $number;
		$ins_message['operator_id']		= $prefix->operator_id;
		$ins_message['message']			= $message;
		$ins_message['is_sent']			= 0;
		$ins_message['sent_date']		= "0000-00-00 00:00:00";
		$CI->sms_message->insert($ins_message);
	}

	private function push_notification($device, $title, $destination, $message) {
		$registrationIds = array( $device );

		$headers = array
		(
			'Authorization: key=' . $this->server_key,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

		$msg = array
		(
			'title'		=> $title,
			'message' 	=> $message,
			'subtitle'	=> $destination,
			'tickerText'=> $this->message_token,
			'vibrate'	=> 0,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array
		(
			'registration_ids' 	=> $registrationIds,
			'data' => $msg
		);
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );

		return $result;
	}

}