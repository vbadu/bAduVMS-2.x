<?php
class navMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('nav',true);
    }
    // 分类首页
    public function index()
    {
        $type=in($_GET['type']);
        $type=(empty($type))?1:$type;
        $this->list=model('nav')->nav_list($type);
        //权限部分
        if(model('user_group')->model_power('nav','edit')){
            $this->edit_power=true;
        }
        if(model('user_group')->model_power('nav','del')){
            $this->del_power=true;
        }
        $this->type=$type;
        $this->show();
    }

    //排序
    public function sequence(){
        $cid=intval($_POST['cid']);
        $sort=intval($_POST['sequence']);
        model('nav')->sequence($cid,$sort);
        $this->msg('排序成功！');
    }


    public function common_info($id=null)
    {
        $type=in($_GET['type']);
        $type=(empty($type))?1:$type;
        if(!empty($id)){
            $info=model('nav')->info($id);
            $action_name='编辑';
            $action='edit';
        }else{
            $action_name='添加';
            $action='add';
        }
        $nav_list=model('nav')->nav_list($type);
        if($_GET['type']<>'content'){
            $return_list=__APP__.'/nav';
        }else{
            $return_list=__APP__.'/content';
        }
        $data['type']=$type;
        $data['info']=$info;
        $data['file_id']=$file_id;
        $data['action_name']=$action_name;
        $data['action']=$action;
        $data['nav_list']=$nav_list;
        $data['return_list']=$return_list;
        return $data;
    }

    public function common_edit_check($data)
    {
        if(empty($data['title'])){
            $this->msg('导航菜单名称未填写',0);
        }

        // 分类检测
        if ($data['pid'] == $data['cid']){
            $this->msg('不可以将当前导航菜单设置为上一级导航菜单！',0);
            return;
        }
        $type=in($data['type']);
        $cat = model('nav')->nav_list($type,$data['cid']);
        if (!empty($cat)) {
            foreach ($cat as $vo) {
                if ($data['pid'] == $vo['cid']) {
                    $this->msg('不可以将上一级导航菜单移动到子导航菜单！',0);
                    return;
                }
            }
        }
    }

    public function common_del_check($data)
    {
        if(model('nav')->list_count($data['cid'])){
            $this->msg('请先删除子导航菜单！',0);
        }
    }

   //导航菜单添加
    public function add()
    {
        $this->view()->assign(module('nav')->common_info());
        $this->show('nav/info');
    }

    //导航菜单保存
    public function add_save()
    {
		unset($_POST['cid']);
		$post=in($_POST);
		$where['title']=in($post['title']);
		$where['url']=in($post['url']);
		$where['type']=intval($post['type']);
		if (6>strlen($where['title'])) $this->msg('导航菜单名不能少于2个汉字，添加失败！');
		if (3>strlen($where['url'])) $this->msg('导航连接不能少于3个字符，添加失败！');
		$count=model('nav')->get_count($where);
		if ($count>0) $this->msg('已存在相同导航菜单，添加失败！');
		$cid=model('nav')->add_save($post);
        $this->msg('导航菜单添加成功！');
    }

    //导航菜单编辑
    public function edit()
    {
        $id=intval($_GET['id']);
        $this->alert_str($id,'int');
        $this->view()->assign(module('nav')->common_info($id));
        $this->show('nav/info');
    }

    //导航菜单保存
    public function edit_save()
    {
        
        $this->alert_str($_POST['cid'],'int',true);
        //检查
        module('nav')->common_edit_check($_POST);
        model('nav')->edit_save($_POST);
        /*hook*/
        $this->plus_hook('nav','edit');
        /*hook end*/
        $this->msg('导航菜单编辑成功！',1);
    }

    //导航菜单删除
    public function del()
    {
        $this->alert_str($_POST['cid'],'int',true);
        module('nav')->common_del_check($_POST);
        /*hook*/
        $this->plus_hook('nav','del');
        /*hook end*/
        model('nav')->del($_POST['cid']);
        $list=model('content')->get_list_id($_POST['cid']);
        if($list){
            foreach ($list as $value) {
                model('content')->del($value['id']);
                model('content')->del_content($value['id']);
            }
        }
        $this->msg('导航菜单删除成功！',1);
    }

}

?>