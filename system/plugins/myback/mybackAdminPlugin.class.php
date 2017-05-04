<?php
/*
 数据库备份插件
制造时间:2013-6-10
*/
class mybackAdminPlugin extends common_pluginMod
{
    public function __construct()
    {
        $_GET['_module']='myback';
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('myback',true);
    }
    //首页    
    public function index()
    {
        //模板内赋值
        if (!is_array($this->config)) $this->msg('配置文件未加载 。。。');
        $db	= new Dbbak($this->config['DB_HOST'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8','../data/dbback/');
        //查找数据库内所有数据表
        $this->tableArry = $db->getTables();
        $this->dbname    = 	'back_'.$this->config['ver'].'_'.date("his",time()).'_';
        $this->filelist =$this->getfile();
        echo '<!--';
        print_r($this->filelist);  
        echo '-->';
        $this->show('admin_index.html');
    }	
	//备份
	public function back()
	{
       //模板内赋值
		$file_name = time(); //目录
		$back_name = $_POST['back_name'];
		$back_time = date("Y-m-d h:i:s",time());
        $db	= new Dbbak($this->config['DB_HOST'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8','../data/dbback/'.$file_name.'/');
		//备份并生成sql文件
		$db_size = $_POST['dbsize'];
		if ($_POST['back'] == 1) {
			$data = $db->getTables();
		}
		else
		{
			$data = $_POST['db'];
		}
		if($db->exportSql($data,$db_size))
		{	
			$xml = '<?xml version="1.0" encoding="utf-8"?>
<back>
	<back_name>'.$back_name.'</back_name>
	<back_time>'.$back_time.'</back_time>
	<back_ver>'.$this->config['ver'].'</back_ver>
</back>';
			$file=__ROOTDIR__.'/data/dbback/'.$file_name.'/ver.xml';
      		@file_put_contents($file, $xml);
			$this->msg('备份成功',1);
		}
		else
		{	
			$this->msg('备份失败',0);
		}
 		
	}

   //删除
	public function del()
	{
		$filename		= $_POST['filename'];
		$this->del_dir('../data/dbback/'.$filename);
		$this->msg('删除成功',1);
	}
    //遍历删除目录和目录下所有文件
	protected function del_dir($dir){
		if (!is_dir($dir)){
			return false;
		}
		$handle = opendir($dir);
		while (($file = readdir($handle)) !== false){
			if ($file != "." && $file != ".."){
				is_dir("$dir/$file")?	del_dir("$dir/$file"):@unlink("$dir/$file");
			}
	}
		if (readdir($handle) == false){
			closedir($handle);
			@rmdir($dir);
		}
	}
	//导入
	public function daor()
	{
		$name = $_POST['file'];
		$file = '../data/dbback/'.$name.'/';
		$db	= new Dbbak($this->config['DB_HOST'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8',$file);
		if($db->importSql($file))
		{
			$this->msg('数据恢复成功！',1);
		}
		else
		{
			$this->msg('数据恢复失败！',0);
		}		
	}
	//获取备份文件列表
	public function getfile()
	{
		$f_open = "../data/dbback/";
		if (!is_dir($f_open)){
			@mkdir($f_open,0777,true);
		}
		$handle = opendir($f_open);
		while (($file = readdir($handle)) !== false)
		{
			if ($file != "." && $file != ".." && file_exists($f_open.'/'.$file.'/ver.xml'))
			{
				$fileData[] = $file;
			}		 
		}        
		closedir($handle);
		if ($fileData) {
			# code...
			foreach ($fileData as $key => $value) {
				# code...
				$xml=file_get_contents($f_open.'/'.$value.'/ver.xml');
				$info=Xml::decode($xml);
				$file_Data[$key]['file_name'] = $value;
				$file_Data[$key]['back_name'] = $info['back']['back_name'];
				$file_Data[$key]['back_time'] = $info['back']['back_time'];
				$file_Data[$key]['back_ver'] = $info['back']['back_ver'];
			}
		}
		return $file_Data;
	}

	
}
?>