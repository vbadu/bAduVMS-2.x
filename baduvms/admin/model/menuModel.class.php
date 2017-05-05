<?php
class menuModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }
    //获取主菜单
    public function main_menu(){
        $menu_list=array(
            0=>array(
                'pid'=>1,
                'name'=>'管理首页',
                'url'=>__APP__.'/menu/index',
                ),
            1=>array(
                'pid'=>3,
                'name'=>'文章管理',
                'url'=>__APP__.'/menu/article',
                ),
            2=>array(
                'pid'=>5,
                'name'=>'活动管理',
                'url'=>__APP__.'/menu/event',
                ),
            4=>array(
                'pid'=>4,
                'name'=>'用户管理',
                'url'=>__APP__.'/menu/user',
                ),
             5=>array(
                'pid'=>2,
                'name'=>'系统管理',
                'url'=>__APP__.'/menu/system',
                ),
            );
        $user=model('user')->current_user();
        $menu_power=explode(',',$user['menu_power']);
        foreach ($menu_list as $value) {
            if(in_array($value['pid'], (array)$menu_power)){
                $list[]=$value;
            }
        }
        if($user['keep']<>1){
            return $list;
        }else{
            return $menu_list;
        }
    }
	public function check_menu($pid=0){
        $user=model('user')->current_user();
        if($user['keep']==1) return true;
        $menu_power=explode(',',$user['menu_power']);
		if(in_array(intval($pid), (array)$menu_power)){
			return true;
		}
		return false;
	}
    //获取菜单
    public function admin_menu($pid=0) {
		if (!$this->check_menu($pid)) exit('对不起，您没有编号'.$pid.'模块的操作权限！');
        $list=$this->model->table('admin_menu')->where('status=1 AND pid='.$pid)->order('id asc')->select();
        $data=array();
        if(!empty($list)){
            foreach ($list as $value) {
               if(model('user_group')->model_power($value['id'])){
                    $data[]=$value;
               }
            }
        }
        return $data;
    }

    //获取菜单项目
    public function menu_list($pid=0) {
        return $this->model->table('admin_menu')->where('status=1 and pid='.$pid)->order('id asc')->select();
    }


    //格式化内容菜单
    public function content_menu($model=1,$gid=null,$pid=null) {
		if (strlen($pid)>0 && $pid>=0) $model=$model.' And pid='.intval($pid);
        $data=$this->model->field('cid,pid,mid,type,name')
                ->table('category','A')
				->where('model = '.$model)
                ->order('sequence DESC,cid ASC')
                ->select();
		$keep=true;	
		if(!empty($gid)){
            $user=model('user')->current_user();
            $class_power=explode(',', $user['class_power']);
            if($user['keep']<>1){
                $keep=false;
            }
        }
        return $this->gentree($data,$class_power,$keep);
    }
    //格式化内容菜单
    public function get_category($type=1,$gid=null) {
        $data=$this->model->field('cid,pid,type,name')->table('category')->where('`type` in ('.$type.')')->order('sequence DESC,cid ASC')->select();
        if(!empty($gid)){
            $user=model('user')->current_user();
            $class_power=explode(',', $user['class_power']);
            if($user['keep']<>1){
                $keep=false;
            }
        }
        return $this->gentree($data,$class_power,$keep);
    }

    //输出数据
    public function gentree($data,$class_power=array(),$keep=true){
        if(!empty($data)){
            foreach ($data as $key=>$value) {
                $tree[$key]=$value;
                if(in_array($value['cid'],(array)$class_power)||$keep==false){
					$tree[$key]['pw']=0;
                }else{
                    $tree[$key]['pw']=1;
                }
            }
        }
        return $tree;

    }

}

?>