<?php
//上传相关
class flinksModel extends commonModel {

	public function __construct()
  	{
        parent::__construct();
  	}

  	//获取附件列表
  	public function file_list($limit)
    {
        return $this->model->table('flinks')->limit($limit)->select();
    }

    public function count($where=null)
    {
        return $this->model->table('flinks')->where($where)->count();
    }

  	//添加
    public function add($data)
    {
        return $this->model->table('flinks')->data($data)->insert();
    }
	
    //编辑
    public function eidt($id,$data){
    	return $this->model->table('flinks')->data($data)->where('id='.$id)->update();
    }

    //删除
    public function del($id)
    {
    	//移除logo
    	$info = $this->get($id);
    	@unlink(__ROOTDIR__ . $info['logo']);
    	return $this->model->table('flinks')->where('id='.$id)->delete();
    }
    
    //获取一条数据
    public function get($id){
    	return $this->model->table('flinks')->where('id='.$id)->find();
    }
	
	//排序
	public function sequence($id,$sequence){
        $data['sequence']=$sequence;
        return $this->model->table('flinks')->data($data)->where('id='.$id)->update();
    }
    // 匹配数据_类别_选择
    public function select_type($data = 1){
             ($data == 1)?$selected = " selected='selected'":$selected = "";
             $html = "<option " . $selected . " value='1'>带图片连接</option>";
             ($data == 2)?$selected = " selected='selected'":$selected = "";
             $html .= "<option " . $selected . " value='2'>纯文字连接</option>";
             return $html;
     }
     // 匹配数据_类别
	public function text_type($data = 1){
         switch ($data){
             case 1:
                 $data = "带图片连接";
                 break;
             case 2:
                 $data = "纯文字连接";
                 break;
          }
          return $data;
    }
}