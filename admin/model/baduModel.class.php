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



















}?>