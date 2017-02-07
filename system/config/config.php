<?php
require (dirname(__file__) . '/data.php');
require (dirname(__file__) . '/ver.php'); 

//站点信息
$config['sitename']='八度义工协会1';
$config['seoname']='八度义工协会';
$config['siteurl']='http://rmb.com';
$config['keywords']='八度义工协会';
$config['description']='八度义工协会';
$config['masteremail']='admin@baduvms.cn';
$config['copyright']='八度义工协会';
$config['certauthority']='八度市义工协会';
$config['helponline']='0793-8300869';
//会员设置
$config['VIP_AUTO']=true;//是否自动生成编号
$config['VIP_CARD']=361124;//区域代码或会员编号首位代码
$config['VIP_CARD_IDS']=1;//会员编号初始编号
$config['VIP_CARD_NOW']=2;//上一次使用编号，用于生成编号参考依据
$config['VIP_CARD_DAY']=201611;//上一次使用编号，用于生成编号参考依据
$config['VIP_CARD_MOD']=true;//true会员编号采取自动累计， false采取按年月初始化初始编号，比如10号编号到9999,11号第一位自动从初始编号0001开始
$config['VIP_CARD_TPL']='{card}{yy}{mm}{dd}{id}';//会员编号规则模版

//全局开关
$config['IP_STATUS']=false; //IP获取地址状态
$config['URL_HTML_MODEL']='2'; //伪静态样式

//手机设置
$config['MOBILE_OPEN']=true; //手机版开关
$config['MOBILE_DOMAIN']='baduvms.cn';

//上传设置
$config['ACCESSPRY_SIZE']=500; //附件大小，单位M
$config['ACCESSPRY_NUM']=300; //上传数量
$config['ACCESSPRY_TYPE']='jpg,bmp,gif,png,flv,mp4,mp3,wma,mp4,7z,zip,rar,ppt,txt,pdf,xls,doc,swf,wmv,avi,rmvb,rm';//上传格式
$config['THUMBNAIL_SWIHCH']=false; //是否缩图
$config['THUMBNAIL_MAXWIDTH']=210; //缩图最大宽度
$config['THUMBNAIL_MAXHIGHT']=110; //最大高度
$config['WATERMARK_SWITCH']=false; //是否打水印
$config['WATERMARK_PLACE']=5; //水印位置
$config['WATERMARK_IMAGE']='logo.png'; //水印图片
$config['WATERMARK_CUTOUT']=true; //缩图方式
	
//模板设置
$config['TPL_TEMPLATE_PATH']='public/themes/';//模板目录，一般不需要修改
$config['TPL_INDEX']='index.html';
$config['TPL_COMMON']='common.html';
$config['TPL_TAGS']='tags.html';
$config['TPL_TAGS_PAGE']=20;
$config['TPL_TAGS_INDEX']='tags_index.html';
$config['TPL_TAGS_INDEX_PAGE']=20;
$config['TPL_SEARCH']='search.html';
$config['TPL_SEARCH_PAGE']=20;
$config['URL_HTML_SUFFIX']='.html';
//调试配置
$config['DEBUG']=true;	//是否开启调试模式，true开启，false关闭
$config['ERROR_HANDLE']=true;//是否启动内置的错误处理，如果开启了xdebug，建议设置为false

//伪静态
$config['URL_REWRITE_ON']=true;//是否开启重写，true开启重写,false关闭重写
$config['URL_MODULE_DEPR']='/';//模块分隔符
$config['URL_ACTION_DEPR']='/';//操作分隔符
$config['URL_PARAM_DEPR']='-';//参数分隔符
$config['URL_HTTP_HOST']='';//设置网址域名特殊

//静态缓存
$config['HTML_CACHE_ON']=false;//是否开启静态页面缓存，true开启.false关闭
$config['HTML_CACHE_RULE']['index']['*']=5;//缓存时间,单位：秒
$config['HTML_CACHE_RULE']['empty']['*']=5;//缓存时间,单位：秒
$config['HTML_CACHE_RULE']['search']['*']=5;//缓存时间,单位：秒
	
//数据库设置
$config['DB_CACHE_ON']=false;//是否开启数据库缓存，true开启，false不开启
$config['DB_CACHE_TYPE']='FileCache';///缓存类型，FileCache或Memcache或SaeMemcache

//模板缓存
$config['TPL_CACHE_ON']=false;//是否开启模板缓存，true开启,false不开启
$config['TPL_CACHE_TYPE']='';//数据缓存类型，为空或Memcache或SaeMemcache，其中为空为普通文件缓存


//附加
$config['KEY']='XDcFdsfeERWQ';
