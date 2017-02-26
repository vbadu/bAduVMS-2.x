<?php

class model_manageModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }
    // 模型列表
    public function model_list()
    {
        return $this->model->table('model')->order('mid asc')->select();
    }

    //模型信息
    public function info($id)
    {
        return $this->model->table('model')->where('mid='.$id)->find();
    }

    //查找模型信息
    public function search($model)
    {
        return $this->model->table('model')->where('model="'.$model.'"')->find();
    }

    //模型修改
    public function setting_save($data)
    {
        $condition['mid']=intval($data['mid']);
        return $this->model->table('model')->data($data)->where($condition)->update(); 
    }

    //删除模型表
    public function del_table($table)
    {
        $sql=" DROP TABLE `{$this->model->pre}{$table}` ";
        @$this->model->query($sql);
    }

    //删除模型记录
    public function del($mid)
    {
        $condition['mid']=$mid;
        $this->model->table('model')->where($condition)->delete();
    }

}

?>