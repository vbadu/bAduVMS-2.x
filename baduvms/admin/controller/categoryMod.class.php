<?php
class categoryMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
	}
    // 分类首页
    public function index()
    {
        if(!model('user_group')->model_power('category/index',true,0)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->model=1;
        $this->list=model('category')->category_list($this->model,0);
        $this->show();
    }
    // 分类首页
    public function def()
    {
        if(!model('user_group')->model_power('category/def',true,0)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->model=2;
        $this->list=model('category')->category_list($this->model,0);
        $this->show('category/index');
    }
    //排序
    public function sequence(){
        $cid=intval($_POST['cid']);
        $sequence=intval($_POST['sequence']);
        model('category')->sequence($cid,$sequence);
        $this->msg('排序成功！');
    }
    public function common_info($id=null,$model=1)
    {
        if(!empty($id)){
            $info=model('category')->info($id);
            $file_id=model('upload')->get_relation('category',$id);
            $action_name='编辑';
            $action='edit';
			$model=$info['model'];
			switch ($info['mid']) {
				case 1:
					$edit_tpl = 'category/content';
					break;
				case 2:
					$edit_tpl = 'category/onepage';
					break;
				case 3:
					$edit_tpl =	'category/jump';
					break;
			}
			if (!is_array($info)) $this->msg('抱歉，您访问的栏目不存在或已下架！',0);			
        }else{
            $action_name='添加';
            $action='add';
			$model=($model==1)?1:2;
        }
        $category_list=model('category')->category_list($model);
        $tpl_list=model('category')->tpl_list();
        if($model==1){
            $return_list=__URL__.'/index';
        }else{
            $return_list=__URL__.'/def';
        }
        $data['edit_tpl']=$edit_tpl;
        $data['info']=$info;
        $data['file_id']=$file_id;
        $data['action_name']=$action_name;
        $data['action']=$action;
        $data['category_list']=$category_list;
        $data['model_list']=$model_list;
        $data['tpl_list']=$tpl_list;
        $data['return_list']=$return_list;
        return $data;
    }
    public function common_add_check($data)
    {
        if(empty($data['class_tpl'])&&empty($data['content_tpl'])){
            $this->msg('栏目或内容模板未选择！',0);
        }

    }
    public function common_edit_check($data)
    {
		if(empty($data['name'])){
            $this->msg('栏目名称未填写',0);
        }
		if(strlen($data['url'])==0 && empty($data['class_tpl'])&& empty($data['content_tpl'])){
            $this->msg('栏目或内容模板未选择！',0);
        }else{
			$_POST['content']=in($data['url']);
			unset($_POST['url']);
		}
        
        // 分类检测
        if ($data['pid'] == $data['cid']){
            $this->msg('不可以将当前栏目设置为上一级栏目！',0);
            return;
        }
        $cat = model('category')->category_list($data['cid']);
        if (!empty($cat)) {
            foreach ($cat as $vo) {
                if ($data['pid'] == $vo['cid']) {
                    $this->msg('不可以将上一级栏目移动到子栏目！',0);
                    return;
                }
            }
        }
    }
    public function common_del_check($data)
    {
        if(model('category')->list_count($data['cid'])){
            $this->msg('请先删除子栏目！',0);
        }
    }
	public function edit(){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST=in($_POST);
			if (1>$cid) $this->msg('栏目分类数据不存在！',0);
			//检查
			$this->common_edit_check($_POST);
			/*hook*/
			$_POST=$this->plus_hook_replace('category','edit_replace',$_POST);
			/*hook end*/
			model('category')->edit_save($_POST);
			/*hook*/
			$this->plus_hook('category','edit');
			/*hook end*/
			$this->msg('编辑成功！',1);
		}
        $id=intval($_GET['id']);
		$info=$this->common_info($id);
        $this->view()->assign($info);
        $this->show($info['edit_tpl']);
	}
    public function del()
    {
        $this->alert_str($_POST['cid'],'int',true);
		//$this->check_class_power($_POST['cid']);
        $this->common_del_check($_POST);
		$model=model('category')->getModelId($_POST['cid']);
        /*hook*/
        $this->plus_hook('category','del');
        /*hook end*/
        model('category')->del($_POST['cid']);
		if ($model==1){
			$list=model('content')->get_list_id($_POST['cid']);
			if($list){
				foreach ($list as $value) {
					model('content')->del($value['aid']);
					model('content')->del_content($value['aid']);
				}
			}
		}
        $this->msg('删除成功！',1);
    }
	public function event(){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST=in($_POST);
			if ($cid>0){
				$this->edit();
			}else{
				$this->common_add_check($_POST);
				/*hook*/
				$_POST=$this->plus_hook_replace('category','add_replace',$_POST);
				/*hook end*/
				$cid=model('category')->add_save($_POST);
				/*hook*/
				$this->plus_hook('category','add',$cid);
      			$this->msg('分类添加成功！');
			}
		}
        $id=intval($_GET['id']);
		$this->model=2;
        $this->view()->assign($this->common_info($id,$this->model));
        $this->show('category/content');
	}
	public function content(){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST=in($_POST);
			if ($cid>0){
				$this->edit();
			}else{
				$this->common_add_check($_POST);
				/*hook*/
				$_POST=$this->plus_hook_replace('category','add_replace',$_POST);
				/*hook end*/
				$cid=model('category')->add_save($_POST);
				/*hook*/
				$this->plus_hook('category','add',$cid);
      			$this->msg('栏目添加成功！');
			}
		}
        $id=intval($_GET['id']);
		$this->model=1;
        $this->view()->assign($this->common_info($id,$this->model));
        $this->show();
	}
	public function onepage(){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST=in($_POST);
			$_POST['type']=0;
			$_POST['mid']=2;
			if ($cid>0){
				$this->edit();
			}else{
				$this->common_add_check($_POST);
				/*hook*/
				$_POST=$this->plus_hook_replace('category','add_replace',$_POST);
				/*hook end*/
				$cid=model('category')->add_save($_POST);
				/*hook*/
				$this->plus_hook('category','add',$cid);
      			$this->msg('单页添加成功！');
			}
		}
        $id=intval($_GET['id']);
		$model=1;
        $this->view()->assign($this->common_info($id,$model));
        $this->show();
	}
	public function jump(){
		if ($this->isPost()){
			$cid=intval($_POST['cid']);
			$_POST=in($_POST);
			$_POST['type']=0;
			$_POST['mid']=3;
			if ($cid>0){
				$this->edit();
			}else{
				/*hook*/
				$_POST=$this->plus_hook_replace('category','add_replace',$_POST);
				/*hook end*/
				$cid=model('category')->add_save($_POST);
				/*hook*/
				$this->plus_hook('category','add',$cid);
      			$this->msg('跳转添加成功！');
			}
		}
        $id=intval($_GET['id']);
		$model=1;
        $this->view()->assign($this->common_info($id,$model));
        $this->show();
	}




}