<?php defined('SYSINIT') or die('No direct access');
$config = array();
/*********************************************
Configuration for ICY CMS.

The base url of the directory containing the
system folder. WITH a trailing slash.
Eg.
$config['baseurl'] = 'http://www.example.com/';
*********************************************/
$config['baseurl']		= 'http://localhost/ice/'; 

// System folder. Default 'icy/'. WITH a trailing slash!
$config['sys_folder']	= 'ice/';

// If enabled, people using Internet explorer will be promted to install
// Google Chrome Frame when using the software.
$config['use_gcf']		= true;

// Enables shorthand functions (element() and image()). Default true.
$config['use_shorthand']= true;

// Enable cache(bool). May be overruled in files. Default false.
$config['use_cache'] 	= false;

// Dev-mode. When enabled, additional features like on-the-fly element
// database record creation is activated. Recommended to set it to false
// after developement is finished for additional security & performance.
$config['dev_mode']		= true;

// Database connection information
$config['db_host']		= 'localhost';
$config['db_username']	= 'root';
$config['db_password']	= '';
$config['db_name']		= 'ice';
$config['db_prefix']	= '';

$config['content_table']= $config['db_prefix'] . 'ice_content';
?>
