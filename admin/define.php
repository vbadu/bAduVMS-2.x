<?php
define('__ADMINDIR__', strtr(dirname(__FILE__),'\\','/'));
$root=str_replace(basename($_SERVER["SCRIPT_NAME"]),'',$_SERVER["SCRIPT_NAME"]);
$admindir=explode('/', __ADMINDIR__);
$adminfile='/'.(end($admindir));
$root=substr($root,0,-1);
$root=str_replace($adminfile,'',$root);
define('bAdu_PATH', __ROOTDIR__ . '/system/');
define('__ROOTURL__', $root); //根URL
define('__PUBLIC__', $root.'/public'); //根公共URL
define('__PUBLICURL__', $root.'/public/admin'); //根公共URL
define('__ROOTUPD__', $root.'/public/upload'); //根公共上传
define('__CONFIG__', bAdu_PATH . 'config/config.php'); //根公共上传
require __CONFIG__;
require bAdu_PATH . 'core/App.class.php'; 
define('__UPDURL__', __ROOTURL__.'/upload'); //根上传目录
define('__TPLDIR__', __ROOTDIR__.'/'.$config['TPL_TEMPLATE_PATH']); //根模板目录

$config['DB_CACHE_PATH'] = __ROOTDIR__.'/data/db_cache/'; 
$config['DB_CACHE_FILE'] = 'admin_cachedata';
$config['HTML_CACHE_ON'] = false;
$config['URL_REWRITE_ON'] = false;
$config['MODULE_PATH']='./controller/';
$config['MODEL_PATH']='./model/';
$config['TPL_TEMPLATE_PATH'] = 'view/';
$config['TPL_TEMPLATE_SUFFIX'] = '.html';
$config['TPL_CACHE_ON'] = false;
$config['URL_MODULE_DEPR']='/';//模块分隔符，一般不需要修改
$config['URL_ACTION_DEPR']='/';//操作分隔符，一般不需要修改
$config['URL_PARAM_DEPR']='-';//参数分隔符，一般不需要修改
$config['URL_HTML_SUFFIX']='.html';//伪静态后缀设置
if($config['URL_REWRITE_ON']){
	define('ROOTAPP', $root);
}else{
	define('ROOTAPP', $root.'/index.php'); //根应用
}

?>