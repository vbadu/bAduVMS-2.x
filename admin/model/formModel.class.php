<?php
class formModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }
    //获取栏目树形列表
    public function category_list($id = 0) {
        $sql="SELECT * FROM {$this->model->pre}form WHERE display=1 ORDER BY id ASC";
        $data=$this->model->query($sql); 
        return $data;
    }
    //获取表单列表
    public function form_list($where=null) {
        return $this->model->table('form')->where($where)->order('id asc')->select();
    }

    //获取表单信息
    public function table_info($table,$fid=null) {
        $where="`table`='".$table."'";
        if(!empty($fid)){
        $where.=' AND id<>'.$fid;
        }
        return $this->model->table('form')->where($where)->find();
    }

    //表单信息
    public function info($id,$field='*')
    {
        return $this->model->table('form')->field($field)->where('id='.intval($id))->find();
    }

    //修改关联信息
    public function associate_edit() {
        $info=$this->model->table('form')->order('id desc')->find();
        $data['fid']=$info['id'];
        $condition['fid']='101010';
        return $this->model->table('form_field')->data($data)->where($condition)->update();
    }

    //添加表单
    public function add($data)
    {
        $where['table']=$data['table'];
		$sql = "SHOW TABLES LIKE   '%{$this->model->pre}form_data_{$data['table']}%';";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
            return false;
        }
         //添加初始表
        $sql="
        CREATE TABLE IF NOT EXISTS `{$this->model->pre}form_data_{$data['table']}` (
          `fid` int(10) NOT NULL AUTO_INCREMENT,
          PRIMARY KEY (`fid`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
        ";
        $this->model->query($sql);
        return $this->model->table('form')->data($data)->insert();
    }

    //修改表单
    public function edit($data)
    {
        $info=$this->info($data['id']);
		$sql = "SHOW TABLES LIKE   '%{$this->model->pre}form_data_{$info['table']}%';";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
			//修改表
			$sql="
			ALTER TABLE {$this->model->pre}form_data_{$info['table']} RENAME TO {$this->model->pre}form_data_{$data['table']}
			";
			$this->model->query($sql);
	
			$condition['id']=intval($data['id']);
			return $this->model->table('form')->data($data)->where($condition)->update(); 
		}
		return $this->add($data);
    }

    //删除表单
    public function del($id)
    {
        $info=$this->info($id);
		$sql = "SHOW TABLES LIKE   '%{$this->model->pre}form_data_{$info['table']}%';";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
			//删除表
			$sql="
			DROP TABLE `{$this->model->pre}form_data_{$info['table']}`
			";
			$this->model->query($sql);
			//删除表内字段
			$this->model->table('form_field')->where('fid='.$id)->delete();
			return $this->model->table('form')->where('id='.$id)->delete(); 
        }
        return false;
    }
    public function find_field_data($fid,$condition=null)
    {
        $where['fid']=$fid;
		if (is_array($condition)){
        	$where = array_merge($where,$condition);
		}
		return $this->model->table('form_field')->where($where)->count();
    }

    public function field_list_data($fid)
    {
        $where['fid']=$fid;
        //$where['system']=0;
		return $this->model->table('form_field')->where($where)->order('sequence asc,id asc')->select();
    }
    //字段修改
    public function field_edit_data($data,$id,$table=null)
    {
        if (empty($table)) return;
		return $this->model->table('form_data_'.$table)->data($data)->where(array('fid'=>$id))->update(); 
    }

    //字段列表
    public function field_list($fid)
    {
        $list=$this->field_list_data($fid);
		if(empty($list)){
            return;
        }
        foreach ($list as $key=>$vo) {
            $data[$key]=$vo;
            $data[$key]['type_name']=$this->field_type($vo['type'],true);
        }
        return $data;

    }

    //字段信息
    public function field_info($id){
        $condition['id']=$id;
        return $this->model->table('form_field')->where($condition)->find();
    }

    //检测字段重复
    public function field_check($fid,$field,$id=null){
        if(!empty($id)){
            $condition=' AND id<>'.$id;
        }
        return $this->model->table('form_field')->where('fid='.$fid.' AND field="'.$field.'"'.$condition)->count();
    }

    //字段添加
    public function field_add($data)
    {
		$model=$this->info($data['fid']);
        $property=$this->field_property($data['property']);
        $data=model('badu')->field_data($data);
		$sql = "desc {$this->model->pre}form_data_{$model['table']} {$data['field']}";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
			$count=$this->find_field_data($data['fid'],array('field'=>$data['field']));
			if (1>$count) return $this->model->table('form_field')->data($data)->insert();
		}else{
			//添加真实字段
			$sql="
			ALTER TABLE {$this->model->pre}form_data_{$model['table']} ADD {$data['field']} {$property['name']}({$data['len']}{$data['decimal_len']}) DEFAULT NULL
			";
			$this->model->query($sql);
			$data['admin_html']=html_in($data['admin_html']);
			return $this->model->table('form_field')->data($data)->insert();
		}
    }

    //字段修改
    public function field_edit($data)
    {
        $model=$this->info($data['fid']);
        $info=$this->field_info($data['id']);
        $property=$this->field_property($data['property']);
        $data=model('badu')->field_data($data);
		$sql = "desc {$this->model->pre}form_data_{$model['table']} {$info['field']}";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
			//修改真实字段
			$sql="
			ALTER TABLE {$this->model->pre}form_data_{$model['table']} CHANGE {$info['field']} {$info['field']} {$property['name']}({$data['len']}{$data['decimal_len']})
			";
			$this->model->query($sql);
			$condition['id']=intval($data['id']);
			$data['admin_html']=html_in($data['admin_html']);
			return $this->model->table('form_field')->data($data)->where($condition)->update(); 
		}else{
			//添加真实字段
			$sql="
			ALTER TABLE {$this->model->pre}form_data_{$model['table']} ADD {$data['field']} {$property['name']}({$data['len']}{$data['decimal_len']}) DEFAULT NULL
			";
			$this->model->query($sql);
			$data['admin_html']=html_in($data['admin_html']);
			return $this->model->table('form_field')->data($data)->insert();
		}
    }

    //字段删除
    public function field_del($data)
    {
        $info=$this->field_info($data['id']);
        $model=$this->info($info['fid']);
		$sql = "desc {$this->model->pre}form_data_{$model['table']} {$info['field']}";
		$status=$this->model->sql_query($sql);
		if (strlen($status[0])>0){
			$sql="ALTER TABLE {$this->model->pre}form_data_{$model['table']} DROP {$info['field']}";
			$this->model->query($sql);
			$condition['id']=intval($info['id']);
			return $this->model->table('form_field')->where($condition)->delete(); 
		}
    }

    //获取字段类型名称
    public function field_type($id=null,$name=false)
    {
        $list=array(
            1=> array(
                'name'=>'字符文本框'
                ),
            2=> array(
                'name'=>'整数文本框'
                ),
            3=> array(
                'name'=>'多行文本'
                ),
            4=> array(
                'name'=>'隐藏域表单'
                ),
            5=> array(
                'name'=>'单选'
                ),
            6=> array(
                'name'=>'多选'
                ),
            7=> array(
                'name'=>'下拉单选'
                ),
        );
        if(!empty($id)){
            if($name){
                return $list[$id]['name'];
            }else{
                return $list[$id];
            }
        }else{
            return $list;
        }
    }

    //获取字段属性
    public function field_property($id=null,$name=false)
    {
        $list=array(
            1=> array(
                'name'=>'varchar',
                'maxlen'=>255,
                ),
            2=> array(
                'name'=>'int',
                'maxlen'=>10,
                ),
            3=> array(
                'name'=>'text',
                'maxlen'=>0,
                ),
            4=> array(
                'name'=>'decimal',
                'maxlen'=>10,
                ),
        );
        if(!empty($id)){
            if($name){
                return $list[$id]['name'];
            }else{
                return $list[$id];
            }
        }else{
            return $list;
        }
    }
    //获取字段HTML
    public function get_field_text($info,$data=null){
        $info['default']=html_out($info['default']);

        if(!empty($data)){
            $info['default']=$data;
        }
		//dump($info);
        $html='';
        switch ($info['type']) {
            case '1':
            case '2':
            case '4':
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3"><input name="'.$info['field'].'" type="text" class="text_value" id="'.$info['field'].'" value="'.$info['default'].'"></td></tr>';
                break;
            case '3':
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3"><textarea name="'.$info['field'].'" class="text_textarea" id="'.$info['field'].'" >'.$info['default'].'</textarea></td></tr>';
                break;
            case '5':
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3">';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<input name="'.$info['field'].'" type="radio" value="'.$value[1].'" ';
                    if($info['default']==''){
                        $info['default']=1;
                    }
                    if($info['default']==$value[1]){
                        $select_list.='checked="checked" ';
                    }
                    $select_list.=' /> '.$value[0].'&nbsp;&nbsp;';
                }
                $html.=$select_list;
                $html.='</td>';
                $html.='</tr>';
				break;
            case '6':
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3">';
				$data=explode(",",in($info['default']));
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<input name="'.$info['field'].'[]" type="checkbox" value="'.$value[1].'" ';
					if(in_array($value[1],$data)){
						$select_list.='checked="checked" ';
					}
                    $select_list.=' /> '.$value[0].'&nbsp;&nbsp;';
                }
                $html.=$select_list;
                $html.='</td></tr>';
				break;
            case '7':
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3"><select name="'.$info['field'].'">';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<option value="'.$value[1].'"';
                    if($info['default']==''){
                        $info['default']=1;
                    }
                    if($info['default']==$value[1]){
                        $select_list.=' selected="selected" ';
                    }
                    $select_list.='>'.$value[0].'</option>';
                }
                $html.=$select_list;
                $html.='</td>';
                $html.='</tr>';
				break;
            case '8':
				$list=explode("\n",html_out($info['config']));
				$size=count($list);
				$size=($size>10)?10:$size;
                $html.='<tr><td align="right">'.$info['name'].'：</td><td colspan="3"><select name="'.$info['field'].'[]" size="'.$size.'" multiple>';
				$data=explode(",",in($info['default']));
                
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<option value="'.$value[1].'"';
					if(in_array($value[1],$data)){
						$select_list.=' selected="selected"';
					}
                    $select_list.=' /> '.$value[0].'&nbsp;&nbsp;';
                }
                $html.=$select_list;
                $html.='</td></tr>';
				break;
				
				
				$list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<input name="'.$info['field'].'" type="radio" value="'.$value[1].'" ';
                    if($info['default']==''){
                        $info['default']=1;
                    }
                    if($info['default']==$value[1]){
                        $select_list.='checked="checked" ';
                    }
                    $select_list.=' /> '.$value[0].'&nbsp;&nbsp;';
                }
                $html.=$select_list;
                $html.='</td>';
                $html.='</tr>';
                break;
            case '9':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='：</td>';
                $html.='<td>';
                $list=explode("\n",html_out($info['config']));
                
                if(!empty($data)){
                   $default=unserialize($info['default']);
                }else{
                   $default=explode('|', $info['default']);
                }
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<input name="'.$info['field'].'[]" type="checkbox" value="'.$value[1].'" ';
                    if($default<>''){
                    if(in_array($value[1], $default)){
                        $select_list.='checked="checked" ';
                    }
                    }
                    $select_list.=' /> '.$value[0].'&nbsp;&nbsp;';
                }
                $html.=$select_list;
                $html.='</td>';
                $html.='</tr>';
                break;
        }
        return $html;
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



}?>