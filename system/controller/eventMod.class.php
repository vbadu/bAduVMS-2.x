<?php
class eventMod extends commonMod{
	public function __construct(){
        parent::__construct();
		$this->category_left=model('badu')->category(2);
    }
    //公共列表信息
    public function common_list_where(){
    	$where = '1';
    	$where_url = '';
		//类型
        $type=intval(in($_GET['id']));
        if(empty($type)){
			$getid=model('category')->getall(2);
        }else{
			$getid=model('category')->getid($type);
        }
		$count=count($getid);
		if (is_array($getid)){
			foreach( $getid as $v => $vv ){
				$array.=",".$vv['cid'];
			}
		}
		$types=$type.$array;
        $where		.=	' AND type in ('.$types.')';
		$where_url	.=	'id-'.$type;
		//组织
        $bid=intval(in($_GET['bid']));
        if(!empty($bid)){
			//$getbid=model('team')->get_team_id($bid);
        }
		$counts=count($getbid);
		if (is_array($getbid) && $counts>0){
			foreach( $getbid as $vs => $vvv ){
				$arrays.=",".$vvv['id'];
			}
			$bids=$bid.$arrays;
	        //$where		.=	' AND bid in ('.$bids.')';
			$where_url	.=	'bid-'.$bid;
		}
		//日期归档
        $weekday=intval(in($_GET['weekday']));
        if(!empty($weekday) && $weekday>0){
	        $where		.=	' AND weekday='.$weekday;
			$where_url	.=	'-weekday-'.$weekday;
        }
		//所在区域
		$area=in($_GET['area']);
        if(!empty($area)){
			if  (is_array($area) && count($area)>0) $area=implode(',', in(array_filter($area)));
        	$where		.=	' AND area in ('.$area.')';
			$where_url	.=	'-area-'.$area;
        }
		//开始时间
        $stime=in($_GET['stime']);
        if(!empty($stime) && $stime>0){
        	$sstime=strtotime($stime);
	        $where		.=	' AND stime>='.$sstime;
			$where_url	.=	'-stime-'.$stime;
        }
		//结束时间
        $etime=in($_GET['etime']);
        if(!empty($etime) && $etime>0){
        	$setime=strtotime($etime);
	        $where		.=	' AND '.$setime.'>=etime';
			$where_url	.=	'-etime-'.$etime;
        }
        //状态
        $status=intval($_GET['status']);
        switch ($status) {
            case '1':
                $where		.=	' AND zt=1';
                $where_url	.=	'-status-1';
                break;
            case '2':
                $where	.=	' AND zt=2';
                $where_url	.=	'-status-2';
                break;
             case '3':
                $where	.=	' AND zt=3';
                $where_url	.=	'-status-3';
                break;
             default:
                $where	.=	' AND zt>=0';
                $where_url	.=	'-status-0';
                break;
        }
        //搜索
        $key=in(urldecode($_GET['key']));
        if(!is_utf8($key)){
            $search=auto_charset($key);
        }
        if(!empty($key)){
        $where		.=	' AND title like "%' . $key . '%" OR  bname like "%' . $key . '%"';
        $where_url	.=	'-key-'.urlencode($key);
        }

        return array(
            'where'=>$where,
            'url'=>$where_url
            );

    }
    //首页
    public function index(){
		$this->action_name='志愿活动';
        //获取公共条件
        $where=$this->common_list_where();
		//dump($where);
    	$search=$_SERVER["QUERY_STRING"];
    	if (isset($search) && strstr($search, '&')){
	        $this->redirect(__URL__.'/index/'.$where['url'].'.html');
    	}
        $url = __URL__ . '/index/'.$where['url'].'-page-{page}.html';
    	$listRows = 20;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
        $this->event=model('event')->getlist($where['where'],NULL,$limit,$order);
        $count=model('event')->getcount($where['where']);
        $this->assign('page', $this->page($url, $count, $listRows));
		$common['title']=$this->action_name;
        $this->assign('common', $common);
		$this->display('event/index.html');
    }
	public function _empty(){
        $id=intval(in(substr($_GET['_action'],-11)));
        if(empty($id)){
            $this->redirect(__URL__.'/index/');;
        }
		$info = model('event')->getinfo($id);
        if (is_array($info)==false){
         	$this->msg('访问的页面不存在！');
        }
		if (empty($info)) $this->alert('参数传递错误！',0);
		$data=unserialize($info['about']);
		if (is_array($data)){
			$info = array_merge($info,$data);
		}
		$info['area'] = model('badu')->data_to_area($info['area']);
		$this->info=$info;
		//处理报名格式数据
		if (intval($info['bmgs'])>0){$this->form=$form=model('form')->get_form($info['bmgs']);}
		if (is_array($this->form)){
	        foreach (@$form['data'] as $value) {
	            $html.=model('form')->get_field_html($value,$value['default'],$info);  
	        }
        }else{
			$vcard=(!is_array($this->user))?date('Ymdhis').substr(utime(),11,5):$this->user['vcard'];
			$mobile=$this->user['mobile'];
			$html.='<div class="layui-form-item"><label class="layui-form-label">会员编号：</label><div class="layui-input-inline">
						<input id="vcard" name="vcard" type="text" value="'.$vcard.'" class="layui-input" required lay-verify="required" placeholder="请输入会员编号" readonly="readonly" /></div></div>';
			$html.='<div class="layui-form-item"><label class="layui-form-label">手机号码：</label><div class="layui-input-inline">
						<input id="mobile" name="mobile" type="text" value="'.$mobile.'" class="layui-input" required lay-verify="required" placeholder="请输入联系电话" /></div></div>';
			
		}
        $this->baoming = $html;
		$this->online=model('event')->getlist(array('eid'=>$id,'status'=>1),'_team',$limit,$order);
		$this->offline=model('event')->getlist("`eid`={$id} and `status`<>1",'_team',$limit,$order);
		$common['title']=$this->action_name;
        $this->assign('common', $common);
		$this->display('event/info.html');
		
	}
    //活动内容页
    public function go(){
    	$id=intval($_GET[0]);
        if(empty($id)){
            $this->redirect(__URL__.'/index/');;
        }else{
            $this->redirect(__URL__.'/'.$id.'.html#online');;
		}
    }
	//活动报名
    public function baoming(){
    	if ($this->isPost() && is_array($_POST)){
			if (isset($_POST['data']) && count($_POST['data'])>0){
				$_POST=$_POST['data'];
			} 
			$id=in($_POST['eid']);
			foreach($_POST as $k=>$v){
				$post[strtolower(substr($k,-20))]=in($v);
			}
			if (1>count($post) || !isset($post['eid'])){
				return $this->msg('缺失必要的参数，请向统管理员反馈！',0);	
			}
			$id=intval($post['eid']);
			if (1>$id){
				return $this->msg('找不到该活动，请重试！',0);	
			}
			$info = model('event')->getinfo($id);
			if (!is_array($info)){
				return $this->msg('找不到该活动，请重试！',0);	
			}
			//活动状态校对
			if (1>$info['status']){
				if ($nowtime>$info['stime'] && $info['etime']>$nowtime){
					$data['zt']=2;
				}elseif ($nowtime>$info['stime'] && $nowtime>$info['etime']){
					$data['zt']=3;
				}elseif ($info['stime']>$nowtime){
					$data['zt']=1;
				}else{
					$data['zt']=0;
				}
				$where['id']=$id;
				model('event')->edit($data,$where);
				unset($data);
				unset($where);
				return $this->msg('活动停止招募无法报名，请考虑其他活动！',0);	
			}
			$nowtime=time();
			if ($nowtime>$info['stime']){
				$data['status']=0;
				if ($nowtime>$info['stime'] && $info['etime']>$nowtime){
					$data['zt']=2;
				}else{
					$data['zt']=3;
				}
				$where['id']=$id;
				model('event')->edit($data,$where);
				unset($data);
				unset($where);
				return $this->msg('活动已开始无法报名，请考虑其他活动！',0);	
			}
			if ($nowtime>$info['etime']){
				$data['status']=0;
				$data['zt']=3;
				$where['id']=$id;
				model('event')->edit($data,$where);
				unset($data);
				unset($where);
				return $this->msg('活动已结束无法报名，请考虑其他活动！',0);	
			}
			$plus_status=($info['bmgs']>0)?true:false;
			if ($plus_status){
				$form = model('form')->get_form($info['bmgs']);
				if (!is_array($form)){
					return $this->msg('找不到该活动报名配置，请向统管理员反馈！',0);	
				}
			}
			$post['uid']=$this->user['id'];
			//针对特殊字段核验
			if (isset($post['mobile'])&& !is_mob($post['mobile'])){
				return $this->msg('手机号码不正确，请填写后再提交！',0);
			}			
			if (isset($post['email'])&& !is_email($post['email'])){
				return $this->msg('电子邮箱不正确，请填写后再提交！',0);
			}
			if ($plus_status){			
				//参数校对与过滤
				foreach($form['data'] as $v){
					if ($v['must'] && empty($post[$v['field']])){
						return $this->msg('【'.$v['name'].'】必须填写或选择，请检查！',0);
					}
					$data[$v['field']]=model('form')->field_in_check($post[$v['field']],$v);
					//$data[$v['field']]=model('form')->field_in($post[$v['field']],$v['type'],$v['field']);
					if(!isset($post[$v['field']])){
						$data[$v['field']]=$v['default'];
					}
				}
			}else{
				$data['vcard']=in($post['vcard']);
				$data['mobile']=in($post['mobile']);
			}
			if ($info['zmdx']==1){
				if (2>count($this->user) || !is_array($this->user)) return $this->msg('请先登录后再提交报名申请！',0);
			}
			//先查询是否报名过
			if ($this->user['id']>0){
				$where['uid']=$this->user['id'];
			}else{
				$where['tel']=$data['mobile'];
			}
			$where['eid']=$id;
			$event_team=false;
			if (model('event')->getcount($where,'_team')){
				$event_team=true;				
			}
			if ($plus_status){
				if ($this->user['id']>0){
					$where_form['uid']=$this->user['id'];
				}else{
					$where_form['mobile']=$data['mobile'];
				}
				$where_form['eid']=$id;
				if (model('form')->form_count($form['table'],$where_form)){
					return $this->msg($this->user['user'].'您已报名过该活动了，不需要再报名了！',0);
				}else{
					//如果附加表没有记录则清空主表报名记录
					if ($event_team==true) {
						model('event')->del($where,'_team');	
						$event_team=false;
					}
				}
				unset($where_form);
			}
			unset($where);
			if ($event_team==true) return $this->msg($this->user['user'].'您已报名过该活动了，不需要再报名了！',0);	
			
			if ($plus_status){
				//先针对报名表进行登记
				$form_id=model('form')->add($data,$form['table']);
				if (1>$form_id) return $this->msg('数据无法保存，请向系统管理员反馈！',0);	

			}
			//报名表记录生成
			$team['eid']=$id;
			$team['uid']=empty($this->user['id'])?0:$this->user['id'];
			$team['uname']=(empty($team['uid']))?hiddentel($data['mobile']):$this->user['user'];
			$team['vcard']=(empty($team['uid']))?(5>strlen($data['vcard']))?date('Ymdhis').substr(utime(),11,5):$data['vcard']:$this->user['vcard'];
			$team['tel']=$data['mobile'];
			$team['table']=$form['table'];
			$team['fid']=($form_id>0)?$form_id:0;
			$team['vtime']=0;//$info['vtime']
			$team['ip']=get_client_ip();
			$team['dtime']=time();
			//审核记录状态$team['vzt'] 用于登记时数审发
			$team['status']=intval($info['zmbm']);
			model('event')->add($team,'_team');
			//更新报名记录
			$event['bmrs']=intval($info['bmrs'])+1;
			model('event')->edit($event,array('id'=>$id));
			$this->msg('报名成功，请在您的报名通过后按说明参加活动！',1);
        }    	
		$this->redirect(__URL__.'/index/');;
    }

	//我的活动
    public function my(){
		$this->action_name='我的活动';
        $url = __URL__ . '/my/page-{page}';
    	$listRows = 20;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		
		$user=$this->usr();
		$where['uid']=$user['id'];

        $this->list=model('event')->get_list($limit,$where,'_team');
        $count=model('event')->get_count($where,'_team');
        $this->assign('page', $this->page($url, $count, $listRows));
		$this->show();  
    }

}