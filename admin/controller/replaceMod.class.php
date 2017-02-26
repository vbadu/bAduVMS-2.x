<?php
//内容替换管理
class replaceMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
		$this->model_url=$_GET['_module'];
        if(!model('user_group')->model_power($this->model_url,'visit')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
	}
	public function index() {
        $this->list=model('replace')->replace_list();
		$this->show();  
	}

	//添加
	public function add() {
        if(!model('user_group')->model_power($this->model_url,'add')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        $this->action_name='添加';
        $this->action='add';
        $this->show('replace/info'); 
	}

	public function add_save() {
        if(!model('user_group')->model_power($this->model_url,'add')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        //录入模型处理
        model('replace')->add($_POST);
        $this->msg('内容替换添加成功！',1);
	}

    //修改
    public function edit() {
        if(!model('user_group')->model_power($this->model_url,'edit')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        $id=intval($_GET['id']);
        $this->alert_str($id,'int');
        $this->info=model('replace')->info($id);
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('replace/info'); 
    }

    //修改数据
    public function edit_save() {
        if(!model('user_group')->model_power($this->model_url,'edit')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        $id=$_POST['id'];
        $this->alert_str($id,'int',true);
        //录入模型处理
        model('replace')->edit($_POST);
        $this->msg('内容替换修改成功! ',1);
    }

    //删除
    public function del() {
        if(!model('user_group')->model_power($this->model_url,'del')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        //录入模型处理
        model('replace')->del($id);
        $this->msg('内容替换删除成功！',1);
    }
	

	

}