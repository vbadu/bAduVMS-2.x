<?php
//用户组数据处理
class user_groupModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //获取用户组列表
    public function admin_list()
    {
        $user=model('user')->current_user();
        return $this->model->table('admin_group')->where('grade>='.$user['grade'])->order('id asc')->select();
    }

    //获取用户组内容
    public function info($id)
    {
        return $this->model->table('admin_group')->where('id='.$id)->find();
    }

    //添加用户组内容
    public function add($data)
    {
        return $this->model->table('admin_group')->data($data)->insert();
    }

    //编辑用户组内容
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        $this->model->table('admin_group')->data($data)->where($condition)->update();
        model('user')->current_user($cache=true,$del=true);
        return true;
    }

    //删除用户组内容
    public function del($id)
    {
        $this->model->table('admin')->where('gid='.intval($id))->delete(); 
        return $this->model->table('admin_group')->where('id='.intval($id))->delete(); 
    }

    //获取模块ID
    public function get_appid($id,$is_mod=false,$nopid=false)
    {
		if ($nopid){
			$where='pid !=0 AND ';	
		}
        if ($is_mod){
        	$mod=$this->model->table('admin_menu')->where($where.'module="'.in($id).'"')->find();
			if (is_array($mod)) return $mod;
        }else{
			$mod=$this->model->table('admin_menu')->where($where.'id="'.intval($id).'"')->find();
			if (is_array($mod)) return $mod;
		}
		return false;
    }
    //判断主菜单权限
    public function menu_power($id,$is_mod=false,$cache=true)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
		$info=$this->cache->get('menu_power_'.$user['uid'].$id);
		if(empty($info)||$cache==false){
        	$info=$this->get_appid($id,$is_mod);
			$this->cache->set('menu_power_'.$user['uid'].$id, $info);
		}
        if(empty($info)){
            return false;
        }
        if (isset($user['menu_power']) && strlen($user['menu_power'])>0){
        	$menu_power=explode(',',$user['menu_power']);
			if(in_array($info['id'],(array)$menu_power)){
				return true;
			}
		}
        return false;
    }

    //判断是否有模块权限
    public function model_power($module,$is_mod=false,$cache=true)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
        $info=$this->cache->get('model_power_'.$user['uid'].$module);
        if(empty($info)||$cache==false){
            $info=$this->get_appid($module,$is_mod,true);
            $this->cache->set('model_power_'.$user['uid'].$module, $info);
        }
        if(empty($info)){
            return false;
        }
        if (isset($user['model_power']) && strlen($user['model_power'])>0){
        	$model_power=explode(',',$user['model_power']);
			if(in_array($info['id'],(array)$model_power)){
				return true;
			}
		}
        return false;
    }

    //判断栏目权限
    public function form_power($cid,$cache=true)
    {
        if (empty($cid)) return false;
        $user=model('user')->current_user();
		if($user['keep']==1){
            return true;
        }
        if (isset($user['form_power']) && strlen($user['form_power'])>0){
        	$form_power=explode(',',$user['form_power']);
			if(in_array($cid,(array)$form_power)){
				return true;
			}
		}
        return false;
    }
    //判断栏目权限
    public function class_power($cid,$cache=true)
    {
        if (empty($cid)) return false;
        $user=model('user')->current_user();
		if($user['keep']==1){
            return true;
        }
        if (isset($user['class_power']) && strlen($user['class_power'])>0){
        	$class_power=explode(',',$user['class_power']);
			//dump($class_power,1);
			if(in_array($cid,(array)$class_power)){
				return true;
			}
		}
        return false;
    }











/*

    //获取子权限表
    public function admin_power($pid)
    {
        return $this->model->table('admin_power')->where('pid='.$pid)->order('sequence asc')->select();
    }


    //判断是否有模块权限
    public function model_power($module,$action,$cache=true)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
        $info=$this->cache->get('model_power_'.$user['uid'].$module);
        if(empty($info)||$cache==false){
            $info=$this->model->table('admin_menu')->where('module="'.$module.'"')->find();
            $this->cache->set('model_power_'.$user['uid'].$module, $info);
        }
        if(empty($info)){
            return false;
        }
        $model_power=unserialize($user['model_power']);
        if(empty($model_power)){
            return false;
        }
        if(in_array($action,(array)$model_power[$info['id']])||in_array($info['id'],(array)$model_power[$info['id']])){
            return true;
        }
        return false;
    }

    //判断主菜单权限
    public function menu_power($id,$isint=true)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
        if (!$isint){
        	$mod=$this->model->table('admin_menu')->where('module="'.in($id).'"')->find();
        	$id=$mod['id'];
        }
        
        $menu_power=unserialize($user['menu_power']);
        if(in_array($id,(array)$menu_power)){
            return true;
        }
        return false;
    }

    //判断是否有表单权限
    public function form_power($table,$action)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
        $info=$this->model->table('form')->where('`table`="'.$table.'"')->find();
        $form_power=unserialize($user['form_power']);
        if(empty($form_power)){
            return false;
        }
        if(in_array($action,(array)$form_power[$info['id']])||in_array($info['id'],(array)$form_power[$info['id']])){
            return true;
        }
        return false;
    }

    //判断内容权限
    public function class_power($cid)
    {
        $user=model('user')->current_user();
        if($user['keep']==1){
            return true;
        }
        $class_power=explode(',', $user['class_power']);
        if(in_array($cid, (array)$class_power)){
            return true;
        }
        return false;

    }

*/
	

}