<?php
//推荐位管理
class positionMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('position',true);
	}
    //首页
	public function index() {
        //获取推荐位列表
        $this->list=model('position')->position_list();
		$this->show();  
	}

    //添加
    public function add() {
        $this->action_name='添加';
        $this->action='add';
        $this->show('position/info'); 
    }

    public function add_save() {
        //录入模型处理
        model('position')->add($_POST);
        $this->msg('推荐位添加成功！',1);
    }

    //修改
    public function edit() {
        $id=$_GET['id'];
        $this->alert_str($id,'int');
        $this->info=model('position')->info($id);
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('position/info'); 
    }

    public function edit_save() {
        $id=$_POST['id'];
        $this->alert_str($id,'int',true);
        //录入模型处理
        model('position')->edit($_POST);
        $this->msg('推荐位修改成功! ',1);
    }

    //删除
    public function del() {
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true); 
        //录入模型处理
        model('position')->del($id);
        $this->msg('推荐位删除成功！',1);
    }

}