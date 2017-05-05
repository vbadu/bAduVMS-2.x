<?php
//用户管理
class memberMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
		$this->category_left=model('member')->menuList($_SESSION[$this->config['SPOT'].'_member']);

    }
    // login
    public function login(){
    	if ($this->isPost() && is_array($_POST)){
			if (isset($_POST['data']) && count($_POST['data'])>0){
				$_POST=$_POST['data'];
			} 
			$usr=in($_POST['id']);
			$pwd=in($_POST['pd']);
        	if(empty($usr)||empty($pwd)) {
				$this->msg('登录失败! 帐号信息输入错误!',0);
        	}
        	if(3>strlen($usr)||6>strlen($pwd)) {
				$this->msg('登录失败! 帐号信息输入错误!',0);
        	}
			$type=(is_mob($usr))?'mobile':'vcard';
			
			//获取帐号信息
			$info=model('member')->checkuser($usr,$type,'find');
			//进行帐号验证
			if(empty($info)){
				$info=model('member')->checkuser($usr,'user','find');
				if(empty($info)){
					$this->msg('登录失败! 无此帐号!',0);
				}
			}
			if($info['password']<>md5($pwd.$info['email'])){
				$this->msg('登录失败! 密码错误!',0);
			}
			
			//更新帐号信息
			$data['logintime']=time();
			$data['ip']=get_client_ip();
			$data['loginnum']=intval($info['loginnum'])+1;
			model('member')->edit($data,intval($info['id']));
			//设置登录信息
			$_SESSION[$this->config['SPOT'].'_member']=$info['id'];
			model('member')->current_member(false);
			$this->msg('登录成功！正在进入会员中心！',1);
        }    	
		$common['title']='会员登录';
		$common['cid']=1;
        $this->assign('common', $common);
		$this->display('member/login.html');
    }
    //退出
    public function logout(){
        model('member')->current_member(true,true);
        unset($_SESSION[$this->config['SPOT'].'_member']);
        $this->msg('账号已退出，即将返回登陆 ',__APP__.'/member/login.html');
     }
    //时数查询
    public function vtime() {
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST['vdkey']);
			if (empty($post) || 5>strlen($post)){
				$this->msg('请输入注册编号!',0);
			}
			$where['vcard']=$post;
			$where['status']=1;
			$user=model('member')->info($where);
			if (!is_array($user) || 1>count($user)){
					$this->msg('您输入的注册编号不存在，可能还未注册或未通过审核!',0);
			}
			$this->msg($user['id'],1);
		}
		//dump($_GET);
		$key=intval(in($_GET['vkey']));

		if (($key)>0){
			$where['id']=$key;
			$where['status']=1;
			$user=model('member')->info($where);
			if (!is_array($user) || 1>count($user)){
				$this->msg('您输入的注册编号不存在，可能还未注册或未通过审核!',0);
			}
			$this->event=model('event')->get_list_my($user['id'],'0,10');
		}
		//dump($user);
		$common['title']='服务时数查询';
		$common['cid']=3;
        $this->assign('common', $common);
		$this->assign('info', $user);
		$this->display('member/vtime.html');
    }
    //密码修改
    public function repass() {
		if ($_SESSION[$this->config['SPOT'].'_member']>0) $this->redirect(__URL__.'/index.html');
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST['idkey']);
			if (empty($post) || 6>=strlen($post)){
				$this->msg('请输入密保邮箱或身份证号码!',0);
			}
			if (!is_email($post)){
				$sfz=api_idcard($post);
				if (strlen($sfz['sex'])!=1){
					$this->msg('请输入正确的密保邮箱或身份证号码!',0);
				}else{
					$where['idcard']=$post;	
					$user=model('member')->info($where,'_data');
				}
			}else{
				$where['email']=$post;	
				$user=model('member')->info($where);
			}
			if (!is_array($user) || 1>count($user)){
					$this->msg('您输入的身份凭据不存在，可能还未注册或已更换过其他电邮或身份信息!',0);
			}
			$newpwd=get_password(8);
			$data['password']=md5($newpwd.$user['email']);
			model('member')->edit($data,'id='.$user['id']);
			$this->msg('系统已将您的密码重置为<font color="red">'.$newpwd.'</font>，请尽快登录会员中心修改您的密码。',1);
		}
		$common['title']='密码重置';
		$common['cid']=2;
        $this->assign('common', $common);
		$this->display('member/repass.html');
    }
	public function getAreaJson(){
		$uri= in($_SERVER["REQUEST_URI"]);
		$uri =explode('&amp;',$uri);
		if (@strpos($uri[0], 'getAreaJson', 1)>0){
			$uri[0] = substr($uri[0],20);
		}
		foreach($uri as $k=>$v){
			$params = explode('=',$v);
			$param[$params[0]] = $params[1];
		}

		$id=intval($param['id']);
		$call=in($param['callback']);
		//dump($param);
		$where['show']=1;
		$where['pid']=$id;
		$list=model('badu')->get_list($limit,$where,'area','sort asc,cid asc');
        if(!empty($list)){
            $data=array();
            foreach ($list as $k=> $v) {
				$data[$k]['id']=$v['cid'];
				$data[$k]['value']=$v['name'];
				$data[$k]['parent']=$v['pid'];
				$data[$k]['isleaf']=($v['type']>4)?'true':'false';//isParent
            }
			$json["code"]=200;
			$json["data"]=$data;
        }else{
			$json["code"]=0;
			$json["data"]=array();
		}
		echo $call."(".json_encode($json).")";
	}
	//用户添加
	public function register() {
		if ($_SESSION[$this->config['SPOT'].'_member']>0) $this->redirect(__URL__.'/index.html');
		$step=intval($_SESSION[$this->config['SPOT'].'_member_register_step']);
		$step=($step>0)?$step:1;
		$step_title=($step>1)?'提交保存':'下一步';
		if ($step==1){
			$steps[1]='current';	
			$steps[2]='todo';	
			$steps[3]='todo';	
		}elseif ($step==2){
			$steps[1]='finished';	
			$steps[2]='current';	
			$steps[3]='todo';	
		}else{
			$steps[1]='finished';	
			$steps[2]='finished';	
			$steps[3]='current';
			$id=intval($_SESSION[$this->config['SPOT'].'_member_register']);
			$info=model('member')->infos($id);
			if (!is_array($info) || empty($info)){
				$this->msg('注册超时，请返回重新注册！',0);
				return;
			}
			unset($_SESSION[$this->config['SPOT'].'_member_register_step']);
			unset($_SESSION[$this->config['SPOT'].'_member_register']);
			//dump($info);
		}
		if ($this->isPost() && is_array($_POST)){
			$this->add_save($_POST);
		}
		$this->action_name='注册第一步：填写基本信息';
        $this->action='add';
		$common['title']='注册义工';
		$common['cid']=0;
        $this->assign('common', $common);
        $this->assign('info', $info);
        $this->assign('step', $step);
        $this->assign('steps', $steps);
		$this->display('member/register.html');
	}
	protected function add_save($post) {
		$post=in($post);
		if (isset($post['data']) && count($post['data'])>0){
			$post=$post['data'];
		} 
		$step= intval($post['step']);
		unset($post['step']);
		if ($step==1){
			if (empty($post['nicename']) || 2>strlen($post['nicename'])) {
				$this->msg('昵称未填写或填写不正确！',0);
				return;
			}
			if (empty($post['username']) || 3>strlen($post['username'])) {
				$this->msg('账号未填写或填写不正确！',0);
				return;
			}
			if (empty($post['password']) || 6>strlen($post['password'])) {
				$this->msg('密码未填写或填写不正确！',0);
				return;
			}
			if (empty($post['repassword']) || 6>strlen($post['repassword'])) {
				$this->msg('重复密码未填写或填写不正确！',0);
				return;
			}
			if($post['password']<>$post['repassword']){
				$this->msg('两次密码输入不同！',0);
				return;
			}
			if (6>strlen($post['email']) || is_email($post['email'])==false) {
				$this->msg('电子邮箱未填写或填写不正确！',0);
				return;
			}
			if(model('member')->count(in($post['username']))>0){
				$this->msg('帐号 '.in($post['username']).' 已被注册，请更换其他用户名',0);
				return;
			}
			if(model('member')->count(in($post['email']),'','email')>0){
				$this->msg('电子邮箱 '.in($post['email']).' 已被注册，您可以试着通过【重置密码】连接找回登陆账号和密码。',0);
				return;
			}
			$data['nicename']=in($post['nicename']);
			$data['user']=in($post['username']);
			$data['password']=md5($post['password'].$post['email']);
			$data['email']=in($post['email']);
			$data['dtime']=time();
			$data['ip']=get_client_ip();
			$data['status']=0;
			$data['type']=1;
			$data['gid']=1;
			$data['gname']='注册用户组';
			//录入模型处理
			$id=model('member')->add($data);
			unset($data);
			$_SESSION[$this->config['SPOT'].'_member_register']=$id;
			$_SESSION[$this->config['SPOT'].'_member_register_step']=2;
			$this->msg(__URL__.'/register.html?step=next',1);
			
		}elseif ($step==2){
			$id=intval($_SESSION[$this->config['SPOT'].'_member_register']);
			if (1>$id) return $this->msg('注册超时，请重新提交注册。如您完成第一步请登录后继续完成注册！',0);
			if (empty($post['realname']) || 3>strlen($post['realname'])) {
				$this->msg('真实姓名未填写或填写不正确！',0);
				return;
			}
			if (empty($post['idcard']) || 15>strlen($post['idcard'])) {
				$this->msg('身份证号未填写或填写不正确！',0);
				return;
			}
			if(model('member')->count(in($post['idcard']),'','idcard','_data')>3){
				$this->msg('身份证号码 '.in($post['idcard']).' 已被注册，您可以试着通过【重置密码】连接找回登陆账号和密码。',0);
				return;
			}
			$idcard=api_idcard($post['idcard']);
			if (!is_array($idcard) || 6>strlen($idcard['birthday']) || 4>strlen($idcard['area'])) {
				$this->msg('身份证号认证未通过，请重试！',0);
				return;
			}
			$idcard['sex']=($idcard['sex']=='M')?1:2;
			if (empty($post['nation']) || 1>intval($post['nation'])) {
				$this->msg('民族未选择或选择不正确！',0);
				return;
			}
			if (empty($post['politics']) || 1>intval($post['politics'])) {
				$this->msg('政治面貌未选择或选择不正确！',0);
				return;
			}
			if (empty($post['eduback']) || 1>intval($post['eduback'])) {
				$this->msg('学历未选择或选择不正确！',0);
				return;
			}
			if (empty($post['major']) || 3>strlen($post['major'])) {
				$this->msg('专业未填写或填写不正确！',0);
				return;
			}
			if (empty($post['job']) || 3>strlen($post['job'])) {
				$this->msg('职业未填写或填写不正确！',0);
				return;
			}
			if (empty($post['organization']) || 5>strlen($post['organization'])) {
				$this->msg('所在单位未填写或填写不正确！',0);
				return;
			}
			if (empty($post['mobile']) || 11>strlen($post['mobile']) || !is_mob($post['mobile'])) {
				$this->msg('手机号码未填写或填写不正确！',0);
				return;
			}
			$post['area']=$idcard['area'];
			if (empty($post['address']) || 5>strlen($post['address'])) {
				$this->msg('联系地址未填写或填写不正确！',0);
				return;
			}
			if (empty($post['experience']) || 1>intval($post['experience'])) {
				$this->msg('志愿经历未选择或选择不正确！',0);
				return;
			}
			if (empty($post['ability']) || 1>intval($post['ability'])) {
				$this->msg('组织能力未选择或选择不正确！',0);
				return;
			}
			if (empty($post['bspace']) || 1>intval($post['bspace'])) {
				//$this->msg('可参与时间未选择或选择不正确！',0);
				//return;
			}
			//先更新注册表记录值
			$data['realname']=in($post['realname']);
			$data['sex']=($idcard['sex']==$post['sex'])?$post['sex']:$idcard['sex'];
			$data['mobile']=in($post['mobile']);
			model('member')->edit($data,$id);
			unset($data);
			//然后插入附加数据
			$data['mid']=$id;
			$data['idcard']=in($post['idcard']);
			$data['nation']=intval($post['nation']);
			$data['politics']=intval($post['politics']);
			$data['eduback']=intval($post['eduback']);
			$data['major']=in($post['major']);
			$data['job']=in($post['job']);
			$data['organization']=in($post['organization']);
			$data['qq']=in($post['qq']);
			$data['weixin']=in($post['weixin']);
			$data['tel']=in($post['tel']);
			$data['area']=in($post['area']);
			$data['address']=in($post['address']);
			$data['experience']=intval($post['experience']);
			$data['ability']=intval($post['ability']);
			$data['bspace']=intval($post['bspace']);
			$data['birthday']=$idcard['birthday'];
			$data['place']=$idcard['address'];
			$data['about']=in($post['about']);
			//录入模型处理
			model('member')->add($data,'_data');
			$_SESSION[$this->config['SPOT'].'_member_register']=$id;
			$_SESSION[$this->config['SPOT'].'_member_register_step']=3;
			$this->msg(__URL__.'/register.html?step=next',1);
		}				
		die;
	}
	//会员首页
	public function index() {
		$this->check_id();
		$limit='0,5';
		$user=model('member')->current_member();
		$this->event=model('event')->get_list_my($user['id'],'0,20');
		//$this->msg=model('helper')->get_msg_list($user['id'],$limit);
		$common['title']='我的首页';
		$common['cid']=11;
		$this->recount();
        $this->assign('common', $common);
        $this->assign('user', $user);
		$this->display('member/index.html');
	}
    //用户修改
    public function profile() {
		$this->check_id();
        $id=$_SESSION[$this->config['SPOT'].'_member'];
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST);
			if (isset($post['data']) && count($post['data'])>0){
				$post=$post['data'];
			} 
			if (empty($post['nicename']) || 2>strlen($post['nicename'])) {
				$this->msg('昵称未填写或填写不正确！',0);
				return;
			}
			if(model('member')->count(in($post['nicename']),$id,'nicename')>0){
				$this->msg('昵称 '.in($post['nicename']).' 已被占用，请更换其他昵称',0);
				return;
			}
			if (empty($post['nation']) || 1>intval($post['nation'])) {
				$this->msg('民族未选择或选择不正确！',0);
				return;
			}
			if (empty($post['politics']) || 1>intval($post['politics'])) {
				$this->msg('政治面貌未选择或选择不正确！',0);
				return;
			}
			if (empty($post['idcard']) || 15>strlen($post['idcard'])) {
				$this->msg('身份证号未填写或填写不正确！',0);
				return;
			}
			if(model('member')->count(in($post['idcard']),$id,'idcard','_data')>0){
				$this->msg('身份证号码 '.in($post['idcard']).' 已被注册，请检查填写是否正确。',0);
				return;
			}
			$idcard=api_idcard($post['idcard']);
			//dump($idcard);
			if (!is_array($idcard) || 6>strlen($idcard['birthday']) || 4>strlen($idcard['area'])) {
				$this->msg('身份证号填写不正确！',0);
				return;
			}
			$idcard['sex']=($idcard['sex']=='M')?1:2;
			if (empty($post['eduback']) || 1>intval($post['eduback'])) {
				$this->msg('学历未选择或选择不正确！',0);
				return;
			}
			if (empty($post['major']) || 3>strlen($post['major'])) {
				$this->msg('专业未填写或填写不正确！',0);
				return;
			}
			if (empty($post['job']) || 3>strlen($post['job'])) {
				$this->msg('职业未填写或填写不正确！',0);
				return;
			}
			if (empty($post['organization']) || 5>strlen($post['organization'])) {
				$this->msg('所在单位未填写或填写不正确！',0);
				return;
			}
			if (empty($post['experience']) || 1>intval($post['experience'])) {
				$this->msg('志愿经历未选择或选择不正确！',0);
				return;
			}
			if (empty($post['ability']) || 1>intval($post['ability'])) {
				$this->msg('组织能力未选择或选择不正确！',0);
				return;
			}
			if (empty($post['bspace']) || 1>intval($post['bspace'])) {
				$this->msg('可参与时间未选择或选择不正确！',0);
				return;
			}
			//先更新主表
			$data['nicename']=in($post['nicename']);
			$data['sex']=$idcard['sex'];
			model('member')->edit($data,$id);
			unset($data);
			//然后更新附加数据
			$where['mid']=$id;
			$data['idcard']=$post['idcard'];
			$data['nation']=intval($post['nation']);
			$data['politics']=intval($post['politics']);
			$data['eduback']=intval($post['eduback']);
			$data['major']=in($post['major']);
			$data['job']=in($post['job']);
			$data['organization']=in($post['organization']);
			$data['experience']=intval($post['experience']);
			$data['ability']=intval($post['ability']);
			$data['bspace']=intval($post['bspace']);
			$data['birthday']=$idcard['birthday'];
			$data['place']=$idcard['address'];
			$data['about']=in($post['about']);
			model('member')->edit($data,$where,'_data');
			unset($post);
			unset($data);
			$this->msg('修改成功!',1);
		}

		$user=model('member')->infos($id);
		$common['title']='我的资料';
		$common['cid']=12;
        $this->assign('common', $common);
        $this->assign('info', $user);
		$this->display('member/profile.html');
    }
    //用户修改
    public function avatar() {
		$this->check_id();
        $id=$_SESSION[$this->config['SPOT'].'_member'];
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST);
			if (empty($post['avatar']) || !is_array($post['avatar'])) {
				$this->msg('上传文件不能为空！',0);
				return;
			}
			if (empty($post['size']) || 500 < round(intval($post['size']) / pow(1024, 1), 2)) {
				$this->msg('您选择的头像文件尺寸超过500KB，请重新选择上传。',0);
				return;
			}
			list($photo_path, $rename) = model('badu')->set_photo_path($id);
			$avatar = DIRECTORY_SEPARATOR."avatar".DIRECTORY_SEPARATOR . $photo_path;
			bulid_dir(__UPDDIR__ .$avatar);
			$upload = __UPDDIR__.$avatar;
			$new_file = $upload . "/" . $rename . ".jpg";
			$base64_image_content = in($post['avatar']);
			$base64_image_content = implode("/", $base64_image_content);
			if (preg_match("/^(data:\s*image\/(\w+);base64,)/", $base64_image_content, $result)) {
				$type = $result[2];
				$base64_image_content = str_replace($result[1], "", $base64_image_content);
				$image_content = base64_decode($base64_image_content);

				if (file_put_contents($new_file, $image_content)) {
					$old_thumbs = glob($upload . "/*_" . $rename . ".jpg");

					if (!empty($old_thumbs)) {
						foreach ($old_thumbs as $v ) {
							@unlink($v);
						}
					}
					$data['image']=1;
					model('member')->edit($data,$id);
					$this->msg('修改成功!',1);
				}
			}
			$this->msg('发生不可预知的错误，如果方便请通知管理员。',0);
		}
		$common['title']='修改我的头像';
		$common['cid']=13;
        $this->assign('common', $common);
		$this->display('member/avatar.html');
    }
    //用户修改
    public function contact() {
		$this->check_id();
        $id=$_SESSION[$this->config['SPOT'].'_member'];
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST);
			if (isset($post['data']) && count($post['data'])>0){
				$post=$post['data'];
			} 
			if (empty($post['mobile']) || 11>strlen($post['mobile']) || !is_mob($post['mobile'])) {
				$this->msg('手机号码未填写或填写不正确！',0);
				return;
			}
			if (is_array($post['area'])){
				foreach ($post['area'] as $k=> $v) {
					if (empty($v) || 1>intval($v)){
						$this->msg('所在区域未完整选择，请补选！',0);
						return;
					}
					$area[$k]=intval($v);
				}
				$area=implode(',',$area);
			}else{
				$area=$post['area'];
			}
			if (empty($post['address']) || 5>strlen($post['address'])) {
				$this->msg('联系地址未填写或填写不正确！',0);
				return;
			}
			$data['mobile']=in($post['mobile']);
			model('member')->edit($data,$id);
			unset($data['mobile']);
			//然后更新附加数据
			$where['mid']=$id;
			$data['qq']=in($post['qq']);
			$data['weixin']=in($post['weixin']);
			$data['tel']=in($post['tel']);
			$data['area']=$area;
			$data['address']=in($post['address']);
			model('member')->edit($data,$where,'_data');
			unset($post);
			unset($data);
			$this->msg('修改成功!',1);
		}
		$user=model('member')->infos($id);
		if (empty($user['area'])) $user['area']='17, 240, 2038';
		//dump($user);
        //$this->data=unserialize($this->info['about']);
		$common['title']='我的联系方式';
		$common['cid']=14;
        $this->assign('common', $common);
        $this->assign('info', $user);
		$this->display('member/contact.html');
    }
    //用户修改
    public function repwd() {
		$this->check_id();
        $id=$_SESSION[$this->config['SPOT'].'_member'];
		if ($this->isPost() && is_array($_POST)){
			$post=in($_POST);
			if (isset($post['data']) && count($post['data'])>0){
				$post=$post['data'];
			} 
			if (empty($post['oldpassword']) || 5>strlen($post['oldpassword'])) {
				$this->msg('原密码未填写或填写不正确！',0);
				return;
			}
			if (empty($post['password']) || 6>strlen($post['repassword'])) {
				$this->msg('新密码未填写或填写不正确！',0);
				return;
			}
			if (empty($post['repassword']) || 6>strlen($post['repassword'])) {
				$this->msg('重复新密码未填写或填写不正确！',0);
				return;
			}
			if(strlen($post['repassword'])>6 && $post['password']<>$post['repassword']){
				$this->msg('两次新密码输入不同！',0);
				return;
			}
			if (6>strlen($post['email']) || is_email($post['email'])==false) {
				$this->msg('电子邮箱未填写或填写不正确！',0);
				return;
			}
			if(model('member')->count(in($post['email']),$id,'email')>0){
				$this->msg('电子邮箱 '.in($post['email']).' 已被注册，请重新输入。',0);
				return;
			}
			$info=model('member')->info('`id`='.$id,'','email,password');
			if(md5($post['oldpassword'].$info['email'])<>$info['password']){
				$this->msg('原密码不正确，请重新输入！',0);
				return;
			}
			if ($info['email']!=in($post['email'])){
				$data['email']=in($post['email']);
				$pwd=md5($post['password'].$data['email']);
			}else{
				$pwd=md5($post['password'].$info['email']);
			}
			$data['password']=$pwd;
			model('member')->edit($data,$id);
			unset($data);
			unset($post);
			$this->msg('修改成功!',1);
		}
		$common['title']='修改我的密码';
		$common['cid']=15;
		$user=model('member')->infos($id);
        $this->assign('info', $user);
        $this->assign('common', $common);
		$this->display('member/repwd.html');
    }
    //用户证书
    public function cert() {
		$id=intval(in($_GET['key']));
		$type=false;
		$common['title']='志愿者证书';
		$common['cid']=3;
		if (1>($id)){
			$this->check_id();
			$id=$_SESSION[$this->config['SPOT'].'_member'];
			$type=true;
			$common['title']='我的证书';
			$common['cid']=17;
		}
		$where="`status`=1 and `gid`>1 and `id`=".$id;
		$user=model('member')->info($where);
		$this->user=model('member')->current_member(false);
		if (!is_array($user) || 1>count($user)){
			$this->msg('无法生成电子证书，可能未通过审核或还不是正式义工!',0);
		}
		$user['cert']=model('member')->markcert($user,$type);
		if (is_array($user['cert'])) $user['msg'] = $user['cert'][1];
        $this->assign('common', $common);
        $this->assign('info', $user);
		$this->display('member/cert.html');
    }
	//活动
	public function event() {
		$this->check_id();
		if ($this->isPost() && is_array($_POST)){
			$this->eventset($_POST);	
		}
		$common['title']='我的活动';
		$common['cid']=16;
		$id=$_SESSION[$this->config['SPOT'].'_member'];
		$zt=intval(in($_GET['status']));
		if ($zt>0) $status="-status-".$zt;
        $url = __URL__ . "/event/index-{page}{$status}.html";
        $listRows=20;
        $limit=$this->pagelimit($url,$listRows);
        //$order='type asc,status asc,id desc';
        //列表
		$this->event=model('event')->get_list_my($id,$limit,$zt);
        $count=model('event')->get_count_my($id,$zt);
		$style['first']='prev';
		$style['pre']='prev';
		$style['now']='';
		$style['now_link']='active';
		$style['next']='next';
		$style['last']='next';
        $this->page=$this->page($url, $count, $listRows,0,5,6,$style,'li');

		//dump($this->event);
        $this->assign('common', $common);
        $this->assign('navzt', $zt);
		$this->display('member/event.html');
	}

	//公用调用
    protected function eventset($post){
		$zt=intval(in($post['status']));
		$id=intval(in($post['id']));
		$mid=$_SESSION[$this->config['SPOT'].'_member'];
		if (!empty($id) && $id>0){//取消申请
			$info = model('event')->check_event($id);
			if(1>count($info)){
				$this->msg('该活动已下架，请与管理员联系！',0);
			}
		}else{
			$this->msg('该活动已下架，请与管理员联系！',0);
		}
		if ($zt==6 && !empty($mid) && is_array($info)){//取消申请
			model('event')->edit($data,array('uid'=>$mid,'eid'=>$id),'_team');
			$data['bmrs']=intval($info['bmrs'])-1;
			model('event')->edit($data,'id='.$id);
			unset($data);
			$this->msg('撤销报名操作成功！',1);
		}elseif ($zt==3 && !empty($mid) && is_array($info)){//请假
			$data['status']=3;
			model('event')->edit($data,array('uid'=>$mid,'eid'=>$id),'_team');
			unset($data);
			$this->msg('请假操作成功！',1);
		}elseif ($zt==0 && !empty($mid) && is_array($info)){//重新申请
			$data['bmrs']=intval($info['bmrs'])-1;
			model('event')->edit($data,array('id'=>$id));
			unset($data);
			$data['status']=0;
			model('event')->edit($data,array('uid'=>$mid,'eid'=>$id),'_team');
			unset($data);
			$this->msg('重新申请操作成功！',1);
		}
	}
	public function recount(){
		$member=$this->check_id();
		if ($_COOKIE[$member['id']."_member_update_".date('Y-m-d')]){
			return false;
		}
		$alltime=model('event')->get_sum($member['id'],'_team');
		$alltime=@number_format($alltime);
		$btime=@number_format($member['btime']);
		$mdata['vtime']=($btime+$alltime);
		if ($mdata['vtime']>0){
			$data=model('member')->auto_group($mdata['vtime']);
			$mdata['gid']=$data['gid'];
			$mdata['gname']=$data['gname'];
			if (1>$mdata['gid']) unset($mdata['gid']);
		}
		if ($mdata['gid']>1){
			model('member')->edit($mdata,array("id"=>$member['id']));
		}
		unset($mdata);
		setcookie($member['id']."_member_update_".date('Y-m-d'),$member['id'],time()+24*3600);
		return true;
	}

}