<?php
class helperModel extends commonModel{
    protected $table = 'helper';

    public function __construct()
    {
        parent::__construct();
    }
    //获取内容
    public function get_chat_list($group,$limit='0,100'){
        if (empty($group)){
        	return;
        }
		$where['group']=$group;
		
        $data=$this->model->table($this->table.'_chat')->where($where)->limit($limit)->order('id desc')->select();
        return $data;
    }
    //获取内容
    public function get_msg_list($id,$limit=NULL){
        if (!empty($id)){
        	$where['rid']=$id;
        }
        $data=$this->model->table($this->table.'_msg')->where($where)->limit($limit)->order('id desc')->select();
        return $data;
    }
    //获取用户内容
    public function info($id,$table=NULL)
    {
        $table=$this->table.$table;
		return $this->model->table($table)->where('id='.$id)->find();
    }

    //添加用户内容
    public function add($data,$table=NULL)
    {
        $table=$this->table.$table;
		return $this->model->table($table)->data($data)->insert();
    }
    //删除用户内容
    public function del($id,$table=NULL)
    {
        $table=$this->table.$table;
        return $this->model->table($table)->where('id='.intval($id))->delete(); 
    }
    //提示
    public function send($rid,$msg,$type=0) {
		if (empty($rid) || is_array($msg)==false) return 0;
		$data['type']=$type;
		$data['sid']=$_SESSION[$this->config['SPOT'].'_member'];
		$data['rid']=$rid;
		$data['title']=$msg['title'];
		$data['msg']=$msg['msg'];
		$data['dtime']=time();
		$this->add($data,'_msg');
		return $rid;
    }

	    

}?>