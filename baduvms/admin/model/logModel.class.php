<?php
//日志记录
class logModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //获取登录列表
    public function log_list($limit)
    {
        $sql="
        SELECT A.*,B.user 
        FROM {$this->model->pre}admin_log A 
        LEFT JOIN {$this->model->pre}admin B ON A.uid = B.id
        WHERE A.uid<>1
        ORDER BY A.id DESC
        LIMIT {$limit} 
        ";
        return $this->model->query($sql); 
    }

    //获取登录总数
    public function count()
    {
        $sql="
        SELECT count(*) as num
        FROM {$this->model->pre}admin_log A 
        LEFT JOIN {$this->model->pre}admin B ON A.uid = B.id
        WHERE A.uid<>1
        ";
        $data=$this->model->query($sql);
        return $data[0]['num'];
    }

    
    //登录记录
    public function login_log($info) {
        $loginnum=$this->model->table('admin_log')->count();
        if($loginnum>299){
            $this->model->table('admin_log')->where(1)->order('id asc')->limit(1)->delete();
        }
        $log_data['uid']=$info['id'];
        $log_data['time']=time();
        $log_data['ip']=get_client_ip();
        $this->model->table('admin_log')->data($log_data)->insert();
    }
    
    //获取短信日志列表
    public function sms_list($limit)
    {
        $sql="
        SELECT A.*,B.user 
        FROM {$this->model->pre}admin_sms A 
        LEFT JOIN {$this->model->pre}admin B ON A.uid = B.id
        ORDER BY A.id DESC
        LIMIT {$limit} 
        ";
        return $this->model->query($sql); 
    }

    //获取登录总数
    public function sms_count()
    {
        $sql="
        SELECT count(*) as num
        FROM {$this->model->pre}admin_sms A 
        LEFT JOIN {$this->model->pre}admin B ON A.uid = B.id
        ";
        $data=$this->model->query($sql);
        return $data[0]['num'];
    }


}