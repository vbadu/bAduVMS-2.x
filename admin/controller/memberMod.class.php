<?php
//用户管理
class memberMod extends commonMod {
	public function __construct(){
        parent::__construct();
    }
	//会员首页
	public function index() {
		$this->action_name='个人会员';
        $url = __URL__ . '/index/page-{page}';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		$where['type']=1;
        $this->list=model('member')->get_list($table,$where,$limit);
        /*
        foreach ($this->list as $k=>$v){
        	if (empty($v['dtime'])){
        	$_v=model('badu')->find('member_data',array('mid'=>$v['id']));
        	$v=array_merge($v, $_v);
        	dump($v);
		    $data['gid']=2;
		    $data['keep']=0;
		    $data['gname']="正式义工会员";
		    $temp = explode(',','女,男');
			$temp=array_keys($temp,$v['image'],true);
			$data['sex']=$temp[0];
			$data['image']='';
		    $data['dtime']=strtotime($v['gname']);
		    model('badu')->set_data(array('id'=>$v['id']),$data,'member');
		    //dump($data);
		    unset($data);
			$temp = explode(',','请选择,汉,蒙古,回,藏,维吾尔,苗,彝,壮,布依,朝鲜,满,侗,瑶,白,土家,哈尼,哈萨克,傣,黎,傈僳,佤,畲,高山,拉祜,水,东乡,纳西,景颇,柯尔克孜,土,达斡尔,仫佬,羌,布郎,撒拉,毛南,仡佬,锡伯,阿昌,普米,塔吉克,怒,乌孜别克,俄罗斯,鄂温克,德昂,保安,裕固,京,塔塔尔,独龙,鄂伦春,赫哲,门巴,珞巴,基诺,入籍,其他');
			$temp=array_keys($temp,$v['vipmz'],true);
			$data['nation']=$temp[0];
			$temp = explode(',','请选择,小学,初中,高中,中专,大专,本科,硕士,博士,其他');
			$temp=array_keys($temp,$v['vipxl'],true);
			$data['eduback']=$temp[0];
			$temp = explode(',','请选择,周一,周二,周三,周四,周五,周六,周日,双休,周末晚上,每天晚上,工作日,不确定,都有时间');
			$temp=array_keys($temp,$v['vipxx'],true);
			$data['bspace']=$temp[0];
			$temp = explode(',','没有,有过');
			$temp=array_keys($temp,$v['vipfw'],true);
			$data['experience']=$temp[0];
			$temp = explode(',','没有,有过,具备');
			$temp=array_keys($temp,$v['vipabout'],true);
			$data['ability']=$temp[0];
			model('badu')->set_data(array('mid'=>$v['id']),$data,'member_data');
		    //dump($data);
		    unset($data);
		    }
        }
		*/
        $count=model('member')->get_count($table,$where);
        $this->assign('page', $this->page($url, $count, $listRows));
		$this->show();  
	}
	//团体会员
	public function team() {
		$this->action_name='团体会员';
        $url = __URL__ . '/team/page-{page}';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		$where['type']=2;
        $this->list=model('member')->get_list($table,$where,$limit);
        $count=model('member')->get_count($table,$where);
        $this->assign('page', $this->page($url, $count, $listRows));
		$this->show();  
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
		$list=model('badu')->select($where,'area',$limit,'sort asc,cid asc');
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
    //用户修改
    public function edit() {
        $this->action_name='资料修改';
		if (!empty($_POST) && is_array($_POST)){
			$id=intval($_POST['uid']);
			$post=in($_POST['info']);
			$info['user']=in($post['user']);
			$info['vcard']=in($post['vcard']);
			$info['nicename']=in($post['nicename']);
			$info['realname']=in($post['realname']);
			$info['sex']=intval($post['sex']);
			$info['mobile']=in($post['mob']);
			$info['email']=in($post['email']);
			$info['btime']=intval($post['btime']);
			$info['status']=intval($post['status']);
			if (strlen($post['password'])>5){
				$info['password']=md5(in($post['password']).$info['email']);
			}
			$info['gid']=intval($post['gid']);
			$group=model('member')->info('id='.$info['gid'],'_group');
			$info['gname']=$group['name'];
			unset($post);
			$post=in($_POST['data']);
			$data['idcard']=in($post['idcard']);
			$data['qq']=in($post['qq']);
			$data['weixin']=in($post['weixin']);
			$data['tel']=in($post['tel']);
			$data['address']=in($post['address']);
			$data['major']=in($post['major']);
			$data['job']=in($post['job']);
			$data['organization']=in($post['organization']);
			$data['nation']=intval($post['nation']);
			$data['politics']=intval($post['politics']);
			$data['eduback']=intval($post['eduback']);
			$data['bspace']=intval($post['bspace']);
			$data['experience']=intval($post['experience']);
			$data['ability']=intval($post['ability']);
			$data['about']=in($post['about']);
			if (!is_mob($info['mobile'])){
				$this->msg('操作失败，手机号码不正确，请检查后提交!',0);
			}
			if (!is_email($info['email'])){
				$this->msg('操作失败，电子邮箱不正确，请检查后提交!',0);
			}
			if (empty($id)==true) {
				if (model('member')->count("`user`='{$info['user']}'")>0){
					$this->msg('操作失败，存在同名用户名，请更换!',0);
				}
				if (model('member')->count("`mobile`='{$info['mobile']}'")>0){
					$this->msg('操作失败，存在相同手机号码注册用户，请检查后提交!',0);
				}
				if (model('member')->count("`email`='{$info['email']}'")>0){
					$this->msg('操作失败，存在相同电子邮件注册用户，请检查后提交!',0);
				}
				if (!empty($data['idcard']) && model('member')->count("`idcard`='{$data['idcard']}'",'','_data')>0){
					$this->msg('操作失败，存在相同身份证号注册用户，请检查后提交!',0);
				}
				$info['dtime']=$info['logintime']=time();
				$info['loginnum']=$info['keep']=0;
				$info['ip']=get_client_ip();
				$info['type']=1;
				$data['mid']=model('member')->add($info);
				model('member')->add($data,'_data');
			}else{
				if (model('member')->count("`user`='{$info['user']}'",$id)>0){
					$this->msg('操作失败，存在同名用户名，请更换!',0);
				}
				if (model('member')->count("`mobile`='{$info['mobile']}'",$id)>0){
					$this->msg('操作失败，存在相同手机号码注册用户，请检查后提交!',0);
				}
				if (model('member')->count("`email`='{$info['email']}'",$id)>0){
					$this->msg('操作失败，存在相同电子邮件注册用户，请检查后提交!',0);
				}
				if (!empty($data['idcard']) && model('member')->count("`idcard`='{$data['idcard']}'",$id,'_data')>0){
					$this->msg('操作失败，存在相同身份证号注册用户，请检查后提交!',0);
				}
				model('member')->edit($info,'id='.$id);
				model('member')->edit($data,'mid='.$id,'_data');
			}
			$this->msg('保存成功!');
		}
    	$id=intval(in($_GET['id']));
		if (empty($id)) {
			$this->action_name='添加个人会员';
				$user['dtime']=$user['logintime']=time();
				$user['loginnum']=$user['btime']=$user['vtime']=0;
				$user['ip']=get_client_ip();
		}else{
	        $this->alert_str($id,'int');
			$this->action_name='编辑个人会员';
			$user = model('member')->infos($id);
			if (empty($user)) $this->alert('参数传递错误！',0);
			$info = model('member')->info('mid='.$id,'_data');
			$data['mid']=$id;
			if (empty($info)) model('member')->add($data,'_data');;
		}
		$this->info=$user;
		$this->group=model('member')->get_list('_group','type!=1');
        //dump($this->info);
		$this->show();
    }
    //用户修改
    public function edit_team() {
        $this->action_name='资料修改';
		if (!empty($_POST) && is_array($_POST)){
			$id=intval(in($_POST['id']));
			$data['user']=in($_POST['user']);
			$data['vcard']=in($_POST['vcard']);
			$data['nicename']=in($_POST['nicename']);
			$data['name']=in($_POST['name']);
			$data['image']=in($_POST['image']);
			$data['idcard']=in($_POST['idcard']);
			$data['sex']=intval(in($_POST['sex']));
			$data['tel']=in($_POST['tel']);
			$data['mob']=in($_POST['mob']);
			$data['email']=in($_POST['email']);
			$data['address']=in($_POST['address']);
			$data['oid']=intval(in($_POST['oid']));
			$data['area']=intval(in($_POST['area']));
			$data['gid']=intval(in($_POST['gid']));
			$data['status']=intval(in($_POST['status']));
			$data['btime']=intval(in($_POST['btime']));
			$data['password']=md5($_POST['password'].$data['email']);
			$datas['about']=serialize(in($_POST['info']));
			$group=model('member')->info('id='.$data['gid'],'_group');
			$data['gname']=$group['name'];
			if (empty($id)==true) {
				$data['dtime']=$data['logintime']=time();
				$data['loginnum']=$data['keep']=0;
				$data['ip']=get_client_ip();
				$data['type']=2;
				$datas['mid']=model('member')->add($data);
				model('member')->add($datas,'_data');
			}else{
				model('member')->edit($data,'id='.$id);
				model('member')->edit($datas,'mid='.$id,'_data');
			}
			$this->msg('保存成功!');
		}
    	$id=intval(in($_GET['id']));
		if (empty($id)==true) {
			$this->action_name='添加团体会员';
				$user['dtime']=$user['logintime']=time();
				$user['loginnum']=$user['btime']=$user['vtime']=0;
				$user['ip']=get_client_ip();
		}else{
	        $this->alert_str($id,'int');
			$this->action_name='编辑团体会员';
			$user = model('member')->info('id='.$id);
			if (empty($user)) $this->alert('参数传递错误！',0);
			$info = model('member')->info('mid='.$id,'_data');
			$data['mid']=$id;
			if (empty($info)) model('member')->add($data,'_data');;
		}
		$this->user=$user;
		$this->group=model('member')->get_list('_group','type!=1');
		$this->team=model('team')->tree_list();
		$this->area = model('badu')->get_area();
        $this->info=unserialize($info['about']);
        //dump($this->info);
		$this->show();
    }

	//会员组管理
    public function group(){
		$this->action_name='会员组';
        $url = __URL__ . '/group/page-{page}';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		$this->list=model('member')->get_list('_group',$where,$limit);
        $count=model('member')->get_count('_group',$where);
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->show();
    }
	//添加编辑人员信息
    public function edit_group(){
		$power=array();
		if (!empty($_POST) && is_array($_POST)){
			$id=intval(in($_POST['id']));
			$data['name']=in($_POST['name']);
			$data['type']=intval(in($_POST['type']));
			$data['credit']=intval(in($_POST['credit']));
			$data['icon']=in($_POST['icon']);
			$data['power']=serialize(in($_POST['power']));
			if (empty($id)==true) {
				model('member')->add($data,'_group');
			}else{
				model('member')->edit($data,'id='.$id.'','_group');
				$datas['gname']=$data['name'];
				model('member')->edit($datas,'gid='.$id);
			}
			$this->msg('保存成功!');
		}
    	$id=intval(in($_GET['id']));
		if (empty($id)==true) {
			$this->action_name='创建会员组';
			$info['credit']= 0;
			$info['type']=0;
			$info['icon']='0.gif';
		}else{
			$this->action_name='编辑会员组';
			$info = model('member')->info('id='.$id,'_group');
			if (empty($info)) $this->alert('参数传递错误！',0);
			$power = unserialize($info['power']);
		}
		$this->assign('info', $info);
		$this->assign('power', $power);
		$this->show();
    }
	// 公共调用
    public function set(){
		if (!empty($_POST) && is_array($_POST)){
			$id=intval(in($_POST['id']));
			$zt=intval(in($_POST['zt']));
			$vcard=in($_POST['vcard']);
			if (empty($zt) && !empty($id)){//删除
				model('member')->del('id='.$id);
				model('member')->del('mid='.$id,'_data');
				$this->msg('会员删除成功！',1);
			}elseif ($zt==1 && !empty($id)){
				$data['status']=1;
				$data['gid']=2;
				$group=model('member')->info('id=2','_group');
				$data['gname']=$group['name'];
				$data['vcard']=model('member')->vcard($vcard,$id,0);
				model('member')->edit($data,'id='.$id);
				$this->msg('会员状态修改成功！',1);
			}elseif ($zt==2 && !empty($id)){
				$data['status']=0;
				$data['gid']=1;
				$group=model('member')->info('id=1','_group');
				$data['gname']=$group['name'];
				model('member')->edit($data,'id='.$id);
				$this->msg('会员状态修改成功！',1);
			}elseif ($zt==3 && !empty($id)){
				$data['oid']=1;
				model('member')->edit($data,'id='.$id);
				$this->msg('会员已从原有团队剔除！'.$data['status'],1);
			}
		}
		$this->msg('nothing！',0);
    }
	// 编号规则
	public function vcard($vcard=''){
		
		
	}
	public function recount(){
		$this->action_name='个人会员';
		@header("Content-type: text/html; charset=utf-8");
		$next=intval($_GET['next']);
        $url = __URL__ . '/recount/';
		if ($next>0){
			$listRows = 1000;
			$limit_start = ($next - 1) * $listRows;
			$limit_start = (0>$limit_start)?0:$limit_start;
			$limit = $limit_start . ',' . $listRows;
			$where="`type`=1 and `status`=1 and `gid`>1";
			$count=model('member')->get_count($table,$where);
			if ($count>0){
				$html="当前总数{$count}";
				$list=model('member')->get_list($table,$where,$limit);
				if (is_array($list)){
					foreach ($list as $k=>$v){
						if ($v['id']>0){
							$alltime=model('event')->get_sum($v['id'],'_team');
							$alltime=@number_format($alltime);
							$btime=@number_format($v['btime']);
							$mdata['vtime']=($btime+$alltime);
							if ($mdata['vtime']>0){
								$data=model('member')->auto_group($mdata['vtime']);
								$mdata['gid']=$data['gid'];
								$mdata['gname']=$data['gname'];
								if (1>$mdata['gid']) unset($mdata['gid']);
							}
							if ($mdata['gid']>1){
								model('member')->edit($mdata,array("id"=>$v['id']));
							}
							unset($mdata);
						}
					}				
					$now=(($next)*$listRows);
					$tips=$now;
					if ($now>$count) { $tips=$count;}
					$html.=",已更新完成{$tips}位会员信息。";
					if ($count>$now) {
						$url = __URL__ . '/recount/next-'.($next+1);
						$this->redirect($url);
					}
				}else{
					$html.=",已更新完成。";
				}
				
			}else{
				$html.="没有可更新的会员。";
			}
		}else{
			$html.="更新速度根据系统中注册的用户量及服务器性能来决定，一般而言用户量超过1万更新耗时会相对较长，建议在深夜执行更新任务。";
			$this->btn="<form action='".__URL__."/recount/next-1' method='get'><input name='sumbit' type='submit' value='开始更新' /></form>";
		}
        $this->assign('url', $url);
        $this->assign('html', $html);
		$this->show();  
		
	}

}