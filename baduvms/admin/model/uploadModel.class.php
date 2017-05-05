<?php
//上传相关
class uploadModel extends commonModel {

	public function __construct()
  	{
        parent::__construct();
  	}

  	//获取附件列表
  	public function file_list($where,$limit)
    {
        return $this->model->table('upload')->where($where)->limit($limit)->order('id desc')->select();
    }

    public function count($where=null)
    {
        return $this->model->table('upload')->where($where)->count();
    }

    public function module_list(){
        $data=array(
            1=>array(
                'name'=>'栏目',
                'type'=>'category',
                ),
            2=>array(
                'name'=>'内容',
                'type'=>'content',
                ),
            3=>array(
                'name'=>'表单',
                'type'=>'form',
                ),
            4=>array(
                'name'=>'扩展',
                'type'=>'plus',
                ),

            );
    	return $data;
    }

  	//添加附件到数据库
    public function add($data)
    {
        return $this->model->table('upload')->data($data)->insert();
    }

    //关联附件表
    public function relation($table,$file_id,$id)
    {
    	if(empty($file_id)){
    		return;
    	}
    	$array=explode(',',$file_id);
    	if(empty($array)){
    		return;
    	}
    	//创建附件关联
    	$this->model->table('upload_'.$table)->where('id='.$id)->delete();
    	foreach ($array as $value) {
    		if(!empty($value)){
    			$data=array();
    			$data['id']=$id;
    			$data['file_id']=$value;
    			$this->model->table('upload_'.$table)->data($data)->insert();
    		}
    	}
    	//修改附件状态
    	$file_id=substr($file_id, 0,-1);
    	if(empty($file_id)){
    		return;
    	}
    	$data=array();
    	$data['type']=$table;
    	$this->model->table('upload')->data($data)->where('id in('.$file_id.')')->update();
        return true;
    }

    //获取关联ID
    public function get_relation($table,$id)
    {
    	$list=$this->model->table('upload_'.$table)->where('id='.$id)->select();
    	if(!empty($list)){
    		foreach ($list as $value) {
    			$file_id.=$value['file_id'].',';
    		}
    	}
    	return $file_id;
    }

    //删除附件+关联
    public function del_file($table,$id)
    {
    	$list=$this->model->table('upload_'.$table)->where('id='.$id)->select();
    	if(!empty($list)){
    		foreach ($list as $value) {
    			$info=$this->model->table('upload')->where('id='.$value['file_id'])->find();
                /*hook*/
                $this->plus_hook('upload_file','del_data',$info);
                /*hook end*/
    			$this->model->table('upload')->where('id='.$value['file_id'])->delete();
    			@unlink(__ROOTDIR__ .$info['file']);
                
    		}
    	}
    	$this->del_relation($table,$id);
    }

    //删除关联表
    public function del_relation($table,$id)
    {
    	$list=$this->model->table('upload_'.$table)->where('id='.$id)->delete();
    }

    //删除附件
    public function del($id)
    {
    	$module_list=$this->module_list();
    	if(!empty($module_list)){
    		foreach ($module_list as $value) {
    			$this->model->table('upload_'.$value['type'])->where('file_id='.$id)->delete();
    		}
    	}
    	$info=$this->model->table('upload')->where('id='.$id)->find();
    	@unlink(__ROOTDIR__ .$info['file']);
        /*hook*/
        $this->plus_hook('upload_file','del_data',$info);
        /*hook end*/
    	return $this->model->table('upload')->where('id='.$id)->delete();
    }


}