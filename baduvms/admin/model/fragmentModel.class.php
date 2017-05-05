<?php
//自定义变量数据处理
class fragmentModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //获取自定义变量列表
    public function fragment_list()
    {
        return $this->model->table('fragment')->order('id asc')->select();
    }

    //获取自定义变量内容
    public function info($id)
    {
        return $this->model->table('fragment')->where('id='.$id)->find();
    }

    //添加自定义变量内容
    public function add($data)
    {
        $data['content']=html_in($data['content']);
        return $this->model->table('fragment')->data($data)->insert();
    }

    //编辑自定义变量内容
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        $data['content']=html_in($data['content']);
        return $this->model->table('fragment')->data($data)->where($condition)->update(); 
    }

    //删除自定义变量内容
    public function del($id)
    {
        return $this->model->table('fragment')->where('id='.intval($id))->delete(); 
    }
	

}