<?php
class menuMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
		$this->model_url=$_GET['_action'];
        if(!model('user_group')->menu_power($this->model_url,1) && $this->model_url!='error'){
        	exit('对不起，您没有该模块('.$this->model_url.')的访问权限！');
        }		
    }
    //后台首页菜单
    public function index()
    {
        $this->list=model('menu')->admin_menu(1);
        $this->display();
    }
    public function article()
    {
       $this->user=model('user')->current_user();
       $tree=model('menu')->content_menu(1,$this->user['gid']);
	   $class_power=explode(',',$this->user['class_power']);
	   //dump($tree,1);
       $data='';
       if(!empty($tree)){
			foreach ($tree as $value) {
				if(in_array($value['cid'], (array)$class_power) || $this->user['keep']==1){
					$url=' , url:"'.__APP__.'/content/index/id-'.$value['cid'].'", target:"main" , icon:"'.__PUBLICURL__.'/ztree/css/img/ico'.$value['mid'].'.gif" ';
					if($value['pw']==1){
						$purview=' , isHidden:true ';
					}else{
						$purview=' , isHidden:false ';
					}
				   $data.='{cid:'.$value['cid'].',pid:'.$value['pid'].', name:"'.$value['name'].'" '.$url.$purview.' }, '."\n";
				}
			}
			$data.='{name:" ", isHidden:true  }'."\n";
       }
       $this->class_tree=$data;
	   $this->list=model('menu')->admin_menu(3);
       $this->assign('user',$user);
       $this->assign('menu',$menu);
       $this->display();
    }
    //用户管理
    public function user() {
        $this->list=model('menu')->admin_menu(4);
        $this->display();
    }
    public function event()
    {
       $this->user=model('user')->current_user();
       $tree=model('menu')->content_menu(2,$this->user['gid']);
       $data='';
       if(!empty($tree)){
           foreach ($tree as $value) {
					$url=' , url:"'.__APP__.'/event/index/id-'.$value['cid'].'", target:"main" , icon:"'.__PUBLICURL__.'/ztree/css/img/ico'.$value['mid'].'.gif" ';
                if($value['pw']==1){
                    $purview=' , isHidden:true ';
                }else{
                    $purview=' , isHidden:false ';
                }
               $data.='{cid:'.$value['cid'].',pid:'.$value['pid'].', name:"'.$value['name'].'" '.$url.$purview.' }, '."\n";
           }
           $data.='{name:" ", isHidden:true  }'."\n";
       }
		$this->class_tree=$data;
		$this->list=model('menu')->admin_menu(5);
		$this->assign('user',$user);
		$this->assign('menu',$menu);
		$this->display();
    }
    //团队管理
    public function team() {
        $this->list=model('menu')->admin_menu(6);
        $this->display();
    }

    public function error() {
        $this->success('很抱歉，暂时没有可显示的功能！');
    }

    //系统菜单
    public function system()
    {
//系统
        $this->list=model('menu')->admin_menu(2);
//栏目$this->category=model('menu')->admin_menu(25);

        $this->display();
    }

}

?>