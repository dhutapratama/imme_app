<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Secure {

	public function __construct()
	{
		$CI =& get_instance();

		// Log device connection
		$data['request_code']	= $CI->input->get_post('request_code');
		$data['method']			= $_SERVER['REQUEST_METHOD'];
		$data['device_id']		= '-';
		$data['ip_address']		= $_SERVER['REMOTE_ADDR'];
		$data['user_agent']		= $_SERVER['HTTP_USER_AGENT'];
		$CI->db->insert('imme_connection_logs', $data);

		if ($_SERVER['REQUEST_METHOD'] == "GET" && $CI->input->get('request_code') != '1000') {
			$this->_information();
		}

		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$CI->load->model('devices');
			if (!$CI->devices->check_user_agent($data['user_agent'])) {
				$this->_error('105', 'Your device has not registered with right apps');
			}
		}
	}

	private function _information() {
		$json['server']		= "IMME API Server";
		$json['version']	= "1.0.0";
		$json['year']		= "2015";
		$json['message']	= "Versi Perang";
		$this->_feedback($json);
	}

	// Error code feedback
	private function _error($code = '100', $message = 'Unknown error')
	{
		$json['error']		= true;
		$json['code']		= $code;
		$json['message']	= $message;
		$this->_feedback($json);
	}

	private function _feedback ($array_data)
	{
		$CI 				=& get_instance();
		$output['data'] 	= $array_data;
		$CI->load->view('make_json', $output);
	}

	private $iv = 'fedcba9876543210'; #Same as in JAVA
    function imme_encode($str = "", $key = "", $isBinary = false)
    {
    	/*
    	if (strlen($key) < 16) {
    		$diff = strlen($key) - 16;
    		for ($i=0; $i < $diff; $i++) { 
    			$key = $key."i";
    		}
    	} */
        $iv = $this->iv;
        $str = $isBinary ? $str : utf8_decode($str);
        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
        mcrypt_generic_init($td, $this->key, $iv);
        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $isBinary ? $encrypted : bin2hex($encrypted);
    }

    function imme_decode($code = "", $key = "", $isBinary = false)
    {
    	/*if (strlen($key) < 16) {
    		$diff = strlen($key) - 16;
    		for ($i=0; $i < $diff; $i++) { 
    			$key = $key."i";
    		}
    	}
    	*/
        $code = $isBinary ? $code : $this->hex2bin($code);
        $iv = $this->iv;
        $td = mcrypt_module_open('rijndael-128', ' ', 'cbc', $iv);
        mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $code);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $isBinary ? trim($decrypted) : utf8_encode(trim($decrypted));
    }

    protected function hex2bin($hexdata)
    {
        $bindata = '';
        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }


    /* Pending because stack on resolver

	// Two Factor Algorithm encoding
	function imme_encode ($data = '', $algorithm = '')
	{
		$secret_key = $algorithm;

		$crypto_step_1	= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$crypto_step_2 	= mcrypt_create_iv($crypto_step_1, MCRYPT_RAND);
		$crypto_step_3 	= mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $secret_key, $data, MCRYPT_MODE_ECB, $crypto_step_2);

		return rtrim(base64_encode($crypto_step_3), "\0");
	}

	// Two Factor Algorithm decoding
	function imme_decode ($cipher = '', $algorithm = '')
	{
		$secret_key = $algorithm;
		$decoded64	= base64_decode($cipher);

		$crypto_step_1	= mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
		$crypto_step_2 	= mcrypt_create_iv($crypto_step_1, MCRYPT_RAND);
		$crypto_step_3 	= mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secret_key, $decoded64, MCRYPT_MODE_ECB, $crypto_step_2);

		return rtrim($crypto_step_3, "\0");
	}

	*/

	function tba_encode($algorithm = '')
	{
		// time in unix
		$tba_key = time();
		for ($i=0; $i < 5; $i++) { 
			$operator_code	= substr($algorithm, $i, 1);
			$factor			= substr($algorithm, $i + 5, 1);

			if ($operator_code == 1) {
				$tba_key = $this->plus($tba_key, $factor);
			} elseif ($operator_code == 2) {
				$tba_key = $this->minus($tba_key, $factor);
			} elseif ($operator_code == 3) {
				$tba_key = $this->times($tba_key, $factor);
			}
		}
		return $tba_key;
	}

	function tba_decode($tba_key = '', $algorithm = '')
	{
		$unix_time = $tba_key;
		for ($i = 4; $i >= 0; $i--) { 
			$operator_code	= substr($algorithm, $i, 1);
			$factor			= substr($algorithm, $i + 5, 1);

			if ($operator_code == 1) {
				$unix_time = $this->minus($unix_time, $factor);
			} elseif ($operator_code == 2) {
				$unix_time = $this->plus($unix_time, $factor);
			} elseif ($operator_code == 3) {
				$unix_time = $this->divide($unix_time, $factor);
			}
		}
		return $unix_time;
	}

	function cba_encode($counter = '', $algorithm = '')
	{
		$cba_key = $counter;
		for ($i=0; $i < 5; $i++) { 
			$operator_code	= substr($algorithm, $i + 0, 1);
			$factor			= substr($algorithm, $i + 5, 1);

			if ($operator_code == 1) {
				$cba_key = $this->plus($cba_key, $factor);
			} elseif ($operator_code == 2) {
				$cba_key = $this->times($cba_key, $factor);
			}
		}
		return $cba_key;
	}

	function cba_decode($cba_key = '', $algorithm = '')
	{
		$counter = $cba_key;
		for ($i = 4; $i >= 0; $i--) { 
			$operator_code	= substr($algorithm, $i, 1);
			$factor			= substr($algorithm, $i + 5, 1);

			if ($operator_code == 1) {
				$counter = $this->minus($counter, $factor);
			} elseif ($operator_code == 2) {
				$counter = $this->divide($counter, $factor);
			}
		}
		return $counter;
	}

	function convert_to_hex($data)
	{
		$result = '';
		$result = bin2hex($data);
		return $result;
	}

	//HEX to ASCII converter
	function convert_to_ascii($data)
	{
		$result = '';
		$result = pack('H*', $data);
		return $result;
	}

	function plus($a = 0, $b = 0)
	{
		return $a + $b;
	}

	function minus($a = 0, $b = 0)
	{
		return $a - $b;
	}

	function times($a = 0, $b = 0)
	{
		return $a * $b;
	}

	function divide($a = 0, $b = 0)
	{
		return $a / $b;
	}
}