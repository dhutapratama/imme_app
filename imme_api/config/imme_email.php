<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ($_SERVER['HTTP_HOST'] == 'api.imme.app') {
	$config['protocol']		= 'smtp';
	$config['smtp_host']	= 'mail.dhutapratama.com';
	$config['smtp_port']	= '25';
	$config['smtp_timeout']	= '30';
	$config['smtp_user']	= 'me@dhutapratama.com';
	$config['smtp_pass']	= '48624862';
	$config['charset']		= 'utf-8';
	$config['newline']		= "\r\n";
	$config['mailtype']		= 'html'; // or html
	$config['validation']	= TRUE; // bool whether to validate email or not   
	$config['sender_name']	= 'IMME Wallet';

	$config['email_server']	= 'http://api1.imme.asia/registration/please_send';
} elseif ($_SERVER['HTTP_HOST'] == 'api1.imme.asia') {
	$config['protocol']		= 'smtp';
	$config['smtp_host']	= 'mail.dhutapratama.com';
	$config['smtp_port']	= '25';
	$config['smtp_timeout']	= '7';
	$config['smtp_user']	= 'info@imme.asia';
	$config['smtp_pass']	= 'info$RFV%TGB';
	$config['charset']		= 'utf-8';
	$config['newline']		= "\r\n";
	$config['mailtype']		= 'html'; // or html
	$config['validation']	= TRUE; // bool whether to validate email or not   
	$config['sender_name']	= 'IMME Wallet';
	$config['sender_mail']	= 'info@imme.asia';

	$config['email_server']	= 'http://api1.imme.asia/registration/please_send';
} elseif ($_SERVER['HTTP_HOST'] == 'api3.imme.asia') {
	$config['protocol']		= 'smtp';
	$config['smtp_host']	= 'sayangan.idwebhost.com';
	$config['smtp_port']	= '25';
	$config['smtp_timeout']	= '7';
	$config['smtp_user']	= 'info@imme.asia';
	$config['smtp_pass']	= 'info$RFV%TGB';
	$config['charset']		= 'utf-8';
	$config['newline']		= "\r\n";
	$config['mailtype']		= 'html'; // or html
	$config['validation']	= TRUE; // bool whether to validate email or not   
	$config['sender_name']	= 'IMME Wallet';
	$config['sender_mail']	= 'info@imme.asia';

	$config['email_server']	= 'http://api1.imme.asia/registration/please_send';
} elseif ($_SERVER['HTTP_HOST'] == 'imme.freevar.com') {
	$config['protocol']		= 'smtp';
	$config['smtp_host']	= 'sayangan.idwebhost.com';
	$config['smtp_port']	= '25';
	$config['smtp_timeout']	= '7';
	$config['smtp_user']	= 'info@imme.asia';
	$config['smtp_pass']	= 'info$RFV%TGB';
	$config['charset']		= 'utf-8';
	$config['newline']		= "\r\n";
	$config['mailtype']		= 'html'; // or html
	$config['validation']	= TRUE; // bool whether to validate email or not   
	$config['sender_name']	= 'IMME Wallet';
	$config['sender_mail']	= 'info@imme.asia';

	$config['email_server']	= 'http://api1.imme.asia/registration/please_send';
} else {
	echo 'Please contact our support at dhuta.pratama@imme.asia : email';
	exit();
}
