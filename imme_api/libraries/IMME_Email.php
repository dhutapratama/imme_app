<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class IMME_Email extends CI_Email {
    public function __construct()
    {
    	$CI =& get_instance();
        parent::__construct();
    }

    public function send_email($send_to, $subject, $message) {
    	$CI =& get_instance();

        $CI->load->config('imme_email');
        $config['protocol']     = $CI->config->item('protocol');
        $config['smtp_host']    = $CI->config->item('smtp_host');
        $config['smtp_port']    = $CI->config->item('smtp_port');
        $config['smtp_timeout'] = $CI->config->item('smtp_timeout');
        $config['smtp_user']    = $CI->config->item('smtp_user');
        $config['smtp_pass']    = $CI->config->item('smtp_pass');
        $config['charset']      = $CI->config->item('charset');
        $config['newline']      = $CI->config->item('newline');
        $config['mailtype']     = $CI->config->item('mailtype');
        $config['validation']   = $CI->config->item('validation');

        $CI->email->initialize($config);

        $CI->email->from($CI->config->item('sender_mail'), $CI->config->item('sender_name'));
        $CI->email->to($send_to); 
        $CI->email->subject($subject);
        $CI->email->message($message);
        return $CI->email->send();
	}
}