<?php
//替换数据处理
class replaceModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //获取内容替换列表
    public function replace_list()
    {
        return $this->model->table('replace')->order('id asc')->select();
    }

    //获取内容替换内容
    public function info($id)
    {
        return $this->model->table('replace')->where('id='.$id)->find();
    }

    //添加内容替换内容
    public function add($data)
    {
        $data['content']=html_in($data['content']);
        return $this->model->table('replace')->data($data)->insert();
    }

    //编辑内容替换内容
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        $data['content']=html_in($data['content']);
        return $this->model->table('replace')->data($data)->where($condition)->update(); 
    }

    //删除内容替换内容
    public function del($id)
    {
        return $this->model->table('replace')->where('id='.intval($id))->delete(); 
    }
	

}