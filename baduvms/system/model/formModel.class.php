<?php
class formModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }
    //表单字段内容
    public function get_form($id){
        if(1>intval($id)){
            return '参数不正确';
        }
		$info=$this->info($id,'id');
		if (2>count($info)) return '没有建立报名表单';
		$info['data']=$this->field_list($id);
		return $info; 
    }
    //获取内容
    public function info($form,$field='table')
    {
        $where[$field]=$form;
		return $this->model->table('form')->where($where)->find();
    }
    //内容信息
    public function info_table($id,$table){
        //读取模型表
        $aid=$this->model->table('form_data_'.$table)->where('id='.$id)->find();
        return $aid;
    }

    //表单字段
    public function field_list($id){
    	return $this->model->table('form_field')->where('fid='.$id)->order('sequence asc,id asc')->select();
    }

    //表单内容列表
    public function form_list($table,$limit,$order,$where){
        if($where){
            $data=$this->model->table('form_data_'.$table)->limit($limit)->where($where)->order($order)->select();
        }else{
            $data=$this->model->table('form_data_'.$table)->limit($limit)->order($order)->select();
        }
    	return $data;
    }

    //表单内容统计
    public function form_count($table,$where){
        if($where){
            $data=$this->model->table('form_data_'.$table)->where($where)->count();
        }else{
            $data=$this->model->table('form_data_'.$table)->count();
        }
    	return $data;
    }

    //添加内容
    public function add($data,$form)
    {
        return $this->model->table('form_data_'.$form)->data($data)->insert();
    }

    //格式化录入字段内容
    public function field_in($value,$type,$field) {
        switch ($type) {
            case '1':
            case '2':
			case '3':
            case '4':
                return html_in($value,true);
                break;
            case '5':
            case '6':
            case '7':
            case '8':
                return intval($value);
                break;
            case '9':
                return strtotime($value);
                break;
            case '10':
                return serialize($value);
                break;
            default:
                return html_in($value);
                break;
        }
    }
    public function field_in_check($value,$form) {
        $type=$form['type'];
		if (in_array($type,array(1,4))){
			$value=in($value);
			if (strlen($value)>$form['maxvalue']){
				return $this->msg('【'.$form['name'].'】最多可输入【'.($form['maxvalue']/2).'】字，请检查！',0);
			}
			return $value;
		}
		if (in_array($type,array(2,5,7))){
			$value=intval($value);
			if (strlen($value)>$form['maxvalue']){
				$msg['msg']='内容';
				$msg['status']=false;
			}	
			return $value;
		}
		if (in_array($type,array(3))){
			$value=html_in($value,true);
			if (strlen($value)>100000){
				return $this->msg('【'.$form['name'].'】最多可输入【5000】字，请检查！',0);
			}
			return $value;
		}
		if (in_array($type,array(6,8))){
			$value=in($value,true);
			if (!is_array($value) || count($value)>$form['maxvalue']){
				return $this->msg('【'.$form['name'].'】最多可选【'.($form['maxvalue']).'】项，请检查！',0);
			}
			return implode(',',$value);
		}
		return html_in($value);
    }
	
	
	
	
    //格式化默认字段值
    public function format_value($str,$array=array()){
        if(empty($str)){
            return $str;
        }
		$str = str_replace("{", "", $str);
		$str = str_replace("}", "", $str);
        switch ($str) {
            case 'uid':case 'userid':case 'vipid':
                return $this->user['id'];
                break;
            case 'vcard':case 'card':case 'vid':
                return $this->user['vcard'];
                break;
            case 'mobile':case 'telphone':case 'mob':case 'tel':
                return $this->user['mobile'];
                break;
            case 'email':
                return $this->user['email'];
                break;
            case 'user':case 'nickname':case 'wangming':case 'username':
                return $this->user['user'];
                break;
            case 'name':case 'realname':case 'xingming':
                return $this->user['realname'];
                break;
            case 'groupid':
                return $this->user['gid'];
                break;
            case 'groupname':
                return $this->user['gname'];
                break;
            case 'sex':case 'xingbie':
                return $this->user['sex'];
                break;
            case 'status':case 'vip':
                return $this->user['status']?'已认证':'未认证';
                break;
            case 'avatar':case 'touxiang':case 'photo':
                return model('badu')->get_photo($this->user['id']);
                break;
            case 'eventid':case 'eid':
                return $array['id'];
                break;
            case 'title':case 'biaoti':
                return $array['title'];
                break;
            case 'zmr':case 'daidui':
                return $array['zmr'];
                break;
            case 'zmtel':case 'daiduitel':
                return $array['zmtel'];
                break;
            case 'vtime':case 'shishu':
                return $array['vtime'];
                break;
            default:
                return html_in($str);
                break;
        }
	}
    //获取字段HTML
    public function get_field_html($info,$data=null,$array=array()){
        $info['default']=html_out($info['default']);

        if(!empty($data)){
            $info['default']=$data;
        }
    	$info['default']=$this->format_value($info['default'],$array);
		//dump($array);
		if ($array['zmdx']==0) {$info['readmodel']=0;}
		
		$html='';
        switch ($info['type']) {
            case '1':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-inline">
						<input id="'.$info['field'].'" name="'.$info['field'].'" type="text" value="'.$info['default'].'" class="layui-input"';
                if(!empty($info['must'])){$html.='required lay-verify="required" placeholder="请输入'.$info['name'].'"';}
                if(intval($info['readmodel'])==1){$html.=' readonly="readonly"';}
                $html.='/></div></div>';
                break;
            case '2':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">
						<input id="'.$info['field'].'" name="'.$info['field'].'" type="tel" value="'.$info['default'].'" class="layui-input"';
                if(!empty($info['must'])){$html.=' placeholder="'.$info['tip'].'" data-rules="required|number"';}
                if(intval($info['readmodel'])==1){$html.=' readonly="readonly"';}
                $html.='/></div></div>';
                break;
            case '3':
                $html.='<div class="layui-form-item layui-form-text"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">
                <textarea id="'.$info['field'].'" name="'.$info['field'].'" rows="3" class="layui-textarea"';
                if(intval($info['readmodel'])==1){$html.=' readonly="readonly"';}
				if(!empty($info['must'])){$html.='required lay-verify="required" placeholder="请输入'.$info['name'].'"';}
				$html.='>'.$info['default'].'</textarea>';
                $html.='</div></div>';
                break;
            case '4':
                $html.='<div class="layui-form-item"><input name="'.$info['field'].'" type="hidden" id="'.$info['field'].'" value="'.$info['default'].'"';
				if(intval($info['readmodel'])==1){$html.=' readonly="readonly"';}
				$html.='/></div>';
                break;
            case '5':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
					$select_list.='<input type="radio" name="'.$info['field'].'"  value="'.$value[1].'" ';
                    //if($info['default']==''){$info['default']=1;}
                    if($info['default']==$value[1]){$select_list.='checked="checked" ';}
                 	if(!empty($info['must'])){$select_list.=' required';}
 					if(intval($info['readmodel'])==1){$select_list.=' readonly="readonly"';}
                  	$select_list.=' title="'.$value[0].'"/><span class="title">'.$value[0].'</span>';
                   }
				$html.=$select_list;
                $html.='</div></div>';
                break;
            case '6':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
  					$select_list.='<input type="checkbox" name="'.$info['field'].'[]" value="'.$value[1].'" ';
                    //if($info['default']==''){$info['default']=1;}
                    if($info['default']==$value[1]){$select_list.='checked="checked" ';}
                 	if(!empty($info['must'])){$select_list.=' data-rules="required"';}
 					//if(intval($info['readmodel'])==1){$select_list.=' disabled="disabled"';}
                    $select_list.=' title="'.$value[0].'"/><span class="title">'.$value[0].'</span>';
                }
				$html.=$select_list;
                $html.='</div></div>';
                break;
            case '7':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">';
                $list=explode("\n",html_out($info['config']));
				$select_list='<select name="'.$info['field'].'" id="'.$info['field'].'"';
				if(!empty($info['must'])){$select_list.=' lay-verify="required"';}
				$select_list.='>';
                foreach ($list as $key) {
                    $value=explode('|',$key);
					$select_list.='<option value="'.$value[1].'"';
                    if($info['default']==$value[1]){$select_list.=' selected="selected" ';}
                    $select_list.='>'.$value[0].'</option>';
                }
				$html.=$select_list;
                $html.='</select></div></div>';
                break;
            case '8':
                $html.='<div class="layui-form-item"><label class="layui-form-label">'.$info['name'].'</label><div class="layui-input-block">';
                $list=explode("\n",html_out($info['config']));
				$size=count($list);
				$size=($size>10)?10:$size;
				$select_list='<select name="'.$info['field'].'[]" id="'.$info['field'].'" size="'.$size.'" multiple';
				if(!empty($info['must'])){$select_list.=' lay-verify="required"';}
				$select_list.='>';
                foreach ($list as $key) {
                    $value=explode('|',$key);
					$select_list.='<option value="'.$value[1].'"';
                    if($info['default']==$value[1]){$select_list.=' selected="selected" ';}
                    $select_list.='>'.$value[0].'</option>';
                }
				$html.=$select_list;
                $html.='</select></div></div>';
                break;
            case '9':
                $config=explode("\n", $info['config']);
                if(empty($config[0])){
                    $config='Y-m-d H:i:s';
                }
                if(empty($config[1])){
                    $config='yyyy-MM-dd HH:mm:ss';
                }
                if($data){
                    $info['default']=date($config,intval($info['default']));
                }else{
                    $info['default']=date($config);
                }                
                $html.='<input name="'.$info['field'].'" type="hidden" id="'.$info['field'].'" value="'.$info['default'].'"/>';
                break;

        }
        return $html;
    }

}?>