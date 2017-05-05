<?php
//用户管理
class userMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('user',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
	}
	public function index() {
		$this->check_app_power('user',true);
        $this->list=model('user')->admin_list();
		$this->show();
	}

	//用户添加
	public function add() {
		$this->check_app_power('user',true);
		$this->user_group=model('user_group')->admin_list();
		$this->action_name='添加';
        $this->action='add';
        $this->show('user/info');
	}

	public function add_save() {
 		$this->check_app_power('user',true);
       if($_POST['password']<>$_POST['password2']){
            $this->msg('两次密码输入不同！',0);
            return;
        }
        if(model('user')->count($_POST['user'])){
            $this->msg('帐号不能重复！',0);
            return;
        }
        $_POST['password']=md5($_POST['password']);
        //录入模型处理
        model('user')->add($_POST);
        $this->msg('用户添加成功！',1);
	}

    //用户修改
    public function edit() {
		$this->check_app_power('user',true);
        $id=$_GET['id'];
        $this->alert_str($id,'int');
        $this->user_group=model('user_group')->admin_list();
        $this->info=model('user')->info($id);
        $this->info_group=model('user_group')->info($this->info['gid']);
        $user=model('user')->current_user();
        if($this->info_group['grade']<$user['grade']){
            $this->msg('越权操作！',0);
        }
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('user/info');
    }

    //修改资料
    public function edit_info() {
        $this->info=model('user')->current_user();
        $this->info_group=model('user_group')->info($this->info['gid']);
        $user=model('user')->current_user();
        if($this->info_group['grade']<$user['grade']){
            $this->msg('越权操作！',0);
        }
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('user/edit_info');
    }

    //用户修改
    public function edit_save() {
		$this->check_app_power('user',true);
        if (!empty($_POST['password']))
        {
            if (empty($_POST['password2']))
            {
               $this->msg('未填写确认密码！',0);
               return;
            }
            if($_POST['password']<>$_POST['password2']){
                $this->msg('两次密码输入不同！',0);
                return;
            }
            if(model('user')->count($_POST['user'],$_POST['id'])){
                $this->msg('帐号不能重复！',0);
                return;
            }
            $_POST['password']=md5($_POST['password']);
        }else{
            unset($_POST['password']);
        }
        $data['editor']=in($_POST['nicename']);
        $where['editor']=in($_POST['oldname']);
        $where['uid']=intval($_POST['id']);
		unset($_POST['oldname']);
        model('user')->edit($_POST);
		model('content')->data_up($data,$where);
        //录入模型处理
        model('user')->edit($_POST);
        $this->msg('用户修改成功! ',1);
    }

    //用户删除
    public function del() {
		$this->check_app_power('user',true);
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        $info=model('user')->info($id);
        if($info['keep']==1){
            $this->msg('内置管理员无法删除！',0);
        }
        $info_group=model('user_group')->info($info['gid']);
        $user=model('user')->current_user();
        if($info_group['grade']<$user['grade']){
            $this->msg('越权操作！',0);
        }
        //录入模型处理
        model('user')->del($id);
        $this->msg('用户删除成功！',1);
    }

}