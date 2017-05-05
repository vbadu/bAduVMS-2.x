<?php
class eventModel extends commonModel{
    protected $table = 'event';

    public function __construct()
    {
        parent::__construct();
    }

    //列表
    public function get_list($where=NULL,$table=NULL,$limit=NULL,$order='id DESC'){  
		$table=$this->table. $table;
        $data=$this->model->table($table)->where($where)->limit($limit)->order($order)->select();
        return $data;
    }
    //统计
	public function get_count($where=NULL,$table=NULL) {
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->count();
	}
    //统计
	public function get_sum($id=1,$table=NULL) {
		$data=$this->model->query("SELECT sum(vtime) as vtimes FROM {$this->model->pre}{$this->table}{$table} WHERE uid={$id}");
		return $data[0]['vtimes'];
	}
    //获取内容
    public function info($where=NULL,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->find();
    }
    //更新项目
    public function edit($data,$condition=null,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->data($data)->where($condition)->update();
    }
    //添加内容
    public function add($data,$table=NULL){
		$table=$this->table. $table;
        $data['dtime']=time();
        return $this->model->table($table)->data($data)->insert();
    }
    //删除内容
    public function del($where=0,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->delete(); 
    }
    //检测重复
    public function count($title,$id=null)
    {
        if(!empty($id)){
            $where=' AND id<>'.$id;
        }
        return $this->model->table($this->table)->where('title="'.$title.'"'.$where)->count(); 
    }
    //获取设置
    public function get_set_radio($name='weekday',$id=1,$data=1){
        $list=$this->get_list('`key`='.$id,'_set','','id asc');
        if (!is_array($list)) return '<font color=red>请先设置操作选项</font>';
        $html='';
            foreach ($list as $vo) {
            	($data==$vo['id'])?$checked=" checked='checked' ":$checked=" ";
                $html .= "<input name='".$name."'".$checked."value='".$vo['id']."' type='radio'> ".$vo['name']."  ";
    		}
         return $html;
    }
    //匹配数据
    public function get_set_select($name='type',$id=1,$data=1){
    	$html='<select name="'.$name.'" reg="." id="cid" msg="请选择">';
		if (is_null($id) || (strlen($id)==0)) $id=0;
		if ($id==0) $selected = " selected='selected'";
        $html .= "<option " . $selected . " value=''>== 请选择 ==</option>";
        $list=$this->get_list('`key`='.$id,'_set','','id asc');
		if (count($list)>1) {
            foreach ($list as $vo) {
            	($data==$vo['id'])?$selected=" selected='selected'":$selected="";
                $html .= "<option ".$selected." value='".$vo['id']."'>".$vo['name']."</option>";
    		}
		}
    	$html.='</select>';
         return $html;
    }
    //收费类别
    public function get_rmb($data=1) {
    	$html='<select name="about[rmb]" reg="." id="cid" msg="请选择">';
         ($data==1)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='1'>免费</option>";
         ($data==2)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='2'>AA制</option>";
         ($data==3)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='3'>自助</option>";
         ($data==4)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='4'>收费</option>";
         ($data==5)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='5'>其他</option>";
      	$html.='</select>';
       return $html;
    }
















}?>