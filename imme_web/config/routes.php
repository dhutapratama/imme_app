<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['404_override'] = 'error/index';
$route['translate_uri_dashes'] = FALSE;

$route['try-imme'] = 'home/try_imme';