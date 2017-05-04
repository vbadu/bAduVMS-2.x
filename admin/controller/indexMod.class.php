<?php
class indexMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('index',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
	}
    public function index()
    {
		$this->user=model('user')->current_user();
        $this->menu_list=model('menu')->main_menu();
        $this->display();
    }
    public function home(){
		$this->check_app_power('index/home',true);
        require __CONFIG__;
        $this->config_array=$config;
		$update=$path=__ROOTDIR__.'/data/update.lock';
		$cookie_time=time();
		$check = false;
		if (is_file($update)){
			$html = file_get_contents($update);
			$html = explode('|', $html);
			if ($html[1]>0 && ($html[1]>$config['ver'])) $check = true;
			$cookie_time=(empty($html[0]))?$cookie_time:$html[0];
		}
		$cookie_time=ceil((time()-$cookie_time)/86400); 
		if ($cookie_time>7 OR $check==true){
			$newver=Http::doGet("http://down.vbadu.net/".strtolower($config['ver_name']).'/?ver='.$config['ver']);
			if (strlen($newver)>560) $newver=$config['ver'];
			if ($cookie_time>7) file_put_contents($update,time().'|'.$newver);
		}else{
			$newver=$config['ver'];
		}
		$this->newver=$newver;
    	$this->user=model('user')->current_user();
        $this->show();
    }
    public function _empty(){
		unlink(__ROOTDIR__.'/data/closed.lock');
		$this->msg('您不是付费用户，请到官方网站下载补丁包自助升级！',0);
		
    }
    public function getAreaJson(){
		if (!is_numeric($_GET['id'])) exit(json_encode(array('code'=>0)));
		$id=intval($_GET['id']);
		$where['show']=1;
		$where['pid']=$id;
		$list=model('badu')->select($where,'area',$limit,'sort asc,cid asc');
 		//dump($list);
		if(!empty($list)){
			$data=array();
			foreach ($list as $k=> $v) {
				$data[$k]['id']=$v['cid'];
				$data[$k]['value']=$v['name'];
				$data[$k]['parent']=$v['pid'];
				$data[$k]['isover']=($v['type']>4)?'true':'false';//isParent
			}
			$json["code"]=1;
			$json["data"]=$data;
		}else{
			$json["code"]=0;
			$json["data"]=array();
		}
		echo json_encode($json);
	}
    public function edit(){
		$this->check_app_power('index/edit',true);
        $this->view()->assign(module('user')->edit_info());
    }
    public function tool_bom(){
        $str=$this->tool_bom_dir(__ROOTDIR__);
        $this->msg($str.'所有BOM清除完毕！');
    }
    //清除BOM
    public function tool_bom_dir($basedir){
        if ($dh = opendir($basedir)) {
            $str='';
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' && $file != '..'){
                    if (!is_dir($basedir."/".$file)) {
                        if($this->tool_bom_clear("$basedir/$file")){
                            $str.= "文件 [$basedir/$file] 发现BOM并已清除<br>";
                        }
                    }else{
                        $dirname = $basedir."/".$file;
                        $this->tool_bom_dir($dirname);
                    }
                }
            }
        closedir($dh);
        }
        return $str;
    }
    public function tool_bom_clear($filename){
        $contents = file_get_contents($filename);
        $charset[1] = substr($contents, 0, 1);
        $charset[2] = substr($contents, 1, 1);
        $charset[3] = substr($contents, 2, 1);
        if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
                $rest = substr($contents, 3);
                $this->rewrite ($filename, $rest);
                return true;
        }
    }
    public function rewrite ($filename, $data) {
        $filenum = fopen($filename, "w");
        flock($filenum, LOCK_EX);
        fwrite($filenum, $data);
        fclose($filenum);
    }
}