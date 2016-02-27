<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$active_group = 'default';
$query_builder = TRUE;

if ($_SERVER['HTTP_HOST'] == 'api.imme.app') {
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'z',
		'password' => 'z',
		'database' => 'zadmin_imme',
		'dbdriver' => 'mysql',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => FALSE
	);
} elseif ($_SERVER['HTTP_HOST'] == 'api1.imme.asia') {
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'dhutapra_imme',
		'password' => 'immetest',
		'database' => 'dhutapra_imme_test',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => FALSE
	);
} elseif ($_SERVER['HTTP_HOST'] == 'api3.imme.asia') {
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => '10.240.0.3',
		'username' => 'dev',
		'password' => 'dev$RFV%TGB',
		'database' => 'imme_test',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => FALSE
	);
} elseif ($_SERVER['HTTP_HOST'] == 'imme.freevar.com') {
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => '932795',
		'password' => '48624862',
		'database' => '932795',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_general_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => FALSE
	);
} else {
	echo 'Please contact our support at dhuta.pratama@imme.asia : database';
	exit();
}