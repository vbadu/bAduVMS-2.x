<?php
/**
 * 
 * 友情连接控制器
 * @author killerbc
 *
 */
class flinksMod extends commonMod{
	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('flinks',true);
    }
    
    public function index(){
    	//分页处理
        //分页信息
        $url = __URL__ . '/index/page-{page}'; //分页基准网址
    	$listRows = 50;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
        
        //内容列表
        $this->list=model('flinks')->file_list($limit);
        //统计总内容数量
        $count=model('flinks')->count();
        $this->assign('page', $this->page($url, $count, $listRows));
		
		//权限部分
        if(model('user_group')->model_power('flinks','edit')){
            $this->edit_power=true;
        }
        if(model('user_group')->model_power('flinks','del')){
            $this->del_power=true;
        }
		$this->show();  
    }
    
    public function add(){
        $type=model('flinks')->select_type();
        $this->assign('type', $type);
   	 	$this->display();
    }
    
    public function add_save(){
    	if(model('flinks')->add($_POST)){
            $this->msg('友情连接添加成功！',1);
        }else{
            $this->msg('友情连接添加失败',0);
        }
    }
    
    public function edit(){
    	$id=intval($_GET['id']);
        if(empty($id)){
            $this->alert('参数传递错误！',0);
        }
        $info = model('flinks')->get($id);
        $cid=$info['cid'];
        $type=model('flinks')->select_type($cid);
        $this->assign('type', $type);
        $this->assign('info', $info);
        $this->display();
    }
    
    public function edit_save(){
    	$id=intval($_POST['id']);
        if(empty($id)){
            $this->alert('参数传递错误！',0);
        }
    	$status=model('flinks')->eidt($id,$_POST);
        if($status){
            $this->msg('友情连接编辑成功！',1);
        }else{
            $this->msg('友情连接编辑失败',0);
        }
    }
    public function del(){
     	$id=intval($_POST['id']);
        if(empty($id)){
            $this->msg('参数传递错误！',0);
        }
        
        $status=model('flinks')->del($id);
        if($status){
            $this->msg('友情连接删除成功！',1);
        }else{
            $this->msg('友情连接删除失败',0);
        }
    }
	
	//排序
    public function sequence(){
        $id=intval($_POST['id']);
        $sequence=intval($_POST['sequence']);
        $status=model('flinks')->sequence($id,$sequence);
		if($status){
            $this->msg('排序成功！',1);
        }else{
            $this->msg('排序失败',0);
        }
    }
}