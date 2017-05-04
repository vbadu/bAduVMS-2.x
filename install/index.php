<?php 
@header("Content-type: text/html; charset=utf-8"); 
error_reporting(0); 
if(file_exists('../data/install.lock'))header("location:../");
define('bAdu_PATH',dirname(__FILE__).'/../system/');//注意目录后面加"/"
require(bAdu_PATH.'config/config.php');//加载配置
require(bAdu_PATH.'core/App.class.php');//加载应用控制类
$config['LOG_PATH']='./../data/log/';function file_mode_info($file_path)
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
	 }	 $config=file_get_contents($config_file);//读取配置
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
	<h1>bAduVMS使用授权协议</h1>
    <div class="content">
      <p><strong>用户须知</strong></p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、中文版授权许可协议 适用于全体用户；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、本软件英文简称为"bAduVMS"，"bAduVMS"中文全称为"八度志愿服务管理系统"，以下简称"本软件"或"bAduVMS"；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、上饶市八度印象科技有限公司为本软件的开发者，依法独立拥有本软件著作权。上饶市八度印象科技有限公司网址为 http://www.bAduVMS.com；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4、本授权协议适用且仅适用于 bAduVMS x.x.x 版本，上饶市八度印象科技有限公司拥有对本协议最终解释权；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5、本软件受国际版权巴黎公约和《中华人民共和国著作权法》保护。本软件授权使用协议书是一份公司与用户之间的合法协议。本协议是您与本公司（指上饶市八度印象科技有限公司，下同)之间关于您使用本公司保证提供的各种软件产品及服务的法律协议。无论您是个人或组织、盈利与否、用途如何（包括以学习和研究为目的），均需仔细阅读本协议，包括免除或者限制本公司责任的免责条款及对您的权利限制。请您审阅并接受或不接受本服务条款。如您不同意本服务条款及/或本公司随时对其的修改，您应不使用或主动取消本公司提供的产品。否则，您的任何对本公司产品中的相关服务的注册、登陆、下载、查看等使用行为将被视为您对本服务条款全部的完全接受，包括接受本公司对服务条款随时所做的任何修改。本服务条款一旦发生变更, 本公司将在网页上公布修改内容。修改后的服务条款一旦在网站上公布即有效代替原来的服务条款。您可随时登陆本公司官方网站查阅最新版服务条款。如果您选择接受本条款，即表示您同意接受协议各项条件的约束。如果您不同意本服务条款，则不能获得使用本服务的权利。您若有违反本条款规定，本公司有权随时中止或终止您对本公司产品的使用资格并保留追究相关法律责任的权利。在理解、同意、并遵守本协议的全部条款后，方可开始使用本公司产品。您可能与本公司直接签订另一书面协议，以补充或者取代本协议的全部或者任何部分。本公司拥有本软件的全部知识产权。本软件只供许可协议，并非出售。本公司只允许您在遵守本协议各项条款的情况下复制、下载、安装、使用或者以其他方式受益于本软件的功能或者知识产权。</p>
      <p><strong>一、协议许可的权利</strong></p>
      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、您可以在完全遵守本许可协议的基础上，将本软件应用于非商业用途或因学习、研究、试用安装在本地服务器使用，而不必支付软件版权许可费用；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、您可以在协议规定的约束和限制范围内修改本公司产品源代码(如果被提供的话)或界面风格以<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、您拥有使用本软件构建的网站中全部信息、会员资料、文章及相关信息的所有权，并独立承担与使用本软件构建的网站内容的审核、注意义务，确保其不侵犯任何人的合法权益，独立承担因使用本公司软件和服务带来的全部责任，若造成本公司或用户损失的，您应予以全部赔偿； <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4、若您需将本公司软件或服务用户商业用途，必须另行获得本公司的书面许可，您在获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持期限、技术支持方式和技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5、您可以依据所购买的软件技术服务中确定的技术支持期限、技术支持方式和技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。正式授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6、您可以从本公司提供的应用中心服务中下载适合您网站的应用程序，但应向应用程序开发者/所有者支付相应的费用。</p>
      <p><strong>二、协议规定的约束和限制</strong></p>
      <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、 未获本公司书面商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目或实现盈利的网站）。购买商业授权请登陆http://www.baduvms.com参了解详情；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、 本软件只限购买者本人（本单位）使用，如转让或转卖，本公司将无法提供技术支持和售后服务；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、 本授权合同书允许在不损害本公司利益的前提下自由使用本软件及相关资料。所有未在授权合同书中授予使用者的权利，均归本公司所有；　<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4、禁止对本软件进行反编译、解密或其他破坏原始程序；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5、未支付版权许可费用下，禁止去掉系统程序文件及各显示页面中含有"bAduVMS"或"上饶市八度印象科技有限公司"的相关标识及本公司连接，一旦发现取得则自动终止服务及升级权利；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6、 可以对程序进行二次开发，但是二次开发后的软件也禁止公开发布包括禁止对软件进行改名发布，禁止以任何形式对本软件形成竞争；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7、不得对本软件或与之关联的软件授权进行出租、出售、抵押或发放子许可证；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;8、禁止在本软件 的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;9、如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</p>
      <p><strong>三、有限担保和免责声明</strong></p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买软件技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、上饶市八度印象科技有限公司不对使用本软件时涉及的文章或信息承担责任。</p>
     <p><strong>四、违约责任及其他</strong></p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1、 若发生损害对本公司利益的侵权行为，我们将采取包括法律手段在内的各种措施保护我们的正当权益，并视其损害的程度索赔，赔偿金额不低于1万元；<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2、 因本授权所发生的一切争执，由双方协商解决；协商不能解决时，可通过上饶市信州区区人民法院进行裁决。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3、有关本软件最终用户授权协议、正式授权与技术服务的详细内容，均由本软件官方网站独家提供。上饶市八度印象科技有限公司拥有在不事先通知的情况下，修改授权协议和服务价目表的权力，修改后的协议或价目表自改变之日起生效。如果用户不接受修改后的协议或价目表，请立即停止使用本软件和服务，用户继续使用本软件和服务将被视为已接受了修改后的协议或价目表。
<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4、电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始使用 本软件，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
</p>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;若您同意以上授权协议全部内容，并慎重承诺履行以上条款请继续安装本软件。
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;版权所有 &copy; 2016-2017 上饶市八度印象科技有限公司保留所有权利。
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
}function test_data(){
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
}</script>
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
    </div>    </div>
  <div class="menu">
    <button type="button" class="submit" onclick="window.location.href='/'">进入首页</button>
    <button type="button" class="submit" onclick="window.location.href='/admin'">进入后台</button>
  </div>
	<?php }?> 
    </form>
</div>
</body>
</html>