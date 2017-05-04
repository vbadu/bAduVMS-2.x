<?php
//用户组管理
class user_groupMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('user',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('user_group',true);
	}
	public function index() {
		$this->list=model('user_group')->admin_list();
		$this->show();  
	}

	//用户组添加
	public function add() {
        $this->user=model('user')->current_user();
		$this->class_tree=model('menu')->content_menu();
		$this->class_power=explode(',',$this->info['class_power']);
        //获取模块权限
        $this->menu_list=model('menu')->menu_list();
        $this->form_list=model('form')->form_list();
        $this->action_name='添加';
        $this->action='add';
        $this->show('user_group/info');
	}

    public function data_save($data) {
		if (is_array($data['menu_power'])) $data['menu_power']=implode(',',$data['menu_power']);
		if (is_array($data['class_power'])) {
			$temp=array();
			foreach($data['class_power'] as $k => $v){
				$temp=array_merge($v,$temp);
			}
		}
		if (is_array($data['event_power'])) {
			$_temp=array();
			foreach($data['event_power'] as $k => $v){
				$_temp=array_merge($v,$_temp);
			}
			unset($data['event_power']);
			$data['class_power']=(is_array($_temp) && is_array($temp))?array_merge($_temp,$temp):$_temp;
			
		}
		if (is_array($data['class_power'])) $data['class_power']=implode(',',$data['class_power']);
		if (is_array($data['form_power'])) $data['form_power']=implode(',',$data['form_power']);
		if (is_array($data['model_power'])) $data['model_power']=implode(',',$data['model_power']);
 		//if (is_array($data['model_power'])) $data['model_power']=serialize($data['model_power']);
        return $data;
    }

	public function add_save() {
        $data=$this->data_save($_POST);
        //录入模型处理
        model('user_group')->add($data);
        $this->msg('用户组添加成功！',1);
	}
	
	public function totree($ar=array(), $id='cid', $pid='pid'){
		foreach($ar as $v) $t[$v[$id]] = $v;  
		foreach ($t as $k => $item){	
			if( $item[$pid]) {  
				$t[$item[$pid]]['child'][$item[$id]] =& $t[$k];
				unset($t[$k]);
			}
		}
		return $t;
	}

    //用户组修改
    public function edit() {
        $id=$_GET['id'];
        $this->alert_str($id,'int');
        //用户组信息
        $this->info=model('user_group')->info($id);
        $this->user=model('user')->current_user();
        if($this->info['grade']<$this->user['grade']){
            $this->msg('请勿越权操作！',0);
        }
        //获取模块权限
        $this->model_power=explode(',',$this->info['model_power']);
        $this->menu_power=explode(',',$this->info['menu_power']);
        $this->menu_list=model('menu')->menu_list();
		$this->class_power=explode(',',$this->info['class_power']);
		$content_cat=model('menu')->content_menu(1,$this->user['gid']);
		$this->content_cat=getTreeHtml(Array2Tree($content_cat,0),$this->class_power,'class_power');
		$event_cat=model('menu')->content_menu(2,$this->user['gid']);
		$this->event_cat=getTreeHtml(Array2Tree($event_cat,0),$this->class_power,'event_power');
		//$this->class_tree=model('menu')->content_menu();
 		$this->form_power=explode(',',$this->info['form_power']);
        $this->form_list=model('form')->form_list();
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('user_group/info');
    }

    //用户组修改
    public function edit_save() {
        $this->alert_str($_POST['id'],'int',true);
        $data=$this->data_save($_POST);
        //录入模型处理
        model('user_group')->edit($data);
        $this->msg('用户组修改成功! ',1);
    }

    //用户组删除
    public function del() {
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        $info=model('user_group')->info($id);
        if($info['keep']==1){
            $this->msg('内置管理组无法删除！',0);
        }
        $this->user=model('user')->current_user();
        if($info['grade']<$this->user['grade']){
            $this->msg('越权操作！',0);
        }
        //录入模型处理
        model('user_group')->del($id);
        $this->msg('用户组删除成功！',1);
    }
}