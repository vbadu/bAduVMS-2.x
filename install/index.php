<?php 
if(file_exists('../data/install.lock')){
	header("location:../");
}
define('bAdu_PATH',dirname(__FILE__).'/../system/');//注意目录后面加"/"
require(bAdu_PATH.'config/config.php');//加载配置
require(bAdu_PATH.'core/App.class.php');//加载应用控制类
$config['LOG_PATH']='./../data/log/';

function file_mode_info($file_path)
{
    if (!file_exists($file_path))
    {
        return false;
    }
    $mark = 0;
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    {
        $test_file = $file_path . '/cf_test.txt';
        if (is_dir($file_path))
        {
            $dir = @opendir($file_path);
            if ($dir === false)
            {
                return $mark;
            }
            if (@readdir($dir) !== false)
            {
                $mark ^= 1;
            }
            @closedir($dir);
            $fp = @fopen($test_file, 'wb');
            if ($fp === false)
            {
                return $mark; 
            }
            if (@fwrite($fp, 'directory access testing.') !== false)
            {
                $mark ^= 2;
            }
            @fclose($fp);
            @unlink($test_file);
            $fp = @fopen($test_file, 'ab+');
            if ($fp === false)
            {
                return $mark;
            }
            if (@fwrite($fp, "modify test.\r\n") !== false)
            {
                $mark ^= 4;
            }
            @fclose($fp);
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
            @unlink($test_file);
        }
        elseif (is_file($file_path))
        {
            $fp = @fopen($file_path, 'rb');
            if ($fp)
            {
                $mark ^= 1;
            }
            @fclose($fp);
            /* 试着修改文件 */
            $fp = @fopen($file_path, 'ab+');
            if ($fp && @fwrite($fp, '') !== false)
            {
                $mark ^= 6;
            }
            @fclose($fp);
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
        }
    }
    else
    {
        if (@is_readable($file_path))
        {
            $mark ^= 1;
        }
        if (@is_writable($file_path))
        {
            $mark ^= 14;
        }
    }
    return $mark;
}
if(@$_GET['action']=='test_data'){
	$link=@mysql_connect($_POST['DB_HOST'].':'.$_POST['DB_PORT'],$_POST['DB_USER'],$_POST['DB_PWD']);
	if(!$link){
		echo '数据库连接失败，请检查连接信息是否正确！';
		return;
	}
	if($_POST['create']==1){
		echo 1;
		return;
	}
	$status=@mysql_select_db($_POST['DB_NAME'],$link);
	if($status){
		echo 1;
	}else{
		echo '数据库连接成功，请先建立数据库！';
	}
	return;
}
class GetChar{
	function getCode ($length = 5, $mode = 0){
		switch ($mode) {
		default:
		$str = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		break;
		}
	
		$result = '';
		$l = strlen($str)-1;
		$num=0;
	
		for($i = 0;$i < $length;$i ++){
			$num = rand(0, $l);
			$a=$str[$num];
			$result =$result.$a;
		}
	return $result.'_';
	}
}
//修改配置的函数 
function set_config($array)
{
	 $config_file=bAdu_PATH.'config/data.php';
	 if(empty($array)||!is_array($array))
	 {
		 return false;
	 }

	 $config=file_get_contents($config_file);//读取配置
 	 foreach($array as $name=>$value)
     { 
		$name=str_replace(array("'",'"','['),array("\\'",'\"','\['),$name);//转义特殊字符，再传给正则替换
		if(is_string($value)&&!in_array($value,array('true','false','3306')))
		{
			$value="'".$value."'";//如果是字符串，加上单引号
		}
		$config=preg_replace("/(\\$".$name.")\s*=\s*(.*?);/i", "$1={$value};", $config);//查找替换
	 }
	//写入配置
	if(file_put_contents($config_file,$config))
	return true;
	else 
	return false; 
}





$step=intval($_GET['step']);
$steps=$step+1;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>bAduVMS 八度志愿者管理系统安装向导</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
	<script src="css/jquery.js"></script>
</head>
<body>
<div id="install">
	<form action="index.php?step=<?php echo $steps;?>" method="post" name="form" id="form">
	<?php if (empty($step)){?> 
	<h1>bAduVMS使用协议</h1>
    <div class="content">
    版权所有 &copy; 2016-2017 上饶市八度印象科技有限公司保留所有权利。<br />

　　本软件（指《bAduVMS 八度志愿者管理系统》v 2.0 版）受国际版权巴黎公约和《中华人民共和国著作权法》保护。本软件授权使用协议书是一份公司与用户之间的合法协议。<br>　　一、本公司（指上饶市八度印象科技有限公司，下同）保证<br>　　1、 保证此授权书是真实的、合法的；<br>　　2、 保证对本软件（程序和全部电子数据）拥有合法的版权和著作权，并在需要的时候负责提供相关证明材料；<br>　　3、 保证本软件不含任何病毒，无明显错误，在符合软件需求的系统环境下能正常使用；<br>
　　4、 保证对合法用户提供必要的技术支持和售后服务。从购买之日算起，用户购买服务期为1年。<br>
　　二、本公司授予用户下述权利 <br>
　　1、 将本软件安装至指定使用环境下的计算机，每套软件只授权在一台机器上长期使用；<br>
　　2、 本软件只限购买者本人（本单位）使用，如转让或转卖，本公司将无法提供技术支持和售后服务。<br>
　　3、 用户因学习、研究、试用安装在本地服务器使用，不需要经本公司授权。<br>
　　4、本软件可免费授权给公益团体使用，但第三方计算机信息服务公司利用本软件组合销售或搭配附送给政府、企业、非营利性机构、公益团体使用，均需得到本公司授权。<br>
　　5、 本授权合同书允许在不损害本公司利益的前提下自由使用本软件及相关资料。所有未在授权合同书中授予使用者的权利，均归本公司所有。　<br>
　　三、被授权人所应该履行的义务<br>
　　1、 禁止复制和扩散光盘；<br>
　　2、 禁止以任何方式将本软件中的部分或全部数据用于商业目的；<br>
　　3、 禁止对本软件进行反编译、解密或其他破坏原始程序设计的操作；<br>
　　4、 可以对程序进行二次开发，但是二次开发后的软件也禁止公开发布包括禁止对软件进行改名发布，禁止以任何形式对bAduVMS形成竞争；<br>
　　四、违约责任及其他 <br>　　1、 若发生损害对本公司利益的侵权行为，我们将采取包括法律手段在内的各种措施保护我们的正当权益，并视其损害的程度索赔，赔偿金额不低于1万元；<br>　　2、 因本授权所发生的一切争执，由双方协商解决；协商不能解决时，可通过上饶市信州区区人民法院进行裁决。<br>
　　若您以上授权协议全部内容，并慎重承诺履行以上条款。请继续安装。<br />
    </div>
    <div class="menu">
    	<button type="button" onclick="no()">不同意</button>
        <button type="button" class="submit" onclick="window.location.href='index.php?step=1'">同意</button>
    </div>
	<script>
    function no(){
        alert('感谢您对bAduVMS的支持！');
        window.close();
    }
    </script>
	<?php }elseif ($step==1){?> 
  <h1>bAduVMS安装环境测试</h1>
  <div class="content">
    <div class="title">系统所需功能支持检测</div>
    <div class="list">
      <div class="name">图像处理：</div>
      <div class="value"> <span class="load" id="image">&nbsp;</span> </div>
    </div>
    <div class="list">
      <div class="name">远程获取：</div>
      <div class="value"> <span class="load" id="getfile">&nbsp;</span> </div>
    </div>
    <div class="list">
      <div class="name">ZIP解压：</div>
      <div class="value"> <span class="load" id="zip">&nbsp;</span> </div>
    </div>
    <div class="title">系统所需目录读写权限</div>
    <span id="dir" class="load">
    
    </span>
  </div>
  <div class="menu">
    <button type="button" class="submit" onclick="window.location.href='index.php?step=2'">准备完毕进入安装</button>
  </div>
<script>
test_image();
function test_image(){
	setTimeout(function(){
		<?php if (function_exists("imageline")==1){
			echo "$('#image').html('<font color=green><b>√</b></font>');";
		} else {
			echo "$('#image').html('<font color=green><b>×</b></font>');";
		} ?>
		$('#image').removeClass('load');
		test_getfile();
	},200);
}
function test_getfile(){
	setTimeout(function(){
		<?php
	 if (function_exists("curl_init")==1){
		echo "$('#getfile').html('<font color=green><b>√</b></font>');";
		} else if (function_exists("fsockopen")==1){
		echo "$('#getfile').html('<font color=green><b>√</b></font>');";
	 	}else{
		echo "$('#getfile').html('<font color=green><b>×</b></font>');";
		}
	  ?>
		$('#getfile').removeClass('load');
		test_zip();
	},200);
}
function test_zip(){
	setTimeout(function(){
		<?php if (function_exists("zip_open")==1){
			echo "$('#zip').html('<font color=green><b>√</b></font>');";
		} else {
			echo "$('#zip').html('<font color=green><b>×</b></font>');";
		} ?>
		$('#zip').removeClass('load');
		test_dir();
	},200);
}
function test_dir(){
	setTimeout(function(){
		html='';
		<?php
  		$dir=array('data','system/config','upload');
  		foreach ($dir as $value) { ?>
		html+='<div class="list">';
      	html+='<div class="name"><?php echo $value; ?></div>';
    	<?php if(file_mode_info('../'.$value.'/')>11){
   		?>
		html+='<div class="value"><font color=green><b>√</b></font></div>';
		<?php }else{ ?>
		html+='<div class="value"><font color=red><b>x</b></font></div>';
		<?php } ?>
		html+='</div>';
		<?php } ?>
		$('#dir').html(html);
  
		$('#dir').removeClass('load');
	},200);
}
</script>
	<?php }elseif ($step==2){
	$code = new GetChar;	
	?> 
  <h1>bAduVMS安装配置</h1>
  <div class="content">
    <div class="list">
      <div class="name">数据库地址：</div>
      <div class="value"><input type="text" class="input" name="DB_HOST" id="DB_HOST" value="localhost" onblur="test_data()" /></div>
    </div>
    <div class="list">
      <div class="name">数据库端口：</div>
      <div class="value"><input type="text" class="input" name="DB_PORT" id="DB_PORT" value="3306" onblur="test_data()" /></div>
    </div>
    <div class="list">
      <div class="name">数据库名称：</div>
      <div class="value"><input type="text" class="input" name="DB_NAME" id="DB_NAME" value="bAduVMS"  onblur="test_data()" /> </div>
      <div class="tip"> <input name="create" type="checkbox" id="create" value="1" checked="checked" onclick="test_data()" />自动创建</div>
    </div>
    <div class="list">
      <div class="name">数据库用户名：</div>
      <div class="value"><input type="text" class="input" name="DB_USER" id="DB_USER" value="root"  onblur="test_data()" /></div>
    </div>
    <div class="list">
      <div class="name">数据库密码：</div>
      <div class="value"><input type="text" class="input" name="DB_PWD" id="DB_PWD" value="" onblur="test_data()" /></div>
    </div>
    <div class="list">
    <div class="name">数据库状态：</div><div class="msg" style="float:left;"><span style="color:#666">等待检测中...</span></div>
    </div>
    <div class="list">
      <div class="name">数据表前缀：</div>
      <div class="value"><input type="text" class="input" name="DB_PREFIX"  value="bd_" id="DB_PREFIX" /></div>
    </div>
    <div class="list">
      <div class="name">安全加密码：</div>
      <div class="value"><input type="text" class="input" name="spot" value="<?php echo $code->getCode(); ?>" /></div>
    </div>
  </div>
  <div class="menu">
    <input name="KEY" id="KEY" type="hidden" value="<?php echo $code->getCode(10); ?>" />
    <button type="button" class="submit" onclick="ins_form();" >准备完毕进入安装</button>
  </div>
<script>
function ins_form(){
	test_data();
	if($('#test_conn').val()==0){
		return false();
	}else{
		$("form").submit();
	}
}

function test_data(){
	 val=$('#create').attr("checked");
     if(val){
		 create=1;
     }else{
     	create=0;
     } 
	 $.post(
	 'index.php?action=test_data',
	 {
		 DB_HOST:$('#DB_HOST').val(),
		 DB_PORT:$('#DB_PORT').val(),
		 DB_NAME:$('#DB_NAME').val(),
		 DB_USER:$('#DB_USER').val(),
		 DB_PWD:$('#DB_PWD').val(),
		 create:create
	 },
	 function(html){
		 if(html==1){
			 $('.msg').html('<span style="color:green">数据库连接成功</span>');
			 $('#test_conn').val('1');
		 }else{
			 $('.msg').html('<span style="color:red">'+html+'</span>');
			 $('#test_conn').val('0');
		 }
	 },'html');
}

</script>
	<?php }elseif ($step==3){
		$app=new App($config);//以类库模式执行
		$config=$_POST;//接收表单数据
		if(empty($config))
		{
			Error::show('请填写数据库参数');
		}
		$model=new Model($config);//实例化模型类
		$sql="CREATE DATABASE IF NOT EXISTS `".$config['DB_NAME']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$model->query($sql);//如果指定数据库不存在，则尝试创建
		$model->db->select_db($config['DB_NAME']);//选择数据库
		$ins=new Install();//实例化数据库安装类
		$DB_PREFIX=in($_POST['DB_PREFIX']);
		if(empty($DB_PREFIX)){
			$DB_PREFIX='bd_';
		}
		$sql_array=Install::mysql('css/db.sql','bd_',$DB_PREFIX); 
		//执行数据库操作
		foreach($sql_array as $sql)
		{
			$model->db->query($sql);//安装数据
		}
		//修改配置文件
		$config_array=array();
		foreach($config as $key=>$value)
		{
			$config_array["config['".$key."']"]=$value;	
		}
		if(!set_config($config_array))
		{
			Error::show('配置文件写入失败！');
		}
		//安装成功，创建锁定文件
		$data_dir=dirname(__FILE__).'/../data/';
		if(!is_dir($data_dir))
			@mkdir($data_dir);
		@fopen($data_dir.'install.lock','w');
	?> 
  <h1>bAduVMS安装成功！</h1>
    <div class="content">
    恭喜您已成功安装八度志愿者管理系统！<br>
　　以下是安装须知信息：<br />
    <div class="list">
      <div class="name">管理员帐号：</div>
      <div class="value">admin</div>
    </div>
    <div class="list">
      <div class="name">管理员密码：</div>
      <div class="value">adminadmin</div>
    </div>
    <div class="list">
      <div class="name">其他须知：</div>
      <div class="value">1、安装成功后请删除本目录install。<br>2、后台管理目录admin可以更改，建议更改。</div>
    </div>

    </div>
  <div class="menu">
    <button type="button" class="submit" onclick="window.location.href='/'">进入首页</button>
    <button type="button" class="submit" onclick="window.location.href='/admin'">进入后台</button>
  </div>
	<?php }?> 
    </form>
</div>
</body>
</html>