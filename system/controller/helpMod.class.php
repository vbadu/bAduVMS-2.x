<?php
class helpMod extends commonMod
{
	public function __construct()
    {
        parent::__construct();
		$this->menu=array(
		0=>array(
                'cid'=>0,
                'name'=>'互助中心',
                'url'=>'help.html',
                ),
		1=>array(
                'cid'=>1,
                'name'=>'我需要志愿者',
                'url'=>'help/need.html',
                ),
		2=>array(
                'cid'=>2,
                'name'=>'我要捐款捐物',
                'url'=>'help/donate.html',
                ),
		);
    }
    //首页
    public function index(){
		$common['title']='互助中心';
		$common['cid']=0;
		$common['url']='help.html';
        $this->assign('common', $common);
		//入口校验
		$url = __URL__ .'/index/{page}.html';
    	$listRows = 20;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		//$where['type']=1;
		$where['open']=1;
		//列表
		$this->list=model('badu')->get_list($limit,$where,'help','id desc');
		$count=model('badu')->get_count($where,'help');
		$this->page=$this->page($url, $count, $listRows);
        $this->display('help.html');
    }
	public function _empty(){
        $id=intval(in(substr($_GET['_action'],-11)));
        if(empty($id)){
            $this->redirect(__URL__.'/index/');;
        }
		$info = model('badu')->info(array('id'=>$id,'open'=>1),'help');
        if (is_array($info)==false){
         	$this->msg('访问的页面不存在或为非公开内容！');
        }
        if ($info['type']!=1){
        	$tpl='_good';
        }
		if (empty($info)) $this->msg('访问的页面不存在或为非公开内容！');
		$this->info=$info;
		$common['title']=$this->menu[0]['name'];
		$common['cid']=0;
		$common['url']='help.html';
        $this->assign('common', $common);
		$this->display('help_info'.$tpl.'.html');
		
	}
     //内容页donate
   	 public function need(){
		$common['title']='我需要志愿者';
		$common['cid']=1;
		$common['url']='help/need.html';
        $this->assign('common', $common);
		if ($this->isPost() && is_array($_POST)){
			if (isset($_POST['data']) && count($_POST['data'])>0){
				$_POST=$_POST['data'];
			}
			$post=in($_POST);
			if (empty($post['title']) || 3>strlen($post['title'])) {
				$this->msg('请简要说明求助事项！',0);
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
				$this->msg('求助说明未填写或填写不够详细！',0);
				return;
			}			
			$data['type']=1;
			$data['title']=in($post['title']);
			$data['sex']=intval($post['sex']);
			$data['open']=intval($post['open']);
			$data['name']=in($post['name']);
			$data['tel']=in($post['tel']);
			$data['content']=in($post['content']);
			$data['status']=0;
			$data['helps']=0;
			$data['ip']=get_client_ip();;
			$data['dtime']=time();
			//录入模型处理
			model('badu')->in_data($data,'help');
			return $this->msg('您的求助已成功提交，我们会尽快与您联系，感谢您的信任！',1);	
		}		
    	$this->display('help_need.html');
    }
     //内容页
   	 public function donate(){
		$common['title']='我要捐款捐物';
		$common['cid']=2;
		$common['url']='help/donate.html';
        $this->assign('common', $common);
		if ($this->isPost() && is_array($_POST)){
			if (isset($_POST['data']) && count($_POST['data'])>0){
				$_POST=$_POST['data'];
			}
			$post=in($_POST);
			$title='';
			if ($post['title[1']=='on')	$title.='资金 ';
			if ($post['title[2']=='on')	$title.='物资物品 ';
			if ($post['title[3']=='on')	$title.='衣物 ';
			if ($post['title[0']=='on')	$title.='其他';
			if (empty($title) ) {
				$this->msg('请选择捐赠类项，可单选或多选！',0);
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
				$this->msg('捐赠说明未填写或填写不够详细！',0);
				return;
			}
			$data['type']=2;
			$data['sex']=intval($post['sex']);
			$data['open']=intval($post['open']);
			$data['name']=in($post['name']);
			$data['title']=$data['name'].'希望捐赠'.$title;
			$data['tel']=in($post['tel']);
			$data['content']=in($post['content']);
			$data['status']=0;
			$data['helps']=0;
			$data['ip']=get_client_ip();;
			$data['dtime']=time();
			//录入模型处理
			model('badu')->in_data($data,'help');
			return $this->msg('您的捐赠需求已成功提交，我们会尽快与您联系，感谢您的信任！',1);	
		}		
    	$this->display('help_donate.html');
    }

}

