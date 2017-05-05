<?php
//数据过滤函数库
/*
功能：用来过滤字符串和字符串数组，防止被挂马和sql注入
参数$data，待过滤的字符串或字符串数组，
$force为true，忽略get_magic_quotes_gpc
*/
function in($data,$force=false){
	if(is_string($data)){
		$data=trim(htmlspecialchars($data));//防止被挂马，跨站攻击
		if(($force==true)||(!get_magic_quotes_gpc())) {
		   $data = addslashes($data);//防止sql注入
		}
		return  $data;
	} else if(is_array($data)) {
		foreach($data as $key=>$value){
			 $data[$key]=in($value,$force);
		}
		return $data;
	} else {
		return $data;
	}	
}

//用来还原字符串和字符串数组，把已经转义的字符还原回来
function out($data){
	if(is_string($data)){
		return $data = stripslashes($data);
	} else if(is_array($data)){
		foreach($data as $key=>$value){
			 $data[$key]=out($value);
		}
		return $data;
	} else {
		return $data;
	}	
}

//文本输入
function text_in($str){
	$str=strip_tags($str,'<br>');
	$str = str_replace(" ", "&nbsp;", $str);
	$str = str_replace("\n", "<br>", $str);	
	if(!get_magic_quotes_gpc()) {
  	  $str = addslashes($str);
	}
	return $str;
}

//文本输出
function text_out($str){
	$str = str_replace("&nbsp;", " ", $str);
	$str = str_replace("<br>", "\n", $str);	
    $str = stripslashes($str);
	return $str;
}

//html代码输入
function html_in($str,$xss=false){
	$search = array("'<script[^>]*?>.*?</script>'si",
					"'<style[^>]*?>.*?</style>'si",
	);
	$replace = array("",
					"",

	);
	if($xss){		  
	$str=@preg_replace ($search, $replace, $str);
	}

//   $str = preg_replace("/<div[^>]*>/i", "<div>", $str);
   $str = preg_replace("/<tr[^>]*>/i", "<tr>", $str);
   $str = preg_replace("/<td[^>]*>/i", "<td>", $str);
   $str = preg_replace("/<TR[^>]*>/i", "<tr>", $str);
   $str = preg_replace("/<TD[^>]*>/i", "<td>", $str);
   $str = preg_replace("/<COL[^>]*>/i", "", $str);
   $str = preg_replace("'<colgroup[^>]*?>.*?/i'si", "", $str);
   $str = preg_replace("/<TABLE[^>]*>/i", "<table border=0 cellSpacing=0 cellPadding=0 width=100% style=border-collapse:collapse;>", $str);
//   $str = preg_replace(""=""", "", $str);


	$str=htmlspecialchars($str);
	if(!get_magic_quotes_gpc()) {
		$str = addslashes($str);
	}

   return $str;
}

//html代码输出
function html_out($str){
//$str = preg_replace('/(.*?style=\\").*?(\\".*?)/i','$1$2',$str);
//$str = preg_replace('/(.*?style=\\").*?(\\".*?)/i','$1$2',$str);
$str = trim($str); //清除字符串两边的空格
//$str = strip_tags($str,"<p>"); //利用php自带的函数清除html格式。保留P标签
//   $str = preg_replace("/<div[^>]*>/i", "<div>", $str);
   $str = preg_replace("/<tr[^>]*>/i", "<tr>", $str);
   $str = preg_replace("/<td[^>]*>/i", "<td style=border:solid windowtext 1.0pt;width:auto;>", $str);
   $str = preg_replace("/<TR[^>]*>/i", "<tr>", $str);
   $str = preg_replace("/<TD[^>]*>/i", "<td style=border:solid windowtext 1.0pt;width:auto;>", $str);
   $str = preg_replace("/<COL[^>]*>/i", "", $str);
   $str = preg_replace("'<colgroup[^>]*?>.*?/i'si", "", $str);
/**/
//   $str = preg_replace("/style=.+?['|\"]/i",' ',$str);//去除样式
   $str = preg_replace("/class=.+?['|\"]/i",' ',$str);//去除样式
   $str = preg_replace("/id=.+?['|\"]/i",' ',$str);//去除样式   
   $str = preg_replace("/width=.+?['|\"]/i",'',$str);//去除样式 
   $str = preg_replace("/height=.+?['|\"]/i",'',$str);//去除样式 
   $str = preg_replace("/border=.+?['|\"]/i",'',$str);//去除样式 
   //$str = preg_replace("/""=""""/i",'',$str);//去除样式 

   $str = preg_replace("/<TABLE[^>]*>/i", "<table style=width:100.0%;border-collapse:collapse;border:none; border=1 cellpadding=0 cellspacing=0 width=100% >", $str);

	if(function_exists('htmlspecialchars_decode'))
		$str=htmlspecialchars_decode($str);
	else
		$str=html_entity_decode($str);
    $str = stripslashes($str);
	return $str;
}

// 获取客户端IP地址
function get_client_ip(){
$Clientip=isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

	if (isset($_SERVER['HTTP_X_REAL_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_REAL_FORWARDED_FOR'];
	}elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_X_FORWARDED_FOR'])){
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/',$_SERVER['HTTP_CLIENT_IP'])){
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}elseif (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
        $ip = getenv("HTTP_CLIENT_IP");
	}else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
        $ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
        $ip = getenv("REMOTE_ADDR");
	}else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
        $ip = $_SERVER['REMOTE_ADDR'];
	}else{
        $ip = "unknown";
	}
	if (preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $ip)) {
		$ip_array = explode('.', $ip);	
		if($ip_array[0]<=255 && $ip_array[1]<=255 && $ip_array[2]<=255 && $ip_array[3]<=255){
			return $ip;
		}			
	}		
   return "unknown";
}

//中文字符串截取
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true){
	if(empty($str)){
		return;
	}
	$sourcestr=$str;
	$cutlength=$length;
	$returnstr = '';
	$i = 0;
	$n = 0.0;
	$str_length = strlen($sourcestr); //字符串的字节数
	while ( ($n<$cutlength) and ($i<$str_length) ){
	   $temp_str = substr($sourcestr, $i, 1);
	   $ascnum = ord($temp_str); 
	   if ( $ascnum >= 252){
	    $returnstr = $returnstr . substr($sourcestr, $i, 6); 
	    $i = $i + 6; 
	    $n++; 
	   }elseif ( $ascnum >= 248 ){
	    $returnstr = $returnstr . substr($sourcestr, $i, 5);
	    $i = $i + 5;
	    $n++;
	   }elseif ( $ascnum >= 240 ){
	    $returnstr = $returnstr . substr($sourcestr, $i, 4);
	    $i = $i + 4;
	    $n++;
	   }elseif ( $ascnum >= 224 ){
	    $returnstr = $returnstr . substr($sourcestr, $i, 3);
	    $i = $i + 3 ; 
	    $n++; 
	   }elseif ( $ascnum >= 192 ){
	    $returnstr = $returnstr . substr($sourcestr, $i, 2);
	    $i = $i + 2; 
	    $n++; 
	   }elseif ( $ascnum>=65 and $ascnum<=90 and $ascnum!=73){
	    $returnstr = $returnstr . substr($sourcestr, $i, 1);
	    $i = $i + 1;
	    $n++;
	   }elseif ( !(array_search($ascnum, array(37, 38, 64, 109 ,119)) === FALSE) ){
	    $returnstr = $returnstr . substr($sourcestr, $i, 1);
	    $i = $i + 1;
	    $n++; 
	   }else{
	    $returnstr = $returnstr . substr($sourcestr, $i, 1);
	    $i = $i + 1;
	    $n = $n + 0.5; 
	   }
	}
	if ( $i < $str_length ){
	   $returnstr = $returnstr . '...';
	}
	return $returnstr;
}

//模块之间相互调用
function  module($module){
	static $module_obj=array();
	static $config=array();
	if(isset($module_obj[$module])){
		return $module_obj[$module];
	}
	if(!isset($config['MODULE_PATH'])){
		$config['MODULE_PATH']=Config::get('MODULE_PATH');
		$config['MODULE_SUFFIX']=Config::get('MODULE_SUFFIX');
		$suffix_arr=explode('.',$config['MODULE_SUFFIX'],2);	
		$config['MODULE_CLASS_SUFFIX']=$suffix_arr[0];
	}	
	if(file_exists($config['MODULE_PATH'].$module.$config['MODULE_SUFFIX'])){
			require_once($config['MODULE_PATH'].$module.$config['MODULE_SUFFIX']);//加载模型文件
			$classname=$module.$config['MODULE_CLASS_SUFFIX'];
			if(class_exists($classname)){
				return  $module_obj[$module]=new $classname();
			}
	}else{
		return false;
	}
}

//模型调用函数
if(!function_exists('model')){
	function  model($model){
		static $model_obj=array();
		static $config=array();
		if(isset($model_obj[$model])){
			return $model_obj[$model];
		}
		if(!isset($config['MODEL_PATH'])){
			$config['MODEL_PATH']=Config::get('MODEL_PATH');
			$config['MODEL_SUFFIX']=Config::get('MODEL_SUFFIX');
			$suffix_arr=explode('.',$config['MODEL_SUFFIX'],2);	
			$config['MODEL_CLASS_SUFFIX']=$suffix_arr[0];
		}	
		if(file_exists($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX'])){
				require_once($config['MODEL_PATH'].$model.$config['MODEL_SUFFIX']);//加载模型文件
				$classname=$model.$config['MODEL_CLASS_SUFFIX'];
				if(class_exists($classname)){
					return  $model_obj[$model]=new $classname();
				}
		}
		return false;
	}
}
// 检查字符串是否是UTF8编码,是返回true,否则返回false
function is_utf8($string){
	if( !empty($string) ) {
		$ret = json_encode( array('code'=>$string) );
		if( $ret=='{"code":null}') {
			return false;
		}
	}
	return true;
}

// 自动转换字符集 支持数组转换
function auto_charset($fContents,$from='gbk',$to='utf-8'){
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if(is_string($fContents) ) {
        if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            return iconv($from,$to,$fContents);
        }else{
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
            $_key =     auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
            if($key != $_key )
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else{
        return $fContents;
    }
}

// 浏览器友好的变量输出
function dump($var, $exit=false){
	@header("Content-type: text/html; charset=utf-8"); 
	$output = print_r($var, true);
	$output = "<pre>" . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
	echo $output;
	if($exit) exit();
}

//获取微秒时间，常用于计算程序的运行时间
function utime(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

//生成唯一的值
function get_uniqid(){
	return md5(uniqid(rand(), true));
}

//加密函数，可用badu_decode()函数解密，$data：待加密的字符串或数组；$key：密钥；$expire 过期时间
function badu_encode($data,$key='',$expire = 0)
{
	$string=serialize($data);
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr(md5(microtime()), -$ckey_length);

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	
	$string =  sprintf('%010d', $expire ? $expire + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) 
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) 
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) 
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	return $keyc.str_replace('=', '', base64_encode($result));		
}
//badu_encode之后的解密函数，$string待解密的字符串，$key，密钥
function badu_decode($string,$key='')
{
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = substr($string, 0, $ckey_length);
	
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	
	$string =  base64_decode(substr($string, $ckey_length));
	$string_length = strlen($string);
	
	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) 
	{
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) 
	{
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) 
	{
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
		return unserialize(substr($result, 26));
	}
	else
	{
		return '';
	}	
}
//遍历删除目录和目录下所有文件
function del_dir($dir){
	if (is_dir($dir)){
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false){
			if ($file != "." && $file != ".."){
				//is_dir($dir.$file)?del_dir($dir.$file):@unlink("$dir/$file");
				is_file($dir.$file)?@unlink("$dir/$file"):del_dir($dir.$file);
			}
		}
		if (readdir($handle) == false){
			closedir($handle);
			@rmdir($dir);
		}

	}else{
		return false;
	}
}

//如果json_encode没有定义，则定义json_encode函数，常用于返回ajax数据
if (!function_exists('json_encode')) {
     function format_json_value(&$value){
        if(is_bool($value)) {
            $value = $value?'true':'false';
        }else if(is_int($value)){
            $value = intval($value);
        }else if(is_float($value)){
            $value = floatval($value);
        }else if(defined($value) && $value === null){
            $value = strval(constant($value));
        }else if(is_string($value)){
            $value = '"'.addslashes($value).'"';
        }
        return $value;
    }

    function json_encode($data){
        if(is_object($data)){
            //对象转换成数组
            $data = get_object_vars($data);
        }else if(!is_array($data)) {
            // 普通格式直接输出
            return format_json_value($data);
        }
        // 判断是否关联数组
        if(empty($data) || is_numeric(implode('',array_keys($data)))) {
            $assoc  =  false;
        }else {
            $assoc  =  true;
        }
        // 组装 Json字符串
        $json = $assoc ? '{' : '[' ;
        foreach($data as $key=>$val) {
            if(!is_null($val)) {
                if($assoc){
                    $json .= "\"$key\":".json_encode($val).",";
                }else{
                    $json .= json_encode($val).",";
                }
            }
        }
        if(strlen($json)>1) {// 加上判断 防止空数组
            $json  = substr($json,0,-1);
        }
        $json .= $assoc ? '}' : ']' ;
        return $json;
    }
}

//POST表单处理函数,$post_array:POST的数据,$null_value:是否删除空表单,$delete_value:删除指定表单
function postinput($post_array,$null_value = null,$delete_value = array()){
	//清除值为空或者为0的元素
	if($null_value){
		foreach($post_array as $key=>$value){
			$value = in($value);
			if($value == ''){
				unset($post_array[$key]);
			}
		} 
	}
	//清除不需要的元素
	$default_value = array('action','button','fid','submit');
	$clear_array = array_merge($default_value,$delete_value);
	foreach($post_array as $key=>$value){
			if(in_array($key,$clear_array)){
				unset($post_array[$key]);		
			}
	}
	return $post_array;
}

//复制目录
function copy_dir($sourceDir,$aimDir){
	$succeed = true;
	if(!file_exists($aimDir)){
		if(!mkdir($aimDir,0777)){
			return false;
		}
	}
	$objDir = opendir($sourceDir);
	while(false !== ($fileName = readdir($objDir))){
		if(($fileName != ".") && ($fileName != "..")){
			if(!is_dir("$sourceDir/$fileName")){
				if(!copy("$sourceDir/$fileName","$aimDir/$fileName")){
					$succeed = false;
					break;
				}
			}else{
				copy_dir("$sourceDir/$fileName","$aimDir/$fileName");
			}
		}
	}
	closedir($objDir);
	return $succeed;
}

//判断ajax提交
function is_ajax() {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
    if (isset($_POST['ajax']) || isset($_GET['ajax'])) return true;
    return false;
}

//获取时间函数
function get_date($format,$timestamp = '') {
	$timezone = 8;
	empty($timestamp) &&$timestamp = PHP_TIME;
	return gmdate($format,$timestamp +intval($timezone) * 3600);
}
//获取时间转换
function str_to_time($timestr) {
	$timezone = 8;
	return function_exists('date_default_timezone_set') ?(strtotime($timestr) -3600 * $timezone) : strtotime($timestr);
}
//返回字符串的毫秒数时间戳
function get_total_millisecond(){
    $time = explode (" ", microtime () );
    $time = $time [1] . ($time [0] * 1000);
    $time2 = explode ( ".", $time );
    $time = $time2 [0];
    return $time;
}
/*
Utf-8、gb2312都支持的汉字截取函数
cut_str(字符串, 截取长度, 开始长度, 编码);
编码默认为 utf-8
开始长度默认为 0
cut_str($str, 8, 0, 'gb2312');
*/
function cut_str($string, $sublen, $start = 0, $code = 'UTF-8'){
	if($code == 'UTF-8'){
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
		return join('', array_slice($t_string[0], $start, $sublen));
	}else{
		$start = $start*2;
		$sublen = $sublen*2;
		$strlen = strlen($string);
		$tmpstr = '';

		for($i=0; $i< $strlen; $i++){
			if($i>=$start && $i< ($start+$sublen)){
				if(ord(substr($string, $i, 1))>129){
					$tmpstr.= substr($string, $i, 2);
				}else{
					$tmpstr.= substr($string, $i, 1);
				}
			}
			if(ord(substr($string, $i, 1))>129) $i++;
		}
		if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
		return $tmpstr;
	}
}
//检查是否是正确的邮箱地址，是则返回true，否则返回false
function is_email($user_email){
    $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
    if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false){
        if (preg_match($chars, $user_email)){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
// 保存配置
function config($array,$config_file='') {
	if (empty($config_file)) $config_file=__CONFIG__;
	if (!is_array($array)) return false;
	foreach ($array as $key => $value) {
		if(!strpos($key,'|')){
			$config_array["config['" . $key . "']"] = $value;
		}else{
			$strarray=explode('|', $key);
			$str="config['" . $strarray[0] . "']";
			foreach ($strarray as $keys=>$values) {
				if($keys<>0){
				$str.="['".$values."']";
				}
			}
			unset($strarrays);
			$config_array[$str] = $value;
		}
	}
	$config = @file_get_contents($config_file); //读取配置
	foreach ($config_array as $name => $value) {
		$name = str_replace(array("'", '"', '[','*'), array("\\'", '\"', '\[','\*'), $name); //转义特殊字符，再传给正则替换
		if (is_string($value) && !in_array($value, array('true', 'false', '3306'))) {
			//如果是字符串，加上单引号
			if(!is_numeric($value)){
				$value = "'" . $value . "'"; 
			}
		}
		//如果是数值类型，但值与格式化后不相同则加单引号
		if(is_numeric($value) && preg_match ("/^(-){0,1}([0-9]+)(,[0-9][0-9][0-9])*([.][0-9]){0,1}([0-9]*)$/", $value) == 1 && ($value*10)!=$value."10"){
			$value = "'" . $value . "'";
		}
		$config = preg_replace("/(\\$" . $name . ")\s*=\s*(.*?);/i", "$1={$value};", $config); //查找替换
	}
	// 写入配置
	if (@file_put_contents($config_file, $config)){
		return true;
	}else{
	   return false;
	}
}
//word清理
function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br>'){
	mb_regex_encoding('UTF-8');
	$search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
	$replace = array('\'', '\'', '"', '"', '-');
	$text = preg_replace($search, $replace, $text);
	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
	if(mb_stripos($text, '/*') !== FALSE){
		$text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
	}
	$text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
	$text = strip_tags($text, $allowed_tags);
	$text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
	$search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
	$replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
	$text = preg_replace($search, $replace, $text);
	$num_matches = preg_match_all("/\<!--/u", $text, $matches);
	if($num_matches){
		$text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
	}
	return $text;
}
//探测是否手机
function is_mobile() {     
	$user_agent = $_SERVER['HTTP_USER_AGENT'];     
	$mobile_agents = Array("240x320", "acer", "acoon", 
		"acs-", "abacho", "ahong", "airness", "alcatel", 
		"amoi", "android", "anywhereyougo.com", 
		"applewebkit/525", "applewebkit/532","applewebkit/533.1", "asus", 
		"audio", "au-mic", "avantogo", "becker", "benq", 
		"bilbo", "bird", "blackberry", "blazer", "bleu", 
		"cdm-", "compal", "coolpad", "danger", "dbtel", 
		"dopod", "elaine", "eric", "etouch", "fly ", 
		"fly_", "fly-", "go.web", "goodaccess", 
		"gradiente", "grundig", "haier", "hedy", 
		"hitachi", "htc", "huawei", "hutchison", 
		"inno", "ipad", "ipaq", "ipod", "jbrowser", 
		"kddi", "kgt", "kwc", "lenovo", "lg ", "lg2", 
		"lg3", "lg4", "lg5", "lg7", "lg8", "lg9", "lg-", 
		"lge-", "lge9", "longcos", "maemo", "mercator", 
		"meridian", "micromax", "midp", "mini", "mitsu", 
		"mmm", "mmp", "mobi", "mot-", "moto", "nec-", 
		"netfront", "newgen", "nexian", "nf-browser", 
		"nintendo", "nitro", "nokia", "nook", "novarra", 
		"obigo", "palm", "panasonic", "pantech", "philips", 
		"phone", "pg-", "playstation", "pocket", "pt-", 
		"qc-", "qtek", "rover", "sagem", "sama", "samu", 
		"sanyo", "samsung", "sch-", "scooter", "sec-", 
		"sendo", "sgh-", "sharp", "siemens", "sie-", 
		"softbank", "sony", "spice", "sprint", "spv", 
		"symbian", "tablet", "talkabout", "tcl-", 
		"teleca", "telit", "tianyu", "tim-", "toshiba", 
		"tsm", "up.browser", "utec", "utstar", "verykool", 
		"virgin", "vk-", "voda", "voxtel", "vx", "wap", 
		"wellco", "wig browser", "wii", "windows ce", 
		"wireless", "xda", "xde", "zte");     
	$is_mobile = false;     
	foreach ($mobile_agents as $device) {       
		if (stristr($user_agent,  $device)) {         
			$is_mobile = true;         
			break;       
		}     
	}     
	return $is_mobile;
}
// 手机号码验证
function is_mob($tel){
	if (strlen($tel)!=11) return false;
	$isMob="/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/";
	$isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
	if(!preg_match($isMob,$tel) || !preg_match($isTel,$tel)){
		return true;
	}
	return false;
}
// 手机号码归属地
function getMobArea($mobs,$type=1,$by=1){
	if ($by==1){
		$mob=IP::get_Area_paipai($mobs,$type);
	}else{
		$mob=IP::get_Area_taobaoMob($mobs,$type);
	}
	return $mob;
}
//微信判断
function isWeixin(){
	$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
	//$is_weixin = strpos($agent, 'micromessenger') ? true : false ;  
	//如果新的没有标识
	if (strpos($agent, 'micromessenger') === false){ 
		//如果旧的也没有标识
		if (strpos($agent, 'MicroMessenger') === false) {
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}
//隐藏手机号码中间几位
function hiddentel($phone){
	$IsWhat = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i',$phone); //固定电话
	if($IsWhat == 1){
		return preg_replace('/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i','$1****$2',$phone);
	}else{
		return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
	}
} 
//检测密码强度 返回百分比
function checkpassword($string){
	$h    = 0;
	$size = strlen($string);
	//print_r(count_chars($string, 1));
	foreach(count_chars($string, 1) as $v){   //count_chars：返回字符串所用字符的信息
		$p = $v / $size;
		$h -= $p * log($p) / log(2);
	}
	$strength = ($h / 4) * 100;
	if($strength > 100){
		$strength = 100;
	}
	return ceil($strength);
} 
//生成随机密码
function get_password( $length = 8 )
{
	$str = substr(md5(time()), 0, $length);
	return $str;
}
//生成随机密码
function make_password( $length = 8 ){
	// 密码字符集，可任意添加你需要的字符
	$chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
	'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's',
	't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D',
	'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O',
	'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z',
	'0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!',
	'@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
	'[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',',
	'.', ';', ':', '/', '?', '|');
	// 在 $chars 中随机取 $length 个数组元素键名
	$keys = array_rand($chars, $length);
	$password = '';
	for($i = 0; $i < $length; $i++)
	{
		// 将 $length 个数组元素连接成字符串
		$password .= $chars[$keys[$i]];
	}
	return $password;
}
//来源验证strcmp($str1, $str2)
function check_from(){
	$domain	 =	get_domain();  
	$fromurl =	get_domain($_SERVER['HTTP_REFERER']);
	if (strcmp($domain, $fromurl)==0){
		return	true;
	}else{
		return	false;
	}
}
//获取域名
function get_domains($url=NULL) {
	$domain	 =	is_null($_SERVER['HTTP_HOST'])?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST'];  
	$host=is_null($url)?$domain:$url;
	$host=strtolower($host);
	if($host){
		preg_match('/[\w][\w-]*\.(?:com\.cn|com|cn|co|net|org|gov|cc|biz|info)(\/|$)/isU', $host, $domain);//获取一级域名
		$result=rtrim($domain[0], '/');
		return $result;
	}else{
		return $host;
	}
}
//检测用户名是否符合要求
function CheckUser($str){
	if(!preg_match("/^[".chr(0xa1)."-".chr(0xff)."a-zA-Z0-9_]+$/",$str)){
		return false;
	}else{
		return true;
	}
}
//隐藏字段中间内容
function half_replace($str,$encoding='utf-8'){
	if(strtolower($encoding)=='gbk'){
		$length=mb_strlen($str,'utf-8');
		$len = ceil(mb_strlen($str,'utf-8')/4);
		$start=mb_substr($str,0,$len,'utf-8');
		if($len<$length-$len){
			$end=mb_substr($str,$length-$len,$len,'utf-8');
		}

		$str=$start.'***'.$end;
		return $str;
	}else{
		$len = strlen($str)/2;
		return substr_replace($str,str_repeat('*',$len),ceil(($len)/2),$len);
	}
}
//获取随机数
function Random($length=6,$type=1){
	if ($type==1){
		$str =range(0,9);
	}else{
		$str = array_merge(range(0,9),range('a','z'),range('A','Z'));
	}
	 shuffle($str);
	 $str = implode('',array_slice($str,0,$length));
	 return $str;
}
//是否POST类型
function  isPost(){
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}
//审核类别
function event_status($data=1) {
	$data=intval($data);
	switch ($data) {
		case 1:
			$html = '<font color="green">已通过';
			break;
		case 2:
			$html = '<font color="red">未通过';
			break;
		case 3:
			$html =	'<font color="#1e90ff">已请假';
			break;
		case 4:
			$html =	'<font color="#4682b4">缺席活动';
			break;
		default:
			$html = '<font color="#4169E1">待审中';
			break;
	}

	return $html.'</font>';
}
//星级类别
function vipstar($data=1,$type=false) {
	//echo $data;
	if (empty($data)) $data=intval($data);
	if (100>$data) $html=($type)?false:'见习期';
	if (200>$data && $data>=100) $html='一星级';
	if (300>$data && $data>=200) $html='二星级';
	if (400>$data && $data>=300) $html='三星级';
	if (500>$data && $data>=400) $html='四星级';
	if (600>$data && $data>=500) $html='五星级';
	if ($data>=600) $html='资深级';
	return $html;
}
//返回域名 strcasecmp二进制安全比较字符串（不区分大小写）
function get_domain() {
	$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
		!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
	return
		($https ? 'https://' : 'http://').
		(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
		($https && $_SERVER['SERVER_PORT'] === 443 ||
		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
		substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
}
//过滤所有非中文文本内容
function clear_no_text($str){
	$search = array ("'<script[^>]*?>.*?</script>'si",   // 去掉 javascript
					"'<style[^>]*?>.*?</style>'si",   // 去掉 css
					"'<[/!]*?[^<>]*?>'si",           // 去掉 HTML 标记
					"'<!--[/!]*?[^<>]*?>'si",           // 去掉 注释 标记
					"'([rn])[s]+'",                 // 去掉空白字符
					"'&(quot|#34);'i",                 // 替换 HTML 实体
					"'&(amp|#38);'i",
					"'&(lt|#60);'i",
					"'&(gt|#62);'i",
					"'&(nbsp|#160);'i",
					"'&(iexcl|#161);'i",
					"'&(cent|#162);'i",
					"'&(pound|#163);'i",
					"'&(copy|#169);'i",
					"'&#(d+);'e"
			);
	 
	$replace = array ("",
				   "",
				   "",
				   "",
				   "\1",
				   "\"",
				   "&",
				   "<",
				   ">",
				   " ",
				   chr(161),
				   chr(162),
				   chr(163),
				   chr(169),
				   "chr(\1)"
			);
	$str = preg_replace($search, $replace, $str); 
	$str = trim($str); 
	return $str;
}
//中文验证
function check_chinese($str,$type=1){
	if ($type==1){
		//判断是否全部是中文
		if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $str)) { //兼容gb2312,utf-8 
			$re= true;
		}else{
			$re= false;
		}
	}else{
		//	判断是否有中文。
		if (preg_match("/[\x7f-\xff]/", $str)) { 
			$re= true; 
		}else{ 
			$re= false;
		}
	}
	return $re;
}
//对象数组化
function object_to_array($stdclassobject){
	$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
	is_array($_array)?null:$_array = array();
	foreach ($_array as $key => $value) {
		$value = (is_array($value) || is_object($value)) ? object_to_array($value) : $value;
		$array[$key] = $value;
	}
	return $array;
}
//创建文件夹
function bulid_dir($dir){ 
	return is_dir($dir) or (bulid_dir(dirname($dir)) and mkdir($dir,0777)); 
}
//根据url创建目录
function mkurldir($dir){
	$paths=parse_url($dir);
	$dir=getcwd().dirname($paths['path']);
	return bulid_dir($dir);
}
/**
 * 友好格式化日期：已过去多久
 *
 * @param int $time 输入时间戳
 * @param string $format 时间格式
 * @param boolon $second 是否精确到秒
 * @return string
 */
function time_format($time, $format = 'Y年n月j日 G:i:s', $second = false){
	if (strlen($time)!=10) return $time;
	$diff = time() - $time;
	if ($diff < 60 && $second)
	{
		return $diff ? $diff.'秒前' : '刚刚';
	}
	$diff = ceil($diff/60);
	if ($diff < 60)
	{
		return $diff  ? $diff.'分钟前' : '刚刚';
	}
	$d = date('Y,n,j', time());
	list($year, $month, $day) = explode(',', $d);
	$today = mktime(0, 0, 0, $month, $day, $year);
	$diff = ($time-$today) / 86400;
	switch (true)
	{
		case $diff < -2:
			break;
		case $diff < -1:
			$format = '前天 '.($second ? 'G:i:s' : 'G:i');
			break;
		case $diff < 0:
			$format = '昨天 '.($second ? 'G:i:s' : 'G:i');
			break;
		default:
			$format = '今天 '.($second ? 'G:i:s' : 'G:i');
	}
	return date($format, $time);
}

/**
 * 友好格式化日期：多久之后
 *
 * @param int $time 时间戳
 * @param string $full_format 超出指定天数范围后使用的时间戳
 * @param int $day_max 指定一个天数范围，当剩余天数大于该天数时，返回 $full_format 格式的时间
 * @return string 格式化结果
 */
function time_format_after($time, $full_format = 'Y-m-d H:i:s', $day_max = 30){
	$diff = $time - TIME;
	if ($diff == 0) {
		return '现在';
	}
	if ($diff < 0)
	{
		return time_format($time, $full_format, true);
	}
	if ($diff < 60) {
		return $diff . '秒后';
	}
	$minute = ceil($diff / 60);
	if ($minute < 60) {
		return $minute . '分钟后';
	}
	$day = ceil($diff / 86400);
	if ($day_max && $day > $day_max) {
		return date($full_format, $time);
	}
	$time = date('G:i', $time);
	if ($day < 1) {
		return '今天 ' . $time;
	}
	if ($day < 2) {
		return '明天 ' . $time;
	}
	if ($day < 3) {
		return '后天 ' . $time;
	}
	return $day . '天后';
}

/**
 * 友好格式化时时：将转换为时分秒显示
 *
 * @param int $second 秒数
 * @return string
 */
function second_format($second){
	$hour = $minute = 0;
	$str = '';
	if($second > 3600)
	{
		$hour = floor($second / 3600);
		$second = $second % 3600;			
	}
	if($second > 60)
	{
		$minute = floor($second / 60);
		$second = $second % 60;	
	}
	if($hour)
	{
		$str .= $hour ."小时";
	}
	if($minute)
	{
		$str .= $minute ."分";
	}
	if($second)
	{
		$str .= $second ."秒";
	}
	return $str;
}
function decodeData($data){
	return is_array($data)
		? $data
		: ($data[0]=='{' || $data[0]=='[') ? json_decode($data, true) : unserialize($data);
}
function url_ext(){
	$rewrite_rules = Config::get('URL_REWRITE_RULE');
	if( !empty($rewrite_rules) ){
		if( ($pos = strpos( $_SERVER['REQUEST_URI'], '?' )) !== false )
			parse_str( substr( $_SERVER['REQUEST_URI'], $pos + 1 ), $_GET );
		$rewrite_rules['<m>/<a>'] = '<m>/<a>';
		foreach($rewrite_rules as $rule => $mapper){
			if(0!==stripos($rule, 'http://'))
				$rule = 'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') .'/'.$rule;
			$rule = '/'.str_ireplace(array(
				'\\\\', 'http://', '/', '<', '>',  '.', 
			), array(
				'', '', '\/', '(?<', '>\w+)', '\.', 
			), $rule).'/i';
			if(preg_match($rule, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], $matchs)){
				foreach($matchs as $matchkey => $matchval){
					if('c' === $matchkey)
						$mapper = str_ireplace('<m>', $matchval, $mapper);
					elseif('a' === $matchkey)
						$mapper = str_ireplace('<a>', $matchval, $mapper);
					else
						if(!is_int($matchkey))$_GET[$matchkey] = $matchval;
				}
				list(App::$module, App::$action) = explode('/', $mapper);
				break;
			}
		}
	} else {
		App::$module=$_GET['m'];
		App::$action=$_GET['a'];
	}
}
function url($module='', $action='', $param=array()){
	$route=$module.'/'.$action;
	$module=empty($module)?'':'?m='.$module;
	$action=empty($action)?'':'&a='.$action;
	$params=empty($param)?'':'&'.http_build_query($param);
	$url = $_SERVER["SCRIPT_NAME"].$module.$action.$params;

	$rewrite_rules = Config::get('URL_REWRITE_RULE');
	if( !empty($rewrite_rules) ) {
		static $urlArray=array();
		if(!isset($urlArray[$url])){
			foreach($rewrite_rules as $rule => $mapper){
				$mapper = '/'.str_ireplace(array('/', '<a>', '<m>'), array('\/', '(?<a>\w+)', '(?<m>\w+)'), $mapper).'/i';
				if(preg_match($mapper, $route, $matchs)){
					list($controller, $action) = explode('/', $route);
					$urlArray[$url] = str_ireplace(array('<a>', '<m>'), array($action, $controller), $rule);
					if(!empty($param)){
						$_args = array();
						foreach($param as $argkey => $arg){
							$count = 0;
							$urlArray[$url] = str_ireplace('<'.$argkey.'>', $arg, $urlArray[$url], $count);
							if(!$count)$_args[$argkey] = $arg;
						}
						$urlArray[$url] = preg_replace('/<\w+>/', '', $urlArray[$url]).
							(!empty($_args) ? '?'.http_build_query($_args) : '');
					}
					
					if(0!==stripos($urlArray[$url], 'http://'))
						$urlArray[$url] = 'http://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\') .'/'.$urlArray[$url];
					return $urlArray[$url];
				}
			}
		}
	}
	return $url;
}
function api_idcard($str){
	$str = strtolower($str);
	if (empty($str)) return false;
	$url = "http://apis.juhe.cn/idcard/index";
	$param = array(
		  "cardno" => $str,
		  "dtype" => "json",
		  "key" => "6ef0284e13b27d4b72ba0742083fd49e"
	);
	$params = http_build_query($param);
	$httpInfo = array();
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	}
	curl_setopt( $ch, CURLOPT_URL , $url.'?'.$params );
	$response = curl_exec( $ch );
	$err = curl_error($ch);
	if ($response === FALSE) {
		if ($err) return "cURL Error #:" .$err;
		return false;
	}
	$httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	$httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	curl_close( $ch );
	$result = json_decode($response,true);
	
	if($result){
		if($result['error_code']=='0'){
			if ($result['result']['sex']=='男') $result['result']['sex']='M';
			$result['result']['birthday']= str_replace(array('年','月'),'-',$result['result']['birthday']);
			$result['result']['birthday']= str_replace('日','',$result['result']['birthday']);
			return $result['result'];
		}else{
			return $result['error_code'].":".$result['reason'];
		}
	}else{
		return "请求失败";
	}
}
//处理图片
function thumb($img,$maxWidth=60,$maxHeight=60,$headpic=false){
	if(empty($img)) return !($headpic) ? __PUBLIC__.'/images/nopic.png' : __PUBLIC__.'/images/nohead.png';
	$file=pathinfo($img);
	$thumbname=$file['dirname'].'/thumb_'.$maxWidth.'_'.$maxHeight.'.'.basename($img);
	$img = Image::thumb($img,$thumbname,$type='',$maxWidth,$maxHeight,true,true,true);
	if(empty($img)) return !($headpic) ? __PUBLIC__.'/images/nopic.png' : __PUBLIC__.'/images/nohead.png';
	if (file_exists($img)){
		if (preg_match("/^(".preg_quote(__UPDURL__, '/')."|".preg_quote(__UPDDIR__, '/').")(.*)$/", $img, $matches)) $img = $matches[2];
	}
	return __UPDURL__.$img;
}
//处理活动图片
function thumbs($img,$maxWidth=60,$maxHeight=60){
	if(empty($img)) return __PUBLIC__.'/images/Unnamed.jpg';
	$file=pathinfo($img);
	$thumbname=$file['dirname'].'/thumb_'.$maxWidth.'_'.$maxHeight.'.'.basename($img);
	$img = Image::thumb($img,$thumbname,$type='',$maxWidth,$maxHeight,true,true,true);
	if(empty($img)) return __PUBLIC__.'/images/Unnamed.jpg';
	if (file_exists($img)){
		if (preg_match("/^(".preg_quote(__UPDURL__, '/')."|".preg_quote(__UPDDIR__, '/').")(.*)$/", $img, $matches)) $img = $matches[2];
	}
	return __UPDURL__.$img;
}
//处理标题
function diytitle($title,$css="h4",$color=NULL,$bold=NULL){
	if(empty($title)) return '';
	if(empty($css)) return $title;
	$style='';
	if (isset($color)) $style.="color:".$color.";";
	if (isset($bold)) $style.="font-weight:bold;";
	if (isset($css)) $title="<".$css." style=".$style.">".$title."</".$css.">";
	return $title;
}
//处理地理位置
function diyarea($title,$css="h4",$color=NULL,$bold=NULL){
	if(empty($title)) return '';
	if(empty($css)) return $title;
	$style='';
	if (isset($color)) $style.="color:".$color.";";
	if (isset($bold)) $style.="font-weight:bold;";
	if (isset($css)) $title="<".$css." style=".$style.">".$title."</".$css.">";
	return $title;
}
//查询IP位置
function getIPLoc($queryIP){
	$url = 'http://ip.qq.com/cgi-bin/searchip?searchip1=' . $queryIP;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_ENCODING, 'gb2312');
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回
	$result = curl_exec($ch);
	$result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
	curl_close($ch);
	preg_match("@<span>(.*)</span></p>@iU", $result, $ipArray);
	$loc = $ipArray[1];
	return $loc;
}
//生成唯一的值
function cp_uniqid(){
	return md5(uniqid(rand(), true));
}
//无限级分类
function Array2Tree($data, $pid){
	$tree = '';
	foreach($data as $k => $v){
		if($v['pid'] == $pid){
			//父亲找到儿子
			$v['pid'] = Array2Tree($data, $v['cid']);
			$v['fid'] = $pid;
			$tree[] = $v;
			//unset($data[$k]);
		}
	}
	return $tree;
}
//生成无限级列表
function Tree2Html($tree,$class_power){
	$html = '';
	foreach($tree as $t){
		if($t['pid'] == ''){
			$html .= "<input class='sub' name='class_power[]' type='checkbox' value='{$t['cid']}' ";
			if (in_array($t['cid'],(array)$class_power)) $html .= 'checked="checked"';
			$html .= $t['name'].'&nbsp;&nbsp';
		}else{
			$html .= "<legend onclick='selectalls('class_power[{$t['cid']}][]')'><input type='checkbox' name='class_power[]' value='{$t['cid']}'>《{$t['name']}》 </legend>";
			$html .= Tree2Html($t['pid'],$class_power);
		}
	}
	return $html ? '<fieldset class="source">'.$html.'</fieldset>' : $html ;
}
function getTreeHtml($tree,$class_power,$name='class_power'){
	$html = '';
	foreach($tree as $k=>$v){
		
		if(is_array($v['pid'])){
			$html.= '<fieldset class="source"><legend><input name="'.$name.'['.$v['fid'].'][]" id="'.$name.'[]" type="checkbox" value="'.$v['cid'].'"';
			if (in_array($v['cid'],(array)$class_power)) $html .= ' checked="checked"';
			$html .='>&nbsp;&nbsp《'.$v['name'].'》 &nbsp;&nbsp</legend>';
			$html .= getTreeHtml($v['pid'],$class_power,$name);
			$html .='</fieldset>';
		}else{
			$html .= '<input class="sub" id="'.$name.'[]" name="'.$name.'['.$v['fid'].'][]" type="checkbox" value="'.$v['cid'].'"';
			if (in_array($v['cid'],(array)$class_power)) $html .= ' checked="checked"';
			$html .= '>&nbsp;&nbsp'.$v['name'].'&nbsp;&nbsp';
			
		}
		unset($v[$k]);
		
	}
	return $html;
}