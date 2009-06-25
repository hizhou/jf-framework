<?php
/*
 * env config
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Hong_Kong');

/*
 * include path config
 */
set_include_path(
	'.' . PATH_SEPARATOR . 
	realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../src')
);


