<?php
   if (!defined('BASEPATH')) exit('No direct script access allowed');
   set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR .     get_include_path());
   require_once APPPATH . 'third_party/Google/autoload.php';

   class Google extends Google_Client {
      function __construct($params = array()) {
       parent::__construct();
      }

      public function push_message($registration_id, $title, $subtitle, $message, $message_id) {
      	// API access key from Google API's Console
      	if (!defined('API_ACCESS_KEY')) {
			define( 'API_ACCESS_KEY', 'AIzaSyDWhRuluTw8417-5BZgKVdZUc4LnyTB4cQ' );
      	}
		$registrationIds = array( $registration_id );

		//define( 'API_ACCESS_KEY', 'AIzaSyCO2NqzWKbLs-uwV_AjWa0Jj2foj_Tnq_I' );
		//$registrationIds = array( "cC4rGZgMj8g:APA91bG11LrnPMVw4AQpA3KNehCvhfy_ObfMRuIlC3VPG8_2SFGqtFWyykC-VsXsFT1wxnf5K5J_lhqLWx0ZN5VP8vFFkHQDuE2rmSf4Uit840gylKkjKBiH7upR6P1vfWy6vonqABv9" );

		$msg = array
		(
			'title'		=> $title,
			'subtitle'	=> $subtitle,
			'message' 	=> $message,
			'tickerText'=> $message_id,
			'vibrate'	=> 1,
			'sound'		=> 1,
			'largeIcon'	=> 'large_icon',
			'smallIcon'	=> 'small_icon'
		);
		$fields = array
		(
			'registration_ids' 	=> $registrationIds,
			'data'				=> $msg
		);
		 
		$headers = array
		(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

		$result = curl_exec($ch );
		curl_close( $ch );
      }
   }