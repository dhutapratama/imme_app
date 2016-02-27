<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['base_url']		= "http://".$_SERVER['HTTP_HOST'];
$config['blog_url']		= "http://blog.".$_SERVER['HTTP_HOST'];
$config['index_page']	= '';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'REQUEST_URI' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'REQUEST_URI'    Uses $_SERVER['REQUEST_URI']
| 'QUERY_STRING'   Uses $_SERVER['QUERY_STRING']
| 'PATH_INFO'      Uses $_SERVER['PATH_INFO']
|
| WARNING: If you set this to 'PATH_INFO', URIs will always be URL-decoded!
*/
$config['uri_protocol']			= 'PATH_INFO';
$config['url_suffix']			= '.imme';
$config['language']				= 'english';
$config['charset']				= 'UTF-8';
$config['enable_hooks']			= FALSE;
$config['subclass_prefix']		= 'IMME_';
$config['composer_autoload']	= FALSE;
$config['permitted_uri_chars']	= 'a-z 0-9~%.:_\-';
$config['allow_get_array']		= TRUE;
$config['enable_query_strings']	= FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd';

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| You can also pass an array with threshold levels to show individual error types
|
| 	array(2) = Debug Messages, without Error Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/

if ($_SERVER['HTTP_HOST'] == 'imme.app') {
	$config['log_threshold'] 	= 4;
	$config['encryption_key']	= 'LLd4HvqtvwbEpaRr7L0vWBOQvlViLaVM';
} elseif ($_SERVER['HTTP_HOST'] == 'imme.asia' || $_SERVER['HTTP_HOST'] == 'www.imme.asia') {
	$config['log_threshold'] 	= 1;
	$config['encryption_key']	= 'LLd4HvqtvwbEpaRr7L0vWBOQvlViLaVM';
} else {
	echo 'Please contact our support at dhuta.pratama@imme.asia : config';
	exit();
}

$config['log_path']				= '';
$config['log_file_extension']	= '';
$config['log_file_permissions'] = 0644;
$config['log_date_format']		= 'Y-m-d H:i:s';
$config['error_views_path']		= '';
$config['cache_path']			= '';
$config['cache_query_string']	= FALSE;

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_driver'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'sess_cookie_name'
|
|	The session cookie name, must contain only [0-9a-z_-] characters
|
| 'sess_expiration'
|
|	The number of SECONDS you want the session to last.
|	Setting to 0 (zero) means expire when the browser is closed.
|
| 'sess_save_path'
|
|	The location to save sessions to, driver dependent.
|
|	For the 'files' driver, it's a path to a writable directory.
|	WARNING: Only absolute paths are supported!
|
|	For the 'database' driver, it's a table name.
|	Please read up the manual for the format with other session drivers.
|
|	IMPORTANT: You are REQUIRED to set a valid save path!
|
| 'sess_match_ip'
|
|	Whether to match the user's IP address when reading the session data.
|
|	WARNING: If you're using the database driver, don't forget to update
|	         your session table's PRIMARY KEY when changing this setting.
|
| 'sess_time_to_update'
|
|	How many seconds between CI regenerating the session ID.
|
| 'sess_regenerate_destroy'
|
|	Whether to destroy session data associated with the old session ID
|	when auto-regenerating the session ID. When set to FALSE, the data
|	will be later deleted by the garbage collector.
|
| Other session cookie settings are shared with the rest of the application,
| except for 'cookie_prefix' and 'cookie_httponly', which are ignored here.
|
*/
$config['sess_driver']				= 'files';
$config['sess_cookie_name'] 		= 'imme_secure';
$config['sess_expiration']			= 7200;
$config['sess_save_path']			= NULL;
$config['sess_match_ip']			= FALSE;
$config['sess_time_to_update']		= 300;
$config['sess_regenerate_destroy']	= FALSE;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix'   = Set a cookie name prefix if you need to avoid collisions
| 'cookie_domain'   = Set to .your-domain.com for site-wide cookies
| 'cookie_path'     = Typically will be a forward slash
| 'cookie_secure'   = Cookie will only be set if a secure HTTPS connection exists.
| 'cookie_httponly' = Cookie will only be accessible via HTTP(S) (no javascript)
|
| Note: These settings (with the exception of 'cookie_prefix' and
|       'cookie_httponly') will also affect sessions.
|
*/
$config['cookie_prefix']	= '';
$config['cookie_domain']	= '';
$config['cookie_path']		= '/';
$config['cookie_secure']	= FALSE;
$config['cookie_httponly'] 	= FALSE;

$config['standardize_newlines'] = FALSE;
$config['global_xss_filtering'] = TRUE;

$config['csrf_protection']		= FALSE;
$config['csrf_token_name']		= 'csrf_token';
$config['csrf_cookie_name']		= 'csrf_protecton';
$config['csrf_expire']			= 999999;
$config['csrf_regenerate']		= TRUE;
$config['csrf_exclude_uris']	= array();

$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
