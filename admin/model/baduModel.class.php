<?php
class baduModel extends commonModel{
    public function __construct()
    {
        parent::__construct();
    }
    //获取列表
    public function get_data($table="content",$where=null,$limit=10,$order="id desc"){
        $data=$this->model->table($table)->where($where)->limit($limit)->order($order)->select();
        return $data;
    }
    //获取内容
    public function get_info($table,$id){
        return $this->model->table($table)->where('id='.$id)->find();
    }
    //获取列表
    public function select($where=null,$table="area",$limit=10,$order="id desc"){
        $data=$this->model->table($table)->where($where)->limit($limit)->order($order)->select();
        return $data;
    }
	public function count($where=null,$table='vote') {
        return $this->model->table($table)->where($where)->count();
	}
    //获取内容
    public function find($table,$where){
        return $this->model->table($table)->where($where)->find();
    }
    //更新数据
	public function set_data($where=null,$data,$table='vote') {
        return $this->model->table($table)->data($data)->where($where)->update();
    }
    //更新数据
	public function del_data($where=null,$table='vote') {
        return $this->model->table($table)->where($where)->delete();
    }
	//提交数据
    public function in_data($data,$table='vote_data'){
        return $this->model->table($table)->data($data)->insert();
    }


    //地理位置
    public function get_area($id = 0){
		if (is_null($id) || (strlen($id)==0)) $id=0;
        $data=$this->select(1,'area',300);
        $cat = new Category(array('id', 'pid', 'name', 'cname'));
        $list = $cat->getTree($data, $id);
        return $list;
    }
    //获取地理位置ID
    public function get_area_id($id){
        return $this->model->field('id')->table('area')->where('pid='.$id)->select();
    }

    //小时选择
    public function get_h_select($data=NULL){
		if (empty($data)) $data=date('H');
       ($data == 7)?$selected = " selected='selected'":$selected = "";
        $html =  "<option value='7' " . $selected . "/>7</option>";
       ($data == 8)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='8' " . $selected . "/>8</option>";
       ($data == 9)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='9' " . $selected . "/>9</option>";
       ($data == 10)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='10' " . $selected . "/>10</option>";
       ($data == 11)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='11' " . $selected . "/>11</option>";
       ($data == 12)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='12' " . $selected . "/>12</option>";
       ($data == 13)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='13' " . $selected . "/>13</option>";
       ($data == 14)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='14' " . $selected . "/>14</option>";
       ($data == 15)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='15' " . $selected . "/>15</option>";
       ($data == 16)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='16' " . $selected . "/>16</option>";
       ($data == 17)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='17' " . $selected . "/>17</option>";
       ($data == 18)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='18' " . $selected . "/>18</option>";
       ($data == 19)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='19' " . $selected . "/>19</option>";
       ($data == 20)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='20' " . $selected . "/>20</option>";
       ($data == 21)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='21' " . $selected . "/>21</option>";
       ($data == 22)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='22' " . $selected . "/>22</option>";
       ($data == 23)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='23' " . $selected . "/>23</option>";
       ($data == 00)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='00' " . $selected . "/>00</option>";
       ($data == 1)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='1' " . $selected . "/>1</option>";
       ($data == 2)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='2' " . $selected . "/>2</option>";
       ($data == 3)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='3' " . $selected . "/>3</option>";
       ($data == 4)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='4' " . $selected . "/>4</option>";
       ($data == 5)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='5' " . $selected . "/>5</option>";
       ($data == 6)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='6' " . $selected . "/>6</option>";
        return $html;
    }
    //分钟选择
    public function get_i_select($data = NULL){
       ($data == 0)?$selected = " selected='selected'":$selected = "";
        $html =  "<option value='00' " . $selected . "/>00</option>";
       ($data == 10)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='10' " . $selected . "/>10</option>";
       ($data == 20)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='20' " . $selected . "/>20</option>";
       ($data == 30)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='30' " . $selected . "/>30</option>";
       ($data == 40)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='40' " . $selected . "/>40</option>";
       ($data == 50)?$selected = " selected='selected'":$selected = "";
        $html .=  "<option value='50' " . $selected . "/>50</option>";
		if (empty($data) || !in_array($data,array(0,10,20,30,40,50))) {
			$data=date('i');
	        $selected = " selected='selected'";
	        $html .=  "<option value='".$data."' " . $selected . "/>".$data."</option>";
		}
        return $html;
    }
	//民族
	public function get_ethnic($name='ethnic',$id = NULL){
		$data='请选择,汉族,蒙古族,回族,藏族,维吾尔族,苗族,彝族,壮族,布依族,朝鲜族,满族,侗族,瑶族,白族,土家族,哈尼族,哈萨克族,傣族,黎族,傈僳族,佤族,畲族,高山族,拉祜族,水族,东乡族,纳西族,景颇族,柯尔克孜,土族,达斡尔族,仫佬族,羌族,布郎族,撒拉族,毛南族,仡佬族,锡伯族,阿昌族,普米族,塔吉克族,怒族,乌孜别克,俄罗斯族,鄂温克族,德昂族,保安族,裕固族,京族,塔塔尔族,独龙族,鄂伦春族,赫哲族,门巴族,珞巴族,基诺族,入籍,其他';
		$list = explode(',',$data);
		$html = '<select name="'.$name.'" id="'.$name.'" reg="." msg="请选择民族">';
		if ($id==0) $selected = " selected='selected'";
        foreach ($list as $k=>$vo){
             (''.$id.'' == ''.$k.'')?$selected = "selected='selected'":$selected = "";
             $html .= "<option " . $selected . " value='" . $k . "'>" . $vo . "</option>";
         }
		$html .= '</select>';
        return $html;
	}
	//政治面貌
	public function get_political($name='political',$id = NULL){
		$data='请选择,群众,团员,党员,其他党派人士';
		$list = explode(',',$data);
		$html = '<select name="'.$name.'" id="'.$name.'" reg="." msg="请选择政治面貌">';
		if ($id==0) $selected = " selected='selected'";
        foreach ($list as $k=>$vo){
             (''.$id.'' == ''.$k.'')?$selected = "selected='selected'":$selected = "";
             $html .= "<option " . $selected . " value='" . $k . "'>" . $vo . "</option>";
         }
		$html .= '</select>';
        return $html;
	}

	//学历
	public function get_degree($name='degree',$id = NULL){
		$data='请选择,小学,初中,高中,中专,大专,本科,硕士,博士,其他';
		$list = explode(',',$data);
		$html = '<select name="'.$name.'" id="'.$name.'" reg="." msg="请选择学历">';
		if ($id==0) $selected = " selected='selected'";
        $html .= "<option " . $selected . " value=''>== 请选择 ==</option>";
        foreach ($list as $k=>$vo){
             (''.$id.'' == ''.$k.'')?$selected = "selected='selected'":$selected = "";
             $html .= "<option " . $selected . " value='" . $k . "'>" . $vo . "</option>";
         }
		$html .= '</select>';
        return $html;
	}
	//休息时间
	public function get_freetime($name='freetime',$id = NULL){
		$data='请选择,平常周一有时间,平常周二有时间,平常周三有时间,平常周四有时间,平常周五有时间,平常周六有时间,平常周日有时间,周六、周日有时间,平常周末晚上有时间,只有晚上有时间,周一至周五都有时间,工作轮休制不能确,任何时候都可以';
		$list = explode(',',$data);
		$html = '<select name="'.$name.'" id="'.$name.'" reg="." msg="请选择学历">';
		if ($id==0) $selected = " selected='selected'";
        foreach ($list as $k=>$vo){
             (''.$id.'' == ''.$k.'')?$selected = "selected='selected'":$selected = "";
             $html .= "<option " . $selected . " value='" . $k . "'>" . $vo . "</option>";
         }
		$html .= '</select>';
        return $html;
	}
    public function get_checkbox($name='whyjoin', $id = '' ,$type =1){
		if (empty($type)) $type=1;
		($type==1)?$type = "checkbox":$type = "radio";
        switch ($name) {
            case 'whyjoin':
                $data = '尽公民责任,增加经验和知识面,帮助有需要的人,对义务工作有兴趣,结交朋友,自我发展,为未来工作做准备,学习新知识和技能,服务社会,扩大生活圈子,善用余暇,其他';
                break;
            case 'target':
                $data = '儿童,青少年,老人,成人,农民工,市民大众,精神病人士,残障人士,戒毒人士,无限制,其他';
                break;
            case 'skills':
                $data =	'电脑知识和应用,网页/多媒体设计,音乐,新闻传播,美术设计,教学/培训,财务管理,行政管理,外语翻译,水电/家电维修,运动,导游,表演,法律,医疗护理,文字工作,环保,驾驶,公关,其他';
                break;
            case 'service':
                $data =	'青少年成长服务,长者情感陪护服务,文化艺术表演服务,讲师培训服务,农民工娱乐服务,环保志愿服务,社区大型活动,捐资助学服务,技能辅导服务,法律维权服务,宣传策划服务,程序开发服务,平面设计服务,动画设计服务,广告设计服务,系统维护服务,办公文字工作,会计出纳服务,其他专业技能服务,其他服务';
                break;
            case 'training':
                $data =	'基础理念训练课程,志愿者领袖课程,服务技巧训练课程,脑瘫心理学等专业志愿服务培训课程,其他志愿者培训课程';
                break;
        }
		$list = explode(',',$data);
        foreach ($list as $k=>$vo){
        	(in_array($k,(array)$id))?$selected = " checked='checked'":$selected = "";
             $html .= " <input name='info[".$name."][]' type='".$type."' value='".$k."' " . $selected . "/> ".$vo."&nbsp;&nbsp;";
         }
		$html .= '</select>';
        return $html;
    }
	//获取头像
	public function get_photo($userid, $width = 80, $height = 80,$do=false)
	{
		$dir = array();
		list($path, $filename) = $this->set_photo_path($userid);
		$avatar = "/avatar/". $path . '/'. $filename . ".jpg";
		$file= is_file(__UPDDIR__ . $avatar) ? thumb(__UPDDIR__ . $avatar, $width, $height) : __PUBLIC__ . "/images/nohead.png";
		if ($do==true){
			return $file;
		}
		echo $file;
	}	
	public function set_photo_path($userid)
	{
		$dir = array();
		$userid = sprintf("%09d", $userid);
		$dir[] = substr($userid, 0, 3);
		$dir[] = substr($userid, 3, 2);
		$dir[] = substr($userid, 5, 2);
		$dir = implode("/", $dir);
		$name = substr($userid, -2);
		$return = array($dir, $name);
		return $return;
	}
    //获取字段类型名称
    public function field_type($id=null,$name=false)
    {
        $list=array(
            1=> array(
                'name'=>'文本框'
                ),
            2=> array(
                'name'=>'多行文本'
                ),
            3=> array(
                'name'=>'编辑器'
                ),
            4=> array(
                'name'=>'文件上传'
                ),
            10=> array(
                'name'=>'单图片上传'
                ),
            5=> array(
                'name'=>'组图上传'
                ),
            6=> array(
                'name'=>'下拉菜单'
                ),
            7=> array(
                'name'=>'日期和时间'
                ),
            8=> array(
                'name'=>'单选'
                ),
            9=> array(
                'name'=>'多选'
                ),
           11=> array(
                'name'=>'发布时间'
                ),
           12=> array(
                'name'=>'隐藏域表单'
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

    //格式化字段
    public function field_data($data) {
        $property=$this->field_property($data['property']);
        if($data['property']==4){
            $data['decimal_len']=','.$data['decimal'];
        }else{
            $data['decimal_len']='';
        }

        if(intval($data['len'])>$property['maxlen']){
            $data['len']=$property['maxlen'];
        }
        $data['default']=html_in($data['default']);
        $data['config']=html_in($data['config']);
        return $data;
    }
    //获取字段显示
    public function get_list_model($type,$str,$config){
        switch ($type) {
            case '1':
            case '2':
            case '4':
            case '12':
                return $str;
                break;
            case '3':
                return html_out($str);
                break;
            case '5':
                if(!empty($str)){
                    $array=unserialize($str);
                    if(!empty($array)){
                        foreach ($array as $value) {
                            $strs.=$value['url'].'<br>';
                        }
                    }
                }
                return $strs;
                break;
            case '6':
            case '8':
                $list=explode("\n",html_out($config));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    if($value[1]==$str){
                        return $value[0];
                    }
                }
                break;
            case '7':
            case '11':
                return date('Y-m-d H:i:s',$str);
                break;
            case '9':
                $list=explode("\n",html_out($config));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    if($value[1]==$str){
                        $strs.=$value[0].' ';
                    }
                }
                return $strs;
                break;
            case '10':
                return '<img name="" src="'.$str.'" alt="" style="max-width:170px; max-height:90px; _width:170px; _height:90px;" />';
                break;
            default:
                return $str;
                break;
        }

    }

    //获取字段HTML
    public function get_field_html($info,$data=null){
        $info['default']=html_out($info['default']);

        if(!empty($data)){
            $info['default']=$data;
        }

        $html='';
        switch ($info['type']) {
            case '1':
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td>
                    <input name="'.$info['field'].'" type="text" class="text_value" id="'.$info['field'].'" value="'.$info['default'].'" 
                ';
                if(!empty($info['must'])){
                    $html.=' reg="\S" msg="'.$info['name'].'不能为空！" ';
                }
                $html.='/>
                    </td>
                    <td>'.$info['tip'].'</td>
                </tr>
                ';
                break;
            case '2':
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td><textarea name="'.$info['field'].'" class="text_textarea" id="'.$info['field'].'" >'.$info['default'].'</textarea>
                    </td>
                    <td>'.$info['tip'].'</td>
                </tr>
                ';
                break;
            case '3':
                
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td colspan="2"><textarea name="'.$info['field'].'" style="width:100%; height:350px;" id="'.$info['field'].'">'.$info['default'].'</textarea>
                    '.module('editor')->get_editor_upload($info['field'].'_upload','editor_'.$info['field']).'
                    <input type="button" id="'.$info['field'].'_upload" class="button_small" style="margin-top:10px;" value="上传图片和文件到编辑器" />
                    </td>
                </tr>
                ';
                $html.=module('editor')->get_editor($info['field'],true);
                break;
            case '4':
                
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td>
                    <input name="'.$info['field'].'" type="text"  class="text_value"  style="width:200px; float:left"  id="'.$info['field'].'" value="'.$info['default'].'" 
                ';
                if(!empty($info['must'])){
                    $html.=' reg="\S"  msg="'.$info['name'].'不能为空！" ';
                }
                $html.='/>
                &nbsp;&nbsp;<input type="button" id="'.$info['field'].'_botton" class="button_small" value="选择文件" />
                    </td>
                    <td>'.$info['tip'].'</td>
                </tr>
                ';
                $html.=module('editor')->get_file_upload($info['field'].'_botton',$info['field'],true);
                break;
            case '10':
                
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td>
                    <input name="'.$info['field'].'" type="text"  class="text_value"  style="width:200px; float:left"  id="'.$info['field'].'" value="'.$info['default'].'" 
                ';
                if(!empty($info['must'])){
                    $html.=' reg="\S"  msg="'.$info['name'].'不能为空！" ';
                }
                $html.='/>
                &nbsp;&nbsp;<input type="button" id="'.$info['field'].'_botton" class="button_small" value="选择图片" />
                    </td>
                    <td>'.$info['tip'].'</td>
                </tr>
                ';
                $html.=module('editor')->get_image_upload($info['field'].'_botton',$info['field'],true);
                break;
            case '5':
                
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td colspan="2">
                    <input type="button" id="'.$info['field'].'_button" class="button_small" value="上传多图" />
                    <div class="fn_clear"></div>
                    <div class="images">
                    <ul id="'.$info['field'].'_list" class="images_list">';

                if(!empty($data)){
                $info['default']=unserialize($info['default']);
                if(!empty($info['default'])){
                foreach ($info['default'] as $value) {
                $html.="<li>
                        <div class='pic' id='images_button'>
                        <img src='".$value['url']."' width='125' height='105' />
                        <input  id='".$info['field']."[]' name='".$info['field']."[]' type='hidden' value='".$value['url']."' />
                        <input  id='".$info['field']."_original[]' name='".$info['field']."_original[]' type='hidden' value='".$value['original']."' />
                        </div>
                        <div class='title'>标题： <input name='".$info['field']."_title[]' type='text' id='".$info['field']."_title[]' value='".$value['title']."' /></div>
                        <div class='title'>排序： <input id='".$info['field']."_order[]' name='".$info['field']."_order[]' value='".$value['order']."' type='text' style='width:50px;' /> <a href='javascript:void(0);' onclick='$(this).parent().parent().remove()'>删除</a></div>
                    </li>";
                }
                }
                }

                $html.="</ul>
                    <div style='clear:both'></div>
                    </div>
                    </td>
                </tr>
                ";
                $html.=module('editor')->get_images_upload($info['field'],$ajax=true);
                break;
            case '6':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='</td>';
                $html.='<td>';
                $select_list='<select name="'.$info['field'].'" id="'.$info['field'].'">';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    $select_list.='<option ';
                    if($info['default']==$value[1]){
                        $select_list.='selected="selected" ';
                    }
                    $select_list.=' value="'.$value[1].'">'.$value[0].'</option>';
                }
                $select_list.='</select>';
                $html.=$select_list;
                $html.='</td>';
                $html.='<td>';
                $html.=$info['tip'];
                $html.='</td>';
                $html.='</tr>';
                break;
            case '7':
                $config=explode("\n", $info['config']);
                if(empty($config[0])){
                    $config[0]='Y-m-d';
                }
                if(empty($config[1])){
                    $config[1]='yyyy-MM-dd';
                }
                if($data){
                    $info['default']=date($config[0],intval($info['default']));
                }else{
                    $info['default']=date($config[0]);
                }
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='</td>';
                $html.='<td>';
                $html.='<input name="'.$info['field'].'"  id="'.$info['field'].'" type="text" class="text_value" style="width:210px; float:left" value="'.$info['default'].'"';
                if($info['must']==1){
                    $html.=' reg="\S" msg="'.$info['name'].'不能为空" ';
                }
                $html.='/><div  id="'.$info['field'].'_button" class="time"></div></td>';
                $html.='<td>';
                $html.=$info['tip'];
                $html.='</td>';
                $html.='</tr>';
                $html.='<script>';
                $html.="$('#".$info['field']."_button').calendar({ id:'#".$info['field']."',format:'".$config[1]."'});";
                $html.='</script>';
                break;
            case '8':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='</td>';
                $html.='<td>';
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
                $html.='<td>';
                $html.=$info['tip'];
                $html.='</td>';
                $html.='</tr>';
                break;
            case '9':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='</td>';
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
                $html.='<td>';
                $html.=$info['tip'];
                $html.='</td>';
                $html.='</tr>';
                break;
            case '11':
                $config=explode("\n", $info['config']);
                if(empty($config[0])){
                    $config[0]='Y-m-d H:i:s';
                }
                if(empty($config[1])){
                    $config[1]='yyyy-MM-dd HH:mm:ss';
                }
                if($data){
                    $info['default']=date($config[0],intval($info['default']));
                }else{
                    $info['default']=date($config[0]);
                }                
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td>'.$info['default'].'
                    <input name="'.$info['field'].'" type="hidden" id="'.$info['field'].'" value="'.$info['default'].'"/></td>
                    <td>'.$info['tip'].'</td>
                </tr> ';
                break;
            case '12':
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'</td>
                    <td>'.$info['default'].'
                    <input name="'.$info['field'].'" type="hidden" id="'.$info['field'].'" value="'.$info['default'].'"/></td>
                    <td>'.$info['tip'].'</td>
                </tr> ';
                break;

        }
        return $html;
    }
    //获取字段HTML
    public function get_field_text($info,$data=null){
        $info['default']=html_out($info['default']);

        if(!empty($data)){
            $info['default']=$data;
        }

        $html='';
        switch ($info['type']) {
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '10':
            case '11':
            case '12':
                $html.='
                <tr>
                    <td align="right">'.$info['name'].'：</td>
                    <td>'.$info['default'].'</td>
                </tr>
                ';
                break;
            case '6':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='：</td>';
                $html.='<td>';
                $list=explode("\n",html_out($info['config']));
                foreach ($list as $key) {
                    $value=explode('|',$key);
                    if($info['default']==$value[1]){
                        $html.=$value[0];
                    }
                }
                $html.='</td>';
                $html.='</tr>';
                break;
            case '7':
                $config=explode("\n", $info['config']);
                if(empty($config[0])){
                    $config[0]='Y-m-d';
                }
                if(empty($config[1])){
                    $config[1]='Y-m-d';
                }
                if($data){
                    $info['default']=date($config[0],intval($info['default']));
                }else{
                    $info['default']=date($config[0]);
                }
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='：</td>';
                $html.='<td>';
                $html.=$info['default'];
                $html.='</td>';
                $html.='</tr>';
                break;
            case '8':
                $html.='<tr>';
                $html.='<td align="right">';
                $html.=$info['name'];
                $html.='：</td>';
                $html.='<td>';
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


    //格式化录入字段内容
    public function field_in($value,$type,$field,$data='') {
        switch ($type) {
            case '1':
            case '4':
            case '11':
                return in($value);
                break;
            
            case '2':
            case '3':
                return html_in($value);
                break;
            case '5':
                if(is_array($value)){
                    $str1=$field.'_title';
                    $str2=$field.'_order';
                    $str3=$field.'_original';
                    $title=$data[$str1];
                    $order=$data[$str2];
                    $original=$data[$str3];
                   foreach ($value as $key=>$vo) {
                        $list[$key]['url']=$vo;
                        $list[$key]['original']=$original[$key];
                        $list[$key]['title']=$title[$key];
                        $list[$key]['order']=$order[$key];
                   }
                }
                return serialize($list);
                break;
            case '6':
                return in($value);
                break;
            case '8':
                return intval($value);
                break;
            case '7':
            case '11':
                return strtotime($value);
                break;
            case '9':
                return serialize($value);
                break;
            default:
                return in($value);
                break;
        }
    }


















}?>