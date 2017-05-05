<?php
//自定义变量管理
class fragmentMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('fragment',true);
	}
	public function index() {
		$this->list=model('fragment')->fragment_list();
		$this->show();  
	}

	//变量添加
	public function add() {
        $this->action_name='添加';
        $this->action='add';
		$this->show('fragment/info'); 
	}

	public function add_save() {
        $id=model('fragment')->add($_POST);
        model('upload')->relation('plus',$_POST['file_id'],$id);
        $this->msg('自定义变量添加成功！',1);
	}

    //变量修改
    public function edit() {
        $id=intval($_GET['id']);
        $this->alert_str($id,'int');
        $this->info=model('fragment')->info($id);
        $this->file_id=model('upload')->get_relation('plus',$id);
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('fragment/info'); 
    }

    //变量修改
    public function edit_save() {
        $id=$_POST['id'];
        $this->alert_str($id,'int',true);
        model('fragment')->edit($_POST);
        model('upload')->relation('plus',$_POST['file_id'],$_POST['id']);
        $this->msg('变量修改成功! ',1);
    }

    //变量删除
    public function del() {
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true); 
        //录入模型处理
        model('fragment')->del($id);
        model('upload')->del_file('plus',$id);
        $this->msg('变量删除成功！',1);
    }
}