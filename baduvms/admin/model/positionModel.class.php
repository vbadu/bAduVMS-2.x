<?php
class positionModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //推荐位列表
    public function position_list() {
        return $this->model->table('position')->where('status=1')->order('sequence desc')->select();
    }

    //添加推荐位关联
    public function add_content_save($list=null,$aid) {
        if(empty($list)){
            return false;
        }
        $list=explode(',', $list);
        foreach ($list as $value) {
            $data['pid']=$value;
            $data['aid']=$aid;
            $this->model->table('position_relation')->data($data)->insert();
        }
    }

    //编辑推荐位关联
    public function edit_content_save($list,$aid) {
        $this->del_content($aid);
        $this->add_content_save($list,$aid);
    }

    //删除内容推荐关联
    public function del_content($aid) {
        return $this->model->table('position_relation')->where('aid='.$aid)->delete();
    }

    //推荐位信息
    public function info($id) {
        return $this->model->table('position')->where('id='.$id)->find();
    }

    //获取指定推荐位列表
    public function content_list($id) {
        return $this->model->table('position')->where('status=1 and id in('.$id.')')->order('sequence desc')->select();
    }


    //获取推荐位数组
    public function relation_array($aid){
        $list=$this->model->table('position_relation')->where('aid='.$aid)->select();
        if(empty($list)){
            return false;
        }
        foreach ($list as $value) {
            $position[]=$value['pid'];
        }
        return $position;
    }

    //添加推荐位
    public function add($data)
    {

        $data['sequence']=intval($data['sequence']);
        return $this->model->table('position')->data($data)->insert();
    }
    //编辑推荐位
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        $data['sequence']=intval($data['sequence']);
        return $this->model->table('position')->data($data)->where($condition)->update(); 
    }
    //删除推荐位
    public function del($id)
    {
        return $this->model->table('position')->where('id='.intval($id))->delete(); 
    }

}

?>