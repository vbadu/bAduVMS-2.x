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
                'pid'=>4,
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


/*
*/
        $user=model('user')->current_user();
        $menu_power=unserialize($user['menu_power']);
        
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

    //获取菜单
    public function admin_menu($pid=0) {
        $list=$this->model->table('admin_menu')->where('status=1 AND pid='.$pid)->order('id asc')->select();
        $data=array();
        if(!empty($list)){
            foreach ($list as $value) {
               if(model('user_group')->model_power($value['module'],'visit')){
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
    public function content_menu($model=1,$gid=null) {
        $data=$this->model->field('A.cid,A.pid,A.mid,A.type,A.name,B.admin_category,B.admin_content')
                ->table('category','A')
                ->add_table('model','B','A.mid = B.mid')
				->where('A.model = '.$model)
                ->order('A.sequence DESC,A.cid ASC')
                ->select();
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