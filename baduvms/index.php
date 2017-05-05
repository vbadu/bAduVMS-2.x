<?php
header("content-type:text/html; charset=utf-8");
@date_default_timezone_set('PRC');
if(file_exists(dirname(__FILE__).'/data/closed.lock')) exit("系统升级中，请稍后访问...");
if(!empty($_SERVER['HTTP_X_REWRITE_URL']) ){
	$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
} else if (!isset($_SERVER['REQUEST_URI'])) {
	if (isset($_SERVER['argv']))
	{
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0]; 
	}else{
		$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING']; 
	} 
}

//定义框架目录
define('bAdu_PATH', dirname(__FILE__) . '/system/'); //指定内核目录
define('__CONFIG__', bAdu_PATH . 'config/config.php'); 
require __CONFIG__;
require (bAdu_PATH . 'core/App.class.php');

//定义自定义目录
$root = $config['URL_HTTP_HOST'] . str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);
define('__ROOT__', substr($root, 0, -1));
define('__ROOTDIR__', strtr(dirname(__FILE__),'\\','/'));
define('__UPL__', __ROOT__.'/upload/');
define('MOBILE', true);
$app = new App($config);
$app->run();

?>