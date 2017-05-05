<?php
class userModel extends commonModel{
    protected $table = 'admin';

    public function __construct()
    {
        parent::__construct();
    }

    //当前用户信息
    public function current_user($cache=true,$del=false) {
        $uid=$_SESSION[$this->config['SPOT'].'_user'];
        if(empty($uid)){
            return ;
        }
        if($del){
            $this->cache->del('current_user_'.$uid);
            return ;
        }
        $user=$this->cache->get('current_user_'.$uid);
        if(empty($user)||$cache==false){
            $user=$this->model->table($this->table)->where('id='.$uid)->find();
            $user_group=$this->model->table('admin_group')->where('id='.$user['gid'])->find();
            $user['gname']=$user_group['name'];
            $user['model_power']=$user_group['model_power'];
            $user['class_power']=$user_group['class_power'];
            $user['menu_power']=$user_group['menu_power'];
            $user['form_power']=$user_group['form_power'];
            $user['grade']=$user_group['grade'];
            $user['keep']=$user_group['keep'];
            $this->cache->set('current_user_'.$uid, $user);
        }
        return $user;
    }

    //获取用户列表
    public function admin_list($kid=0){
        $user=$this->current_user();
        $where=' And A.id<>1';
        if(model('user_group')->model_power('user','current')&&$user['keep']<>1){
            $where.=' AND A.id='.$user['id'];
        }
        if (!empty($kid)){
        	$where.=' AND A.keep<>1';
        }
        $data=$this->model->field('A.*,B.name as gname')
                ->table('admin','A')
                ->add_table('admin_group','B','A.gid = B.id')
                ->where('B.grade>='.$user['grade'].$where)
                ->order('gid asc,id asc')
                ->select();
        return $data;
    }
    //获取用户列表 JSON
    public function user_list($word=null){
    	if (strlen($word)>0){
	    //	$where=' user like "%' . $word . '%"  or nicename like "%' . $word . '%"';
	    	$where='nicename like "%' . $word . '%"';
    	}
    	$data=$this->model->table($this->table)->field('id,nicename as value')->where($where)->select();
    	$data=json_encode($data);
        return $data;
    }
    //获取用户内容
    public function info($id)
    {
        return $this->model->table($this->table)->where('id='.$id)->find();
    }
    //获取用户内容
    public function info_auto($user,$keys=0){
   // 	$where=' user like "%' . $word . '%"  or nicename like "%' . $word . '%"';
    //  $where='user="'.$user.'" or nicename="'.$user.'"';
    	$where='nicename="'.$user.'"';
        $info=$this->model->table($this->table)->where($where)->find();
        if (empty($keys)){
           	return $info;
        }else{
        	return $info[$keys];
        }
    }
    //检测JSON
    public function count_auto($user){
        $where='nicename="'.$user.'"';
        return $this->model->table($this->table)->where($where)->count(); 
    }

    //检测重复用户
    public function count($user,$id=null)
    {
        if(!empty($id)){
            $where=' AND id<>'.$id;
        }
        return $this->model->table($this->table)->where('user="'.$user.'"'.$where)->count(); 
    }

    //添加用户内容
    public function add($data)
    {
        $_POST['regtime']=time();
        return $this->model->table($this->table)->data($data)->insert();
    }

    //编辑用户内容
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        $id=$this->model->table($this->table)->data($data)->where($condition)->update();
        return $id;
    }

    //删除用户内容
    public function del($id)
    {
        return $this->model->table($this->table)->where('id='.intval($id))->delete(); 
    }

}

?>