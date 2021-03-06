<?php
class contentMod extends commonMod
{
    public function __construct(){
        parent::__construct();
        if(!model('user_group')->menu_power('article',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
	}
    //公共列表信息
    public function common_list_where()
    {
        //排序
        $sequence=intval($_GET['sequence']);
        switch ($sequence) {
            case '1':
                $order='A.updatetime DESC';
                $where_url='1';
                break;
            case '2':
                $order='A.updatetime ASC';
                $where_url='2';
                break;
            case '3':
                $order='A.aid DESC';
                $where_url='3';
                break;
            case '4':
                $order='A.aid ASC';
                $where_url='4';
                break;
            case '5':
                $order='A.inputtime DESC';
                $where_url='5';
                break;
            case '6':
                $order='A.inputtime ASC';
                $where_url='6';
                break;
            case '7':
                $order='A.views DESC';
                $where_url='7';
                break;
            case '8':
                $order='A.views ASC';
                $where_url='8';
                break;
        }
        if(!empty($order)){
        $order=$order.',';
        $where_url='-sequence-'.$where_url;
        }

        //状态
        $status=intval($_GET['status']);
        switch ($status) {
            case '1':
                $where=' AND A.status=1';
                $where_url='1';
                break;
            case '2':
                $where=' AND A.status=0';
                $where_url='2';
                break;
            case '5':
                $where=' AND A.status=5';
                $where_url='5';
                break;
        }
        if(!empty($status)){
        $where_url='-status-'.$where_url;
        }

        //搜索
        $search=in(urldecode($_GET['search']));
        if(!is_utf8($search)){
            $search=auto_charset($search);
        }
        if(!empty($search)){
        $where=' AND A.title like "%' . $search . '%"';
        $where_url='-search-'.urlencode($search);
        }

        //推荐位
        $position=intval($_GET['position']);
        if(!empty($position)){
            $where=' AND D.pid='.$position;
            $where_url='-position-'.$position;
        }
        return array(
            'order'=>$order,
            'where'=>$where,
            'url'=>$where_url
            );

    }
    public function common_list()
    {
        $position_list=model('position')->position_list();
        $category_list=model('category')->category_list();
        $data['position_list']=$position_list;
        $data['category_list']=$category_list;
        return $data;
    }

    //公共调用信息
    public function common_info($id,$status=false)
    {
        if($status){
            $info=model('content')->info($id);
			$this->check_class_power($info['cid']);
            $info_data=model('content')->info_content($id);
            $position_array=model('position')->relation_array($id);
            $file_id=model('upload')->get_relation('content',$id);
            $cid=$info['cid'];
            $action_name='编辑';
            $action='edit';
        }else{
            $cid=$id;
            $action_name='添加';
            $action='add';
            $user=model('user')->current_user();
            $info['editor']=$user['nicename'];
        }
        $class_info = model('category')->info($cid);
        $category_list=model('category')->category_list();
        $position_list=model('position')->position_list();
        $tpl_list=model('category')->tpl_list();
        $data['info']=$info;
        $data['info_data']=$info_data;
        $data['position_array']=$position_array;
        $data['file_id']=$file_id;
        $data['action_name']=$action_name;
        $data['action']=$action;
        $data['class_info']=$class_info;
        $data['category_list']=$category_list;
        $data['position_list']=$position_list;
        $data['tpl_list']=$tpl_list;
        return $data;
    }
    //公共保存检测信息
    public function common_data_check($data){
        return in($data);
    }
    //获取关键词
    public function get_keyword(){
        
        $title=$_POST['title'];
        $content=$_POST['content'];
        $keyword=model('content')->get_keyword($title,$content);
        if(!empty($keyword)){
            $this->msg($keyword);
        }else{
            $this->msg('暂时无法获取到关键词！',0);
        }
    }
    // 内容列表
    public function all(){
        //获取公共信息条件
        $where=$this->common_list_where();
        $this->view()->assign($this->common_list());
		$class_info['name']='所有文章';
		$this->class_info=$class_info;
        //分页信息
        $listRows=30;
        $url = __URL__ . '/all/page-{page}'.$where['url'].'.html'; //分页基准网址
        $limit=$this->pagelimit($url,$listRows);
        //内容列表
        $this->list=model('content')->content_list(1,$limit,$where['where'],$where['order'],true);
        $count=model('content')->count(1,$where['where'],true);
        $this->page=$this->page($url, $count, $listRows);
        $this->show('content/index');
		die;
    }
    // 内容列表
    public function index(){
	   	$id=intval($_GET['id']);
        if (empty($id)) $this->all();
        $this->check_class_power($id);
        //获取公共信息条件
        $where=$this->common_list_where();
        $this->view()->assign($this->common_list());
        //栏目信息
        $this->class_info = model('category')->info($id);
		if ($this->class_info['mid']>1){
			if ($this->class_info['mid']==3){
				$this->jump($this->class_info);
			}
			if ($this->class_info['mid']==2){
				$this->onepage($this->class_info);
			}
			exit;
		}
		//分页信息
        $listRows=30;
        $url = __URL__ . '/index/id-' . $id . '-page-{page}'.$where['url'].'.html'; //分页基准网址
        $limit=$this->pagelimit($url,$listRows);
        if (empty($where['where'])) $where['where']=' and status=1';
        //内容列表
        $this->list=model('content')->content_list($id,$limit,$where['where'],$where['order']);
        $count=model('content')->count($id,$where['where']);
        $this->page=$this->page($url, $count, $listRows);
        $this->show();
    }
	public function onepage($array=array()){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$data=in($_POST);
			if (1>$cid) $this->msg('栏目分类数据不存在！',0);
			//检查
			if(empty($data['name'])){
				$this->msg('栏目名称未填写',0);
			}
			model('category')->edit_save($data);
			$this->msg('编辑成功！',1);
		}
        $id=intval($_GET['id']);
		$model=1;
        $this->info=$array;
        $this->show('content/onepage');
	}
	public function jump($array=array()){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST['content']=in($_POST['url']);
			unset($_POST['url']);
			$data=in($_POST);
			if (1>$cid) $this->msg('栏目分类数据不存在！',0);
			//检查
			if(empty($data['name'])){
				$this->msg('栏目名称未填写',0);
			}
			model('category')->edit_save($data);
			$this->msg('编辑成功！',1);
		}
        $id=intval($_GET['id']);
		$model=1;
        $this->info=$array;
        $this->show('content/jump');
	}
    //内容添加
    public function add(){
        $cid=intval($_GET['cid']);
        $this->view()->assign($this->common_info($cid));
        $this->show('content/info');
    }
    //内容保存
    public function add_save()
    {
        $this->check_class_power($_POST['cid']);
        /*hook*/
        $_POST=$this->plus_hook_replace('content','add_replace',$_POST);
        /*hook end*/
        $this->common_data_check($_POST);
		$user=model('user')->current_user();
        $_POST['uid']=$user['id'];
		if (empty($_POST['editor'])) $_POST['editor']=$user['nicename'];
    	//保存内容信息
    	$_POST['aid']=model('content')->add_save($_POST);
    	model('content')->add_content_save($_POST);
        /*hook*/
        $this->plus_hook('content','add',$_POST);
        /*hook end*/
    	$this->msg('内容添加成功！',1);
    }

    //内容编辑
    public function edit()
    {
        $id=intval($_GET['id']);
        $this->alert_str($id,'int');
        $this->view()->assign($this->common_info($id,true));
        $this->show('content/info');
    }

    //内容保存
    public function edit_save()
    {
        $this->check_class_power($_POST['cid']);
        /*hook*/
        $_POST=$this->plus_hook_replace('content','edit_replace',$_POST);
        /*hook end*/
        $this->common_data_check($_POST);
        $_POST['uid']=$user['id'];
		if (empty($_POST['editor'])) {
			$user=model('user')->current_user();
			$_POST['editor']=$user['nicename'];
		    $_POST['uid']=$user['id'];
		}
        //保存内容信息
        $status=model('content')->edit_save($_POST);
        model('content')->edit_content_save($_POST);
        /*hook*/
        $this->plus_hook('content','edit');
        /*hook end*/
        $this->msg('内容编辑成功！',1);
    }

    //内容删除
    public function del()
    {
        $id=intval($_POST['aid']);
        $this->alert_str($id,'int',true);
		$info=model('content')->info($id,'cid');
		$this->check_class_power($info['cid']);
        /*hook*/
        $this->plus_hook('content','del',$id);
        /*hook end*/
        $status=model('content')->del($id);
        model('content')->del_content($id);
        $this->msg('内容删除成功！',1);
    }

    //批量操作
    public function batch(){
        if(empty($_POST['status'])||empty($_POST['id'])){
            $this->msg('请先选择内容！',0);
        }
        $id_array=substr($_POST['id'],0,-1);
        $id_array=explode(',', $id_array);
		$class_id=intval($_POST['class_id']);
		$this->check_class_power($class_id);
        switch ($_POST['status']) {
            case '1':
                //审核
                foreach ($id_array as $value) {
                    model('content')->status($value,1);
                }
                break;
            case '2':
                //待审
                foreach ($id_array as $value) {
                    model('content')->status($value,0);
                }
                break;
            case '3':
                //删除
                foreach ($id_array as $value) {
                    /*hook*/
                    $this->plus_hook('content','del',$value);
                    /*hook end*/
                    model('content')->del($value);
                    model('content')->del_content($value);
                }
                break;
            case '4':
                //转移栏目
                $cid=intval($_POST['cid']);
                if(empty($cid)){
                    $this->msg('请先选择目标栏目！',0);
                }
				$this->check_class_power($cid);
                foreach ($id_array as $value) {
                    model('content')->edit_cid($value,intval($_POST['cid']));
                }
                break;
            case '5':
                //归档
                foreach ($id_array as $value) {
                    model('content')->status($value,5);
                }
                break;
        }
        $this->msg('操作执行完毕！',1);

    }
}