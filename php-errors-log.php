<?php
$log_file=dirname(__FILE__) . '/php-errors.log';
@ini_set('log_errors', 1);
@ini_set('display_errors', 0); /* enable or disable public display of errors (use 'On' or 'Off') */
@ini_set('error_log', $log_file); /* path to server-writable log file */
@ini_set( 'error_reporting', E_ALL ^ E_NOTICE ); /* the php parser to  all errors, excreportept notices.  */
