<?php 
@date_default_timezone_set('PRC');
$GLOBALS['_startTime'] = microtime(true);
define('__ROOTDIR__', str_replace("\\",'/',substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR))));
require 'define.php';
$app = new App($config); 
$app->run();
