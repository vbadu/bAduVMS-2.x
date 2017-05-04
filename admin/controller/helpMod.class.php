<?php
class helpMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('index',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('help',true);
    }
    // 分类首页
    public function index()
    {
        $type=intval($_GET['type']);
        if ($type>0){
        	$where['type']=$type;
        }
		$url = __URL__ .'/index/type-'.$type.'-{page}.html';
    	$listRows = 20;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
        $this->list=model('badu')->select($where,'help',$limit);
        //权限部分
        if(model('user_group')->model_power('help','edit')){
            $this->edit_power=true;
        }
        if(model('user_group')->model_power('help','del')){
            $this->del_power=true;
        }
        $this->type=$type;
        $count=model('badu')->count($where,'help');
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->show();
    }

    //导航菜单编辑
    public function info()
    {
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST);
			$id=intval($post['id']);
			if (empty($post['title']) || 3>strlen($post['title'])) {
				$this->msg('标题要填写！',0);
				return;
			}			
			if (empty($post['name']) || 1>strlen($post['name'])) {
				$this->msg('联系人未填写或填写不正确！',0);
				return;
			}			
			if (empty($post['tel']) || 11>strlen($post['tel']) || !is_mob($post['tel'])) {
				$this->msg('手机号码未填写或填写不正确！',0);
				return;
			}			
			if (empty($post['content']) || 5>strlen($post['content'])) {
				$this->msg('说明未填写或填写不够详细！',0);
				return;
			}			
			$data['type']=intval($post['type']);
			$data['title']=in($post['title']);
			$data['sex']=intval($post['sex']);
			$data['open']=intval($post['open']);
			$data['name']=in($post['name']);
			$data['tel']=in($post['tel']);
			$data['content']=in($post['content']);
			$data['status']=intval($post['status']);
			$data['helps']=intval($post['helps']);
			//录入模型处理
			model('badu')->set_data(array('id'=>$id),$data,'help');
			return $this->msg('更新成功!',1);	
		}		
        $id=intval($_GET['id']);
        if(empty($id)){
            $this->msg('访问的页面不存在！');
        }
		$info = model('badu')->get_info('help',$id);
        if (is_array($info)==false){
         	$this->msg('访问的页面不存在！');
        }
		if (empty($info)) $this->msg('访问的页面不存在！');
		$this->info=$info;
        $this->show();
    }

    //导航菜单删除
    public function del()
    {
        $id=intval($_GET['id']);
        if(empty($id)){
            $this->msg('访问的页面不存在！');
        }
        model('badu')->del_data(array('id'=>$id),'help');
        $this->msg('删除成功！',1);
    }

}

?>