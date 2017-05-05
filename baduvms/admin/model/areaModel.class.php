<?php
class areaModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_list($pid='',$show=1) {
		$where['show']=intval($show);
		if (intval($pid)>=0){
			$where['pid']=intval($pid);
		}
		$data= $this->model->table('area')->where($where)->order('sort asc,cid asc')->select();
        $cat = new Category(array('cid', 'pid', 'name', 'cname'));
        return $cat->getTree($data,intval($pid));
    }
    public function get_maxid()
    {
        $data=$this->model->table('area')->field('MAX(cid) as maxid')->find();
		return $data['maxid'];
    }
    public function get_count($id,$get='cid',$show=1)
    {
		$where['show']=intval($show);
		if (intval($id)>=0){
			if (in_array($get,array('cid','pid','sort','type','name'))) $get='cid';
			$where[$get]=intval($id);
		}
        return $this->model->table('area')->where($where)->count();
    }
    public function info($id)
    {
        return $this->model->table('area')->where('cid='.$id)->find();
    }
    public function data_save($data){
		$where['cid']=intval($data['cid']);
		unset($data['cid']);
        return $this->model->table('area')->data($data)->where($where)->update();

    }

    //增加
    public function data_in($data)
    {   
        $cid=$this->model->table('area')->data($data)->insert();
        return $cid;
    }
    //删除
    public function del($cid)
    {
        $status=$this->model->table('area')->where('cid='.$cid)->delete(); 
        return $status;
    }

}

?>