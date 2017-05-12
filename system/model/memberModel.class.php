<?php
class memberModel extends commonModel{
    protected $table = 'member';

    public function __construct()
    {
        parent::__construct();
    }
	public function menuList($key){
		if(1>intval($key)){
        	$menu_list=array(
				0=>array('cid'=>0,'name'=>'注册义工','url'=>__APP__.'/member/register'),
				1=>array('cid'=>1,'name'=>'会员登录','url'=>__APP__.'/member/login'),
				2=>array('cid'=>2,'name'=>'密码重置','url'=>__APP__.'/member/repass'),
				3=>array('cid'=>3,'name'=>'服务时数查询','url'=>__APP__.'/member/vtime'),
            );
		}else{
        	$menu_list=array(
				0=>array('cid'=>11,'name'=>'我的首页','url'=>__APP__.'/member'),
				1=>array('cid'=>16,'name'=>'我的活动','url'=>__APP__.'/member/event'),
				2=>array('cid'=>17,'name'=>'我的证书','url'=>__APP__.'/member/cert'),
				3=>array('cid'=>12,'name'=>'个人资料','url'=>__APP__.'/member/profile'),
				4=>array('cid'=>13,'name'=>'我的头像','url'=>__APP__.'/member/avatar'),
				5=>array('cid'=>14,'name'=>'联系方式','url'=>__APP__.'/member/contact'),
				6=>array('cid'=>15,'name'=>'修改密码','url'=>__APP__.'/member/repwd'),
				7=>array('cid'=>18,'name'=>'退出登录','url'=>__APP__.'/member/logout'),
            );
		}
		return $menu_list;			
	}
    //当前用户信息
    public function current_member($cache=true,$del=false) {
        $uid=$_SESSION[$this->config['SPOT'].'_member'];
        if(empty($uid)){
            return ;
        }
        if($del){
            $this->cache->del('current_member_'.$uid);
            return ;
        }
        $member=$this->cache->get('current_member_'.$uid);
        if(empty($member)||$cache==false){
            $member=$this->model->table($this->table)->where('id='.$uid)->find();
            $this->cache->set('current_member_'.$uid, $member);
        }
        return $member;
    }	

    //获取用户内容
    public function get_list($where=NULL,$table=NULL,$limit=NULL,$order='id DESC'){ 
		$table=$this->table.$table;  
        return $this->model->table($table)->where($where)->limit($limit)->order('id asc')->select();
    }
    //统计
	public function get_count($where=NULL,$table=NULL) {
		$table=$this->table.$table;  
        return $this->model->table($table)->where($where)->count();
	}
    //获取用户内容
    public function info($where,$table=NULL,$field='*'){
		$table=$this->table.$table;  
        return $this->model->table($table)->field($field)->where($where)->find();
    }
    //获取用户内容
    public function infos($id){
		$data=$this->model->query("SELECT * FROM {$this->model->pre}{$this->table} A LEFT JOIN {$this->model->pre}{$this->table}_data B ON A.id = B.mid WHERE A.id={$id}");
        return $data[0]; 

    }
    //检测用户
    public function checkuser($id=1,$type='id',$get='count'){
		$where[$type]=$id;
        return $this->model->table($this->table)->where($where)->$get(); 
    }

    //检测重复用户
    public function count($data=null,$id=null,$databy='user',$table='')
    {
        if(!empty($id)){
            $where='id<>'.$id.' AND ';
        }
        if(!empty($data) && !empty($databy)){
            $where.=$databy.'="'.$data.'"';
        }
		$table=$this->table.$table;
        return $this->model->table($table)->where($where)->count(); 
    }

    //添加用户内容
    public function add($data,$table=NULL){
		$table=$this->table.$table;
        return $this->model->table($table)->data($data)->insert();
    }
    //编辑用户内容
    public function edit($data,$where,$table=NULL){
		$table=$this->table.$table;
        if(!is_array($where) && intval($where)>0){
            $temp['id']=intval($where);
			$where=$temp;
        }
        return $this->model->table($table)->data($data)->where($where)->update();
    }
    //删除用户内容
    public function del($where,$table=NULL){
		$table=$this->table.$table;
        return $this->model->table($table)->where($where)->delete(); 
    }
	//制作证书show0 输出 2 下载 1文件
	public function markcert($data,$reset=FALSE){
		$Root_path=getcwd();
		$Root_px=DIRECTORY_SEPARATOR;
		$Root_ux='/';
		list($photo_path, $rename) = model('badu')->set_photo_path($id);

		$Pub_Path=__PUBDIR__.$Root_px.'images'.$Root_px;
		$UPD_Path=__UPDDIR__.$Root_px.'cert'.$Root_px.$photo_path.$Root_px;
		$UPD_URL=__UPDURL__.$Root_ux.'cert'.$Root_ux.$photo_path.$Root_ux;
		$filename=(3>strlen($data['vcard']))?'cert_temp.png':$data['vcard'].'.png';

		if (!function_exists('gd_info')) return(array(0=>0,1=>'无法生成证书，请联系管理员开启GD库'));
		if (3>strlen($data['vcard'])) return(array(0=>0,1=>'无法生成证书，您查询的志愿者尚未注册或未认证。'));
		if (10>$data['vtime'] || 3>$data['gid']) return(array(0=>0,1=>'无法生成证书，'.$data['realname'].'还处于见习期。'));
		if (is_file($UPD_Path.'cert_'.$filename) && !$reset) return $UPD_URL.'/cert_'.$filename;

		if (!is_file($Pub_Path.'certificate.png')) return(array(0=>0,1=>'无法生成证书，证书模版不存在。'));
		if (!is_file($Pub_Path.'cert.ttf')) return(array(0=>0,1=>'无法生成证书，证书字库不存在。'));
		$cert[0] = $data['vcard'];  
		$cert[1] = date('Y年m月d日',$data['dtime']);  
		$cert[2] = $data['realname'];  
		$cert[3] = ($data['sex'])?'男':'女';  
		$cert[4] = vipstar($data['vtime']);  
		$cert[5] = $data['vtime'];  
		$cert[6] = "自入会以来，积极参与各项志愿活";  
		$cert[7] = "动，为志愿服务事业付出了很多心血与努力。";  
		$cert[8] = "鉴于您累计志愿服务时数已达本协会".$cert[4]."志";  
		$cert[9] = "愿者服务资质认定标准。特颁发此证以资鼓励！";  
		$cert[10] = "感谢您为志愿服务事业所做的贡献。";  
		$cert[11] = empty($this->config['certauthority'])?$this->config['copyright']:$this->config['certauthority'];
		if (empty($cert[11])) return(array(0=>0,1=>'无法生成证书，请联系管理员设置证书颁发单位。'));
		$cert[12] = date('Y年m月d日'); 
				
		$image = @imagecreatefrompng($Pub_Path.'certificate.png');
		$red 	= @imagecolorallocate($image,255,0,0);
		$black  = @imagecolorallocate($image,0,0,0);
		$huise  = @imagecolorallocate($image,128,138,135);
		$font	= $Pub_Path.'cert.ttf';
		
		@imageTTFText($image, 7, 0, 475, 490, $huise,$font, "本电子证书生成时间：".date('Y年m月d号 H:i:s'));
		@imageTTFText($image, 10, 0, 100, 350, $black,$font,"注册编号：");
		@imageTTFText($image, 10, 0, 165, 350, $red,$font, $cert[0]);
		@imageTTFText($image, 10, 0, 100, 375, $black,$font,"注册日期：");
		@imageTTFText($image, 10, 0, 165, 375, $black,$font, $cert[1]);
		@imageTTFText($image, 10, 0, 490, 85, $black,$font,"姓      名：");
		@imageTTFText($image, 10, 0, 550, 85, $black,$font, $cert[2]);
		@imageTTFText($image, 10, 0, 490, 110, $black,$font,"性      别：");
		@imageTTFText($image, 10, 0, 550, 110, $black,$font, $cert[3]);
		@imageTTFText($image, 10, 0, 490, 135, $black,$font,"服务星级：");
		@imageTTFText($image, 10, 0, 550, 135, $red,$font, $cert[4]);
		@imageTTFText($image, 10, 0, 490, 160, $black,$font,"服务时数：");
		@imageTTFText($image, 10, 0, 550, 160, $black,$font, $cert[5]." 小时");
		
		@imageTTFText($image, 11, 0, 395, 200, $red,$font, $cert[2]);
		@imageTTFText($image, 10, 0, 455, 200, $black,$font, $cert[6]);
		@imageTTFText($image, 10, 0, 370, 225, $black,$font, $cert[7]);
		@imageTTFText($image, 10, 0, 395, 250, $black,$font, $cert[8]);
		@imageTTFText($image, 10, 0, 370, 275, $black,$font, $cert[9]);
		@imageTTFText($image, 10, 0, 395, 300, $black,$font, $cert[10]);
		
		@imageTTFText($image, 11, 0, 520, 405, $black,$font, $cert[11]);
		@imageTTFText($image, 11, 0, 550, 440, $black,$font, date('Y年m月d日'));
		
		if ( !is_dir($UPD_Path) ) {
			if ( !mkdir($UPD_Path, 0777, true) ) {
				return ('上传目录' . $UPD_Path . '不存在');
			}
		}		
		$filepath	= $UPD_Path.'cert_'.$filename;
		@ImagePng($image, $filepath);
		@imagedestroy($image);
		//获取原图的大小list($b_w,$b_h) = getimagesize($filepath);
		//贴合头像
		$headpic=(5>strlen($data['image']))?$Pub_Path.'nohead.png':$data['image'];
		$headpic_thumb=(5>strlen($data['image']))?$Pub_Path.'90x100_nohead.png':'90x100_'.$data['image'];
		$headpic=Image::thumb($headpic,$headpic_thumb,'',90,100);
		if (!is_file($filepath)) return(array(0=>0,1=>'无法生成证书，证书图片不存在。'));
		$back = @imagecreatefrompng($filepath);
		if (!is_file($filepath)) return(array(0=>0,1=>'无法生成证书，证书图片不存在。'));
		$water = @imagecreatefrompng($headpic);
		//使用Inagecopy函数复制水印图片到指定位置,原图，贴图，位置x，位置y，角度0，角度0，贴图w，贴图h
		@imagecopy($back, $water, 390, 70, 0, 0, 90, 100);
		@ImagePng($back,$filepath);
		@imagedestroy($back);
	   	@imagedestroy($water);
		//贴合公章
		$cert_seal=$Pub_Path.'cert_seal.png';
		if (!is_file($cert_seal)) return(array(0=>0,1=>'无法生成证书，证书水印不存在。'));
		$back = @imagecreatefrompng($filepath);
		$water = @imagecreatefrompng($cert_seal);
		@imagecopy($back, $water, 530, 360, 0, 0, 100, 100);
		@ImagePng($back,$filepath);
		@imagedestroy($back);
	   	@imagedestroy($water);
		$fileurl	= $UPD_URL.'cert_'.$filename;
		return $fileurl;
	}
    //民族
    public function get_nation($data=0,$do=false) {
		$array=array("0"=>"请选择","1"=>"汉族","2"=>"蒙古族","3"=>"回族","4"=>"藏族","5"=>"维吾尔族","6"=>"苗族","7"=>"彝族","8"=>"壮族","9"=>"布依族","10"=>"朝鲜族","11"=>"满族","12"=>"侗族","13"=>"瑶族","14"=>"白族","15"=>"土家族","16"=>"哈尼族","17"=>"哈萨克族","18"=>"傣族","19"=>"黎族","20"=>"僳僳族","21"=>"佤族","22"=>"畲族","23"=>"高山族","24"=>"拉祜族","25"=>"水族","26"=>"东乡族","27"=>"纳西族","28"=>"景颇族","29"=>"柯尔克孜族","30"=>"土族","31"=>"达斡尔族","32"=>"仫佬族","33"=>"羌族","34"=>"布朗族","35"=>"撒拉族","36"=>"毛南族","37"=>"仡佬族","38"=>"锡伯族","39"=>"阿昌族","40"=>"普米族","41"=>"塔吉克族","42"=>"怒族","43"=>"乌孜别克族","44"=>"俄罗斯族","45"=>"鄂温克族","46"=>"德昂族","47"=>"保安族","48"=>"裕固族","49"=>"京族","50"=>"塔塔尔族","51"=>"独龙族","52"=>"鄂伦春族","53"=>"赫哲族","54"=>"门巴族","55"=>"珞巴族","56"=>"基诺族","57"=>"其他");
		if ($do==true){
			return $array[$data];
		}
		echo $array[$data];
    }	
    //政治面貌
    public function get_politics($data=0,$do=false) {
		$array=array("0"=>"请选择","1"=>"群众","2"=>"团员","3"=>"党员","4"=>"其他党派人士");
		if ($do==true){
			return $array[$data];
		}
		echo $array[$data];
    }	
    //学历
    public function get_eduback($data=0,$do=false) {
		$array=array("0"=>"请选择","1"=>"小学","2"=>"初中","3"=>"高中","4"=>"中专","5"=>"大专","6"=>"本科","7"=>"硕士","8"=>"博士","9"=>"其他");
		if ($do==true){
			return $array[$data];
		}
		echo $array[$data];
    }	
    //休息时间
    public function get_bspace($data=0,$do=false) {
		$array=array("0"=>"请选择","1"=>"平常周一有时间","2"=>"平常周二有时间","3"=>"平常周三有时间","4"=>"平常周四有时间","5"=>"平常周五有时间","6"=>"平常周六有时间","7"=>"平常周日有时间","8"=>"周六、周日有时间","9"=>"平常周末晚上有时间","10"=>"只有晚上有时间","11"=>"周一至周五都有时间","12"=>"工作轮休制不能确","13"=>"任何时候都可以");
		if ($do==true){
			return $array[$data];
		}
		echo $array[$data];
    }
	public function auto_group($vtime=0){
		$vtime=@number_format($vtime);
		if (1>$vtime) return 0;
		$config_file= bAdu_PATH.'config/group.php';
		if (is_file($config_file)){
			$group=require $config_file;
			if (!is_array($group)) $group=module('member')->groupconfig();
		}else{
			$config=model('member')->get_list('`type`=1 and `credit`>0','_group','','credit desc,id desc','id,credit,name');
		}
		foreach ($group as $k=>$v){
			$credit=@number_format($v['credit']);
			if (isset($credit)  && $vtime>=$credit ){
				$data['gid']=$v['id'];
				$data['gname']=$v['name'];
				$data['user_time']=$vtime;
				$data['group_time']=$credit;
				return $data;
			}
		}
	}

}