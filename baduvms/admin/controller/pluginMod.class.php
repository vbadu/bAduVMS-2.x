<?php
//插件管理器
class pluginMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('plugin',true);
	}
	public function index() {
        $this->list=model('plugin')->plugin_list();
		$this->show();
	}

    public function info() {
        header('Content-Type:text/html;charset=utf-8');
        $name=$_GET['name'];
        if(empty($name)){
            echo '此插件无说明信息';
            exit;
        }
        $info=model('plugin')->info($name);
        if(empty($info)){
            echo '此插件无说明信息';
        }else{ 
            echo $info['info'];
        }
    }

    //安装
    public function install() {
        $name=$_POST['name'];
        $this->alert_str($name,'',true);
        $dir = __ROOTDIR__ . '/system/plugins/' . $name;
        //获取插件信息
        $info=model('plugin')->info($name);
        if(empty($info)){
           $this->msg('插件信息获取错误！',0);
        }

        if($info['rely']){
            $rely=explode(',', $info['rely']);
            if(!empty($rely)){
                foreach ($rely as $value) {
                    if(!model('plugin')->info_data_count($value)){
                        $this->msg('没有发现依赖插件：'.$value,0);
                    }
                }
            }
        }

        //导入数据库
        $db = new Dbbak($this->config['DB_HOST'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8',$dir.'/dbbak/');
        $db->importSql('',$info['prefix'],$this->config['DB_PREFIX']);

        if(!$info['nomenu']){
            //添加菜单
            $mid=model('plugin')->add_menu($info);
            if(empty($mid)){
                $this->msg('菜单添加失败！',0);
            }
        }

        //添加模块
        $id=model('plugin')->add($info,$mid);
        @Plugin::run($name,'install',$id);
        $this->msg('插件安装成功！',1);   
    }

    //导出
    public function out()
    {
       $id=intval($_POST['id']);
        $info=model('plugin')->info_data($id);
        if(!empty($info['file'])){
            $data=@Plugin::run($info['file'],'plugin_ini_data','',true);
        }

        $data_array=array();
        if(!empty($data)){
            //执行数据库操作
            foreach ($data as $value) {
                $data_array[]=$this->config['DB_PREFIX'].$value;
            }
            //创建文件夹
            $dir=__ROOTDIR__.'/system/plugins/'.$info['file'];
            @mkdir($dir.'/dbbak/',0777,true);
            if(!file_exists($dir.'/dbbak/')){
                $this->msg('文件夹创建失败，请保证本插件目录有写入权限',0);
            }
            //导出数据库
            $db = new Dbbak($this->config['DB_HOST'],$this->config['DB_USER'],$this->config['DB_PWD'],$this->config['DB_NAME'],'utf8',$dir.'/dbbak/');
            if(!$db->exportSql($data_array,0,$sql)){
                $this->msg('数据库导出失败！',0);
            }
        }

        $old_config=model('plugin')->info($info['file']);

        //写入插件信息
        $html='<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
        $html.='<config>' . PHP_EOL;
        $html.='<name>'.$info['name'].'</name>' . PHP_EOL;
        $html.='<file>'.$info['file'].'</file>' . PHP_EOL;
        $html.='<ver>'.$info['ver'].'</ver>' . PHP_EOL;
        $html.='<author>'.$info['author'].'</author>' . PHP_EOL;
        $html.='<info>'.$old_config['info'].'</info>' . PHP_EOL;
        $html.='<prefix>'.$this->config['DB_PREFIX'].'</prefix>' . PHP_EOL;
        $html.='</config>' . PHP_EOL;

        @file_put_contents($dir.'/config.xml',$html);

        @Plugin::run($info['file'],'out',$info);
        $this->msg('插件导出完毕，请自行到plugin目录下载插件文件',1);
        
    }

    //卸载
    public function uninstall() {
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        //获取插件信息
        $info=model('plugin')->info_data($id);
        if(empty($info)){
            $this->msg('插件信息获取失败！',0);
        }
        //获取配置信息
        $config=model('plugin')->info($info['file']);
        if(empty($config)){
            $this->msg('配置信息获取失败！',0);
        }
        $dir = __ROOTDIR__ . '/system/plugins/' . $info['file'];
        if(!empty($info['file'])){
            $data=@Plugin::run($info['file'],'plugin_ini_data','',true);
        }
        @Plugin::run($info['file'],'uninstall',$info);
        //删除表
        if(!empty($data)){
            foreach ($data as $value) {
                model('plugin')->del_table($value);
            }
        }
        //删除菜单
        model('plugin')->del_menu($info['mid']);
        //删除主表
        model('plugin')->del($id);
        //删除完毕
        $this->msg($info['file']);

    }

    //删除文件
    public function del_file(){
        $name=$_POST['name'];
        $this->alert_str($name,'',true);
        $dir = __ROOTDIR__ . '/system/plugins/' . $name;

        if(del_dir($dir)){
            $this->msg('删除成功！');
        }else{
            $this->msg('删除失败！',0);
        }

    }

    //停用插件
    public function disable(){
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        //获取插件信息
        $info=model('plugin')->info_data($id);
        if(empty($info)){
            $this->msg('插件信息获取失败！',0);
        }
        //禁用
        model('plugin')->status($id,0);
        model('plugin')->status_menu($info['mid'],0);

        $this->msg('插件已停用！',0);
    }

    //启用插件
    public function enable(){
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        //获取插件信息
        $info=model('plugin')->info_data($id);
        if(empty($info)){
            $this->msg('插件信息获取失败！',0);
        }
        //启用
        model('plugin')->status($id,1);
        model('plugin')->status_menu($info['mid'],1);

        $this->msg('插件已启用！',0);
    }

	

}