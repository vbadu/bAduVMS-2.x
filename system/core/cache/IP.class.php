<?php
/**
获取ip地址的地理位置信息
基于新浪或淘宝IP库
	IP::GET($ip,$type);
 */
class IP{
	//综合地理位置
    static public function GET($ip=NULL,$type=1){
		if (empty($ip)){
			$ipAddr= self::getIp();
		}else{
			$ipAddr= $ip;
		}
		$array[0]=self::get_Area_sina($ipAddr);
		$array[1]=self::get_Area_taobao($ipAddr);
		if (empty($array[0][0]) || empty($array[0][1]) || empty($array[0][2])){
			$data=$array[1];
		}else{
			$data=$array[0];
		}
		if (empty($data[0]) || empty($data[1]) || empty($data[2])){
			$data[0] = '计算机';	
			$data[1] = '本地';
			$data[2] = '局域网';
		}
		switch ($type){
            case 0://省
                $back=$data[1];
                break;
            case 1://省市
                $back=$data[1].$data[2];
                break;
            case 2://数组
                $back=$data;
                break;
            case 3://市
             	$back=$data[2];
                break;
            default:
            	$back=$array;
                break;
        }
		return $back;
	}

	//获取ip地址
	static public function getIp()
	{
	   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		   $ip = getenv("HTTP_CLIENT_IP");
	   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		   $ip = getenv("HTTP_X_FORWARDED_FOR");
	   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		   $ip = getenv("REMOTE_ADDR");
	   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		   $ip = $_SERVER['REMOTE_ADDR'];
	   else
		   $ip = "unknown";
	   return($ip);
	}

	//对象数组化
    static public function object_to_array($stdclassobject){
		$_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
		is_array($_array)?null:$_array = array();
		foreach ($_array as $key => $value) {
			$value = (is_array($value) || is_object($value)) ? self::object_to_array($value) : $value;
			$array[$key] = $value;
		}
		return $array;
	}
	//获取sina库
    static public function get_Area_sina($ip=NULL){
		if (empty($ip)){
			$ipAddr= self::getIp();
		}else{
			$ipAddr= in($ip);
		}
		$ipInfoApi= 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ipAddr; 
		$areaInfo= http::doGet($ipInfoApi); 
		$areaInfo= self::object_to_array(json_decode($areaInfo)); 
		$array[0]=$areaInfo['country'];
		$array[1]=$areaInfo['province'];
		$array[2]=$areaInfo['city'];
		return $array;
	}
	//获取淘宝库
    static public function get_Area_taobao($ip=NULL){
		if (empty($ip)){
			$ipAddr= self::getIp();
		}else{
			$ipAddr= in($ip);
		}
		$ipInfoApi= 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ipAddr; 
		$areaInfo= http::doGet($ipInfoApi); 
		$areaInfo= self::object_to_array(json_decode($areaInfo)); 
		$array[0]=$areaInfo['data']['country'];
		$array[1]=$areaInfo['data']['region'];
		$array[2]=$areaInfo['data']['city'];
		return $array;
	}
	//获取paipai库，探测手机归属地
    static public function get_Area_paipai($mob,$type='1'){
		if (empty($mob)){
			return;
		}
		$mobInfoApi= 'http://virtual.paipai.com/extinfo/GetMobileProductInfo?&callname=test&amount=10000&mobile='.$mob; 
		$areaInfo= http::doGet($mobInfoApi); 
		preg_match('/\(  [^\)]+?  \)/x', $areaInfo, $matches);
		$areaInfo= auto_charset(substr($matches[0], 2, -2));
		foreach (explode(',',$areaInfo) as $areaInfo){
			list($k,$v)=explode(':',$areaInfo);
			$areaInfos[$k]=$v;
		}
		$areaInfo= $areaInfos; 
	    switch ($type) {
	        case '0': //只区域
	             $str = $areaInfo['province'];
	             break;
	        case '1': //只区域
	             $str = $areaInfo['cityname'];
	             break;
	        case '2'://只运营商
	             $str = substr($areaInfo['isp'], 2);
	             break;
	        case '3'://地域运营商
	             $str = $areaInfo['province'].substr($areaInfo['isp'], 2);
	             break;
			default:
				 $str = $areaInfo;
				 break;
		}
		return $str;
	}
	//获取淘宝库，探测手机归属地
	static public function get_Area_taobaoMob($mob,$type='1'){
		if (empty($mob)){
			return;
		}
		$url = "http://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel=".$mob."&t=".time();
		$content = file_get_contents($url);
		$array['cityname'] = auto_charset(substr($content, 56, 4));
		$array['province'] = $array['cityname'];
		$array['isp'] = auto_charset(substr($content, 81, 4));
		$array['isps'] = $array['cityname'].$array['isp'];
	    switch ($type) {
	        case '0':case '1': //只区域
	             $str = $array['cityname'];
	             break;
	        case '2'://只运营商
	             $str = 	$array['isp'];
	             break;
	        case '3'://地域运营商
	             $str = $array['isps'];
	             break;
			default:
				 $str = $array;
				 break;
		}
		return $str;
	}	
	
}
?>