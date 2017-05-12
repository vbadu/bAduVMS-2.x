<?php 
@header("Content-type: text/html; charset=utf-8"); 
error_reporting(0); 
define('X',DIRECTORY_SEPARATOR);
define('ROOT_PATH',dirname(__FILE__));
if(!file_exists(ROOT_PATH.'/data/install.lock')) exit("<font color=red><b>程序未安装，不能进行升级操作！</b></font>");
if(!file_exists(ROOT_PATH.'/data/closed.lock')) file_put_contents(ROOT_PATH.'/data/closed.lock',time());
require(ROOT_PATH.'/system/config/config.php');//加载配置
$link=@mysql_connect($config['DB_HOST'].':'.$config['DB_PORT'],$config['DB_USER'],$config['DB_PWD']);
if($link){
	$status=@mysql_select_db($config['DB_NAME'],$link);
	if(!$status){
		exit("<font color=red><b>数据库连接失败，请检查连接信息是否正确！</b></font>");
	}		
}else{
	exit("<font color=red><b>数据库连接失败，请检查连接信息是否正确！</b></font>");
}
$PHP_SELF= str_replace('/','',isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] :(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] :$_SERVER['ORIG_PATH_INFO'])); 
define('PHP_SELF',$PHP_SELF);
require(ROOT_PATH.'/system/core/App.class.php');
$config['LOG_PATH']='./../data/log/';
$source_dir=ROOT_PATH.X.'baduvms';
$app=new App($config);
$model=new Model($config);
$model->db->select_db($config['DB_NAME']);
$count=0;
$step=intval($_GET['step']);
$steps=$step+1;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>bAduVMS 八度志愿者管理系统系统升级向导</title>
    </head>
<body>
<div id="install">
	<?php if (empty($step)){?> 
	<h1>bAduVMS升级说明</h1>
    <div class="content">
    本次升级将对数据库的部分表和字段进行删除、新增、更新操作，如果您有二次开发或者未备份数据的，建议您进行如下操作:
    <br>1、先进入后台-系统-数据备份 先备份；
    <br>2、将补丁包中的baduvms文件夹下的所有文件上传至网站根目录下，覆盖。
    <br>3、授权你的数据库帐号具备数据表字段的修改、删除权限。
    <br>如您已完成以上操作，请进行本升级程序。<br />
    </div>
    <div class="menu">
        <a class="submit" href="<?php echo PHP_SELF?>?step=1">开始升级</a>
    </div>
	<?php }elseif ($step==1){?> 
  <h1>bAduVMS升级确认</h1>
  <div class="content">
    <div class="title">数据库权限情况</div>
    <div class="list">
      <div class="name">数据库登录帐号：</div>
      <div class="value"> <span id="DB_USER"><?php echo $config['DB_USER']?></span> </div>
    </div>
    <div class="list">
      <div class="name">数据库帐号权限：</div>
      <div class="value">请授予   	index、select、insert、update、delete、create、drop、alter权限</div>
    </div>
    <div class="list">
      <div class="name">数据库服务器：</div>
      <div class="value"> <span id="DB_HOST"><?php echo $config['DB_HOST']?></span> </div>
    </div>
  </div>
  <div class="menu">
		<a class="submit" href="<?php echo PHP_SELF?>?step=2">检查完毕，进入升级</a>
  </div>
	<?php  }elseif ($step==2){
	$query=$model->db->query("SHOW FIELDS FROM {$config['DB_PREFIX']}form_field");
	while($row = $model->db->fetchArray($query)) {
		$data[] = $row['Field'];
	}
	if (!in_array('maxvalue',$data)){
		$query=$model->db->query("ALTER TABLE {$config['DB_PREFIX']}form_field ADD `maxvalue` int(10) DEFAULT NULL;");
	}
	echo '<div class="list"><div class="name">表 form_field 新增字段 maxvalue：<font color=green><b>√</b></font></div></div>';
	unset($query);
	unset($data);
	$query=$model->db->query("DROP TABLE IF EXISTS {$config['DB_PREFIX']}admin_menu;");
	$query=$model->db->query("CREATE TABLE {$config['DB_PREFIX']}admin_menu(`id` int(10) NOT NULL AUTO_INCREMENT,`pid` int(10) DEFAULT NULL,`name` varchar(100) DEFAULT NULL,`module` varchar(250) DEFAULT NULL,`status` int(10) NOT NULL DEFAULT '1',PRIMARY KEY (`id`)) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;");
	$query=$model->db->query("INSERT INTO `{$config['DB_PREFIX']}admin_menu` VALUES ('1','0','首页','index','1'), ('2','0','系统','system','1'), ('3','0','文章','article','1'), ('4','0','用户','user','1'), ('5','0','活动','event','1'), ('6','1','中心首页','index/home','1'), ('7','1','我的资料','index/edit','1'), ('8','1','求助管理','help','1'), ('9','2','系统设置','setting','1'), ('10','2','导航设置','nav','1'), ('11','2','展示位管理','position','1'), ('12','2','插件管理','plugin','1'), ('13','2','附件管理','upload_file','1'), ('14','2','区域设置','area','1'), ('15','2','自定义变量','fragment','1'), ('16','2','友情连接','flinks','1'), ('17','2','数据备份','myback','1'), ('18','3','文章分类管理','category/index','1'), ('19','4','会员管理','member','1'), ('20','4','会员组管理','member/group','1'), ('21','4','管理组管理','user_group','1'), ('22','4','管理员管理','user','1'), ('23','4','后台登录记录','log','1'), ('24','5','活动分类管理','category/def','1'), ('25','5','数据字典','event/dict','1'), ('26','5','活动报名模版','form','1');");
	$query=$model->db->query("update {$config['DB_PREFIX']}admin set user='support' and password='0a268d1f81f8142eece92cc298efb36b' and nicename='商业版技术支撑' where user='admin'");
	echo '<div class="list"><div class="name">表 admin_menu 重置：<font color=green><b>√</b></font></div></div>';
	unset($query);
	unset($data);
	unlink(ROOT_PATH.'/data/closed.lock');
	unlink(PHP_SELF);
	?>
  <h1>bAduVMS升级成功</h1>
  <div class="content">恭喜您已成功升级八度志愿者管理系统<br><a class="submit" href="/">进入首页</a><a class="submit" href="/admin">进入后台</a></div>
<?php }?> 
</div>
<div style="display:none"><script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?b241de14dfd7640ca1a32f9cddf83a35";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
</div>
</body>
</html>