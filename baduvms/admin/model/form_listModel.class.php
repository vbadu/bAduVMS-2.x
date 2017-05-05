<?php
//表单数据处理
class form_listModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //字段列表
    public function field_list($id)
    {
        return $this->model->table('form_field')->where('fid='.$id.' AND admin_display=1')->order('sequence asc,id asc')->select();
    }
    //所有字段列表
    public function list_lod($id)
    {
        return $this->model->table('form_field')->where('fid='.$id)->select();
    }
	public function get_info($id=0,$table=''){  
        if(empty($id) || empty($table)){
        	return false;
        }
		$where='WHERE  A.id='.intval($id);
        $sql="
            SELECT 
			A.id,A.uid,A.uname,A.vcard,A.tel,A.vtime,A.vzt,A.status,A.ip,A.dtime,A.table,
			B.gid,B.gname,B.oid,B.status as isvip,B.realname,B.sex,B.btime,B.vtime as vtimes,B.loginnum,C.*,D.bmgs
            FROM {$this->model->pre}event_team A 
            LEFT JOIN {$this->model->pre}member B ON A.uid = B.id
            LEFT JOIN {$this->model->pre}form_data_{$table} C ON A.fid = C.fid
			LEFT JOIN {$this->model->pre}event D ON A.eid = D.id
            {$where} ORDER BY {$order}A.dtime DESC,A.id DESC LIMIT 1";		
			$data=$this->model->query($sql);
        return $data[0];
    }

	public function get_list($eid=0,$table='',$limit=NULL,$order='A.id DESC'){  
        if(!empty($eid) && $eid>0){
            $where='WHERE  A.eid='.intval($eid);
        }
        if(!empty($limit)){
            $limit='limit '.$limit;
        }
        if(!empty($order)){
            $order=$order.',';
        }
        if(!empty($table)){
            $sql="SELECT A.id,A.uid,A.uname,A.vcard,A.tel,A.vtime,A.vzt,A.status,A.ip,A.dtime,B.* FROM {$this->model->pre}event_team A LEFT JOIN {$this->model->pre}form_data_{$table} B ON A.eid = B.eid AND A.fid=B.fid {$where} ORDER BY {$order}A.dtime desc {$limit}";
        	$data=$this->model->query($sql);
        	return $data; 
		}
		return $this->field_list($id);
    }
    public function get_count($eid=0,$table=''){  
        if(!empty($eid) && $eid>0){
            $where.=' AND A.eid='.intval($eid);
        }
        if(!empty($table)){
            $sql="SELECT count(A.id) as num FROM {$this->model->pre}event_team A LEFT JOIN {$this->model->pre}form_data_{$table} B ON A.eid = B.eid AND A.fid=B.fid {$where}";
        	$data=$this->model->query($sql);
        	return $data[0]['num']; 
		}
        return $this->count($id);
    }

    //表单内容列表
    public function form_list($id,$limit,$order)
    {
        //获取模型表
        $model=model('form')->info($id);
        return $this->model->table('form_data_'.$model['table'])->order($order.',fid desc')->limit($limit)->select();
    }

    //表单内容统计
    public function count($id)
    {
        //获取模型表
        $model=model('form')->info($id);
        return $this->model->table('form_data_'.$model['table'])->count();
    }

    //添加内容
    public function add($data){
        if(empty($data)){
            return;
        }
        //读取模型表
        $model=model('form')->info($data['fid']);
        //录入表数据
        $id=$this->model->table('form_data_'.$model['table'])->data($data)->insert();
        return $id;
    }

    //编辑内容
    public function edit($data){
        if(empty($data)){
            return;
        }
        //读取模型表
        $model=model('form')->info($data['fid']);
        //录入表数据
        $condition['id']=$data['id'];
        $id=$this->model->table('form_data_'.$model['table'])->data($data)->where($condition)->update(); 
        return $id;
    }


    //内容信息
    public function info($id,$table){
        //读取模型表
        $aid=$this->model->table('form_data_'.$table)->where('fid='.$id)->find();
        return $aid;
    }

    //内容删除
    public function del($id,$fid){
        if(empty($id)||empty($fid)){
            return;
        }
        //读取模型表
        $model=model('form')->info($fid);
        //删除操作
        $condition['id']=$id;
        return $this->model->table('form_data_'.$model['table'])->where($condition)->delete();
    }
    //获取字段显示
    public function get_list_model($type,$str,$config){
        switch ($type) {
            case '1':
            case '2':
            case '4':
                return $str;
                break;
            case '3':
                return html_out($str);
                break;
            case '5':
            case '7':
                $list=explode("\n",html_out($config));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    if($value[1]==$str){
                        return $value[0];
                    }
                }
                break;
            case '6':
            case '8':
				$str=explode(",",in($str));
                $list=explode("\n",html_out($config));
                foreach ($list as $key) {
                    $value=explode('|',$key);
					if(in_array($value[1],$str)){
						$html.=$value[0]." ";
					}
                }
				return $html;
                break;
            case '11':
                return date('Y-m-d H:i:s',$str);
                break;
            default:
                return $str;
                break;
        }

    }


}