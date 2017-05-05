<?php
class eventMod extends commonMod{
	public function __construct(){
        parent::__construct();
        if(!model('user_group')->menu_power('event',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
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
		$where_url	.=	'type-'.$type;
		//组织
        $bid=intval(in($_GET['bid']));
        if(!empty($bid)){
			$getbid=model('team')->get_team_id($bid);
        }
		$counts=count($getbid);
		if (is_array($getbid) && $counts>0){
			foreach( $getbid as $vs => $vvv ){
				$arrays.=",".$vvv['id'];
			}
			$bids=$bid.$arrays;
	        $where		.=	' AND bid in ('.$bids.')';
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
                $where		.=	' AND status=1';
                $where_url	.=	'-status-1';
                break;
            case '2':
                $where	.=	' AND status=0';
                $where_url	.=	'-status-2';
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
        //获取公共条件
        $where=$this->common_list_where();
		//dump($where);
    	$search=$_SERVER["QUERY_STRING"];
    	if (isset($search) && strstr($search, '&')){
	        $this->redirect(__URL__.'/index/'.$where['url'].'.html');
    	}
		$this->action_name='活动';
    	$type=intval($_GET['type']);
        if(empty($type)) $type=0;
		$this->type=$type;
		
		//$this->area = model('badu')->get_area();
        $url = __URL__ . '/index/'.$where['url'].'-page-{page}.html';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
        $this->list=model('event')->get_list($where['where'],NULL,$limit);
		$count=model('event')->get_count($where['where']);
        $this->assign('page', $this->page($url, $count, $listRows));
		$this->show();
    }
	//活动审批
    public function team(){
		$this->action_name='报名管理';
    	$id=intval(in($_GET['id']));
    	$info=model('event')->info('id='.$id);
        if(empty($id) && !is_array($info)){
            $this->alert('参数传递错误！',0);
        }
        $this->info=$info;
        $url = __URL__ . '/team/id-'.$id.'-index-{page}';
    	$listRows = 10;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		//dump($info);
		$this->type=$id;
		if ($info['bmgs']>0){
			$this->form=model('form')->info($info['bmgs']);
			//dump($this->info);
			//内容列表
			$this->field_list=model('form_list')->field_list($info['bmgs']);
			$this->list=model('form_list')->get_list($id,$this->form['table'],$limit);
			$count=model('form_list')->get_count($eid,$this->form['table']);
			$this->page = $this->page($url, $count, $listRows);
			$this->show('event/form_list');			
		}else{
			$where['eid']=$id;
			$this->list=model('event')->get_list($where,'_team',$limit);
			$count=model('event')->get_count($where,'_team');
			$this->assign('page', $this->page($url, $count, $listRows));
			$this->show();
		}		
	}
    public function baoming(){
    	if (!empty($_POST) && is_array($_POST)){
			$post=in($_POST);
			$id=intval($post['id']);
			$eid=intval($post['eid']);
			$fid=intval($post['fid']);
			$formid=intval($post['formid']);
			$status=intval($post['status']);
			if (1>$id || 1>$eid){
				return $this->msg('参数传递错误！',0);
			}
			model('event')->edit(array('status'=>$status),array('id'=>$id),'_team');
			//如果存在附加表单字段
			if ($fid>0 && $eid >0){
				unset($post['id']);
				unset($post['eid']);
				unset($post['fid']);
				unset($post['status']);
				unset($post['formid']);
				//字段循环处理
				$field_list=model('form_list')->field_list($formid);
				//dump($field_list);
				if (is_array($field_list)){
					$from=model('form')->info($formid,'`table`');
					//参数校对与过滤
					foreach($field_list as $v){
						if ($v['must'] && empty($post[$v['field']])){
							return $this->msg('【'.$v['name'].'】必须填写或选择，请检查！',0);
						}
						$data[$v['field']]=model('form')->field_in_check($post[$v['field']],$v);
						if(!isset($post[$v['field']])){
							$data[$v['field']]=$v['default'];
						}
					}
					if (count($data)>0){
						model('form')->field_edit_data($data,$fid,$from['table']);
					}
				}
			}
			return $this->msg('成功更新设置！',1);
    	}
		$this->action_name='报名管理';
    	$id=intval(in($_GET['id']));
    	$fid=intval(in($_GET['fid']));
        if(empty($id) && empty($fid)){
            return $this->msg('参数传递错误！',0);
        }
    	$form=model('form')->info($fid);
        if(!is_array($form)){
            return $this->msg('不存在该报名表单！',0);
        }
    	$info=model('form_list')->get_info($id,$form['table']);
		$this->field_list=model('form_list')->field_list($fid);
        $this->info=$info;
		$this->show();			
    }
	//活动考勤
    public function check(){
		$this->action_name='考勤管理';
    	$id=intval(in($_GET['id']));
    	$info=model('event')->info('id='.$id);
        if(empty($id) && !is_array($info)){
            $this->alert('参数传递错误！',0);
        }
        $this->info=$info;
        $url = __URL__ . '/check/id-'.$id.'-page-{page}';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		
		$where='eid='.$id.' and status in (1,3,4)';
		$this->type=$id;
		$this->list=model('event')->get_list($where,'_team',$limit);
        $count=model('event')->get_count($where,'_team');
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->show();
    }
	//数据字典
    public function dict(){
		if(!model('user_group')->menu_power('event/dict',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->action_name='数据字典';
        $url = __URL__ . '/dict/-page-{page}';
    	$listRows = 30;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;
		
		$where['key']=1;
		$this->list=model('event')->get_list($where,'_set',$limit);
        $count=model('event')->get_count($where,'_set');
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->show();
    }
	//数据字典编辑
    public function dict_edt(){
		if(!model('user_group')->menu_power('event/dict',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
    	if (!empty($_POST) && is_array($_POST)){
    		$this->edt_save($_POST);
    	}
		$this->action_name='数据字典编辑';
		$this->show();
    }
	//活动编辑
    public function edt(){
    	if (!empty($_POST) && is_array($_POST)){
    		$this->edt_save($_POST);
    	}
    	$id=intval($_GET['id']);
		if (empty($id)==true) {
			$this->action_name='创建活动';
			$info['stime']= $info['etime']= $info['dtime']=$info['ztime']=time();
			$info['shtime']=date('H');
			$info['ehtime']=date('H');
			$info['sitime']=date('i');
			$info['eitime']=date('i');

		}else{
			$this->action_name='编辑活动';
			$info = model('event')->info('id='.$id);
			$info_data = model('event')->info('eid='.$id,'_data');
			$this->about=unserialize($info_data['about']);
			$this->assign('info_data', $info_data);
			//$this->file_id=model('upload')->get_relation('event',$id);
			if (empty($info)) $this->alert('参数传递错误！',0);
		}
		$this->form_list=model('form')->category_list();
		//dump($this->form_list,1);
		$this->category_list=model('category')->category_list(2);
		//$this->area = model('badu')->get_area();
		$this->assign('info', $info);
		$this->show();
    }
	public function edt_save($data){
		if (!empty($data) && is_array($data)){
			$id=intval($data['id']);
			if (empty($data['title']) || strlen($data['title'])<2) {
				$this->msg('活动名称未填写或填写不正确！',0);
				return;
			}
			if (empty($data['about']['content']) || strlen($data['about']['content'])<6) {
				$this->msg('活动内容填写不完整！',0);
				return;
			}
			$info['title']=in($data['title']);
			$info['font_color']=in($data['font_color']);
			$info['font_bold']=in($data['font_bold']);
			$info['image']=in($data['image']);
			$info['weekday']=in($data['weekday']);
			$info['type']=in($data['type']);
			$area=in($data['area']);
			if(!empty($area)){
				if  (is_array($area) && count($area)>0) $area=implode(',', in(array_filter($area)));
			}
			$info['area']=$area;
			$info['stime']=strtotime(in($data['stime']).' '.in($data['shtime']).':'.in($data['sitime']));
			$info['etime']=strtotime(in($data['etime']).' '.in($data['ehtime']).':'.in($data['eitime']));
			$info['ztime']=strtotime($data['ztime']);
			if ($info['ztime']>$info['etime']) return $this->msg('招募截止时间不能晚于活动结束时间',0);
			
			$info['zmrs']=in($data['zmrs']);
			$info['vtime']=in($data['vtime']);
			$info['map']=in($data['map']);
			$info['zmbm']=intval($data['zmbm']);
			$info['zmdx']=intval($data['zmdx']);
			$info['description']=in($data['description']);
			$info['status']=intval($data['status']);
			$info['zmr']=in($data['zmr']);
			$info['zmtel']=in($data['zmtel']);
			$info['bmgs']=intval($data['bmgs']);

			$about['rmb']=in($data['about']['rmb']);
			$about['rmbs']=in($data['about']['rmbs']);
			$about['zmdd']=in($data['about']['zmdd']);
			
			$event['about']=serialize($about);
			$event['content']=in($data['about']['content']);
			$event['zmtj']=in($data['about']['zmtj']);
			$event['zmzy']=in($data['about']['zmzy']);
			//录入模型处理
			if(empty($id)){
				$user=$this->usr();
				$info['bid']=$user['id'];
				$info['bname']=$user['nicename'];
				$info['views']=0;
				$info['dtime']=time();
				if(model('event')->count(in($data['title']))>0 ) $this->msg('活动：（'.in($data['title']).'）已发布，请勿重复发布！',__URL__.'/edt/type-'.$info['type']);
				$event['eid']=model('event')->add($info);
				model('event')->add($event,'_data');
			}else{
				$this->check_user($id);
				if(model('event')->count(in($data['title']),$id)>0) $this->msg('活动：（'.in($data['title']).'）已发布，请勿重复发布！',__URL__.'/edt/type-'.$info['type']);
				model('event')->edit($info,'id='.$id);
				$status=model('event')->edit($event,'eid='.$id.'','_data');
				if (1>$status){
					 $event['eid']=$id;
					 model('event')->add($event,'_data');	
				}
			}
			$this->msg('操作成功！');
		}
	}
	// 公共调用
    public function set(){
		if (!empty($_POST) && is_array($_POST)){
			$id=intval(in($_POST['id']));
			$zt=intval(in($_POST['zt']));//0 删除 1 通过 2不通过 3待审 4结束 5进行中
			if (empty($zt) && !empty($id)){//删除
				$this->check_user($id);
				model('event')->del('id='.$id);
				model('event')->del('eid='.$id,'_data');
				$this->msg('删除成功！',1);
			}elseif ($zt==1 && !empty($id)){//通过
				$this->check_user($id);
				$data['status']=1;
				model('event')->edit($data,'id='.$id);
				$this->msg('活动已设置为开通招募！',1);
			}elseif ($zt==2 && !empty($id)){//未通过
				$this->check_user($id);
				$data['status']=0;
				model('event')->edit($data,'id='.$id);
				$this->msg('活动已设置为结束招募！',1);
			}elseif ($zt==3 && !empty($id)){//创建字典
				$data['key']=$id;
				$data['name']=in($_POST['title']);
				model('event')->add($data,'_set');
				$this->msg('添加成功！',1);
			}
		}
    }
    //批量操作
    public function batch(){
        if(strlen($_POST['status'])==0||empty($_POST['id'])){
            $this->msg('请先选择操作对象！',0);
        }
        $ids=in($_POST['id']);
        $status=intval(in($_POST['status']));
        if ($status==5){
        	$idv=in($_POST['vtime']);
        	$by=in($_POST['by']);
        	$evt=number_format(in($_POST['evt']),2);
	        foreach ($ids as $id) {
	        	$id=intval($id);
	        	$data['vzt']=1;
	        	$idv[$id]=number_format(str_replace('-','',$idv[$id]),2);
	        	if ($idv[$id]>$evt) $idv[$id]=$evt;
	        	$data['vtime']=str_replace('+','',$by[$id]).$idv[$id];
	            model('event')->edit($data,'id='.$id,'_team');
				$uid=intval($_POST["userid_".$id.""]);
				if ($uid>0){
					$member=model('member')->info($uid,'','gid,vcard,vtime');
					if ($member['gid']>1 || strlen($member['vcard'])>3){
						$mdata['vtime']=(number_format($member['vtime'])+($data['vtime']));
						if ($mdata['vtime']>0){
							$mdata['gid']=model('member')->auto_group($mdata['vtime']);
							if (1>$mdata['gid']) unset($mdata['gid']);
						}
						model('member')->edit($mdata,array("id"=>$uid));
						unset($mdata);
					}
				}
				unset($data);
	        }
			unset($_POST);
        }elseif ($status==6){
			$bys=in($_POST['by']);
        	$name=in($_POST['name']);
	        foreach ($ids as $id) {
	        	$id=intval($id);
	        	$by[$id]=intval($bys[$id]);
	        	if($by[$id]==1){
	            	model('event')->del('id='.$id,'_set');
	        	}else{
		        	$data['name']=$name[$id];
		            model('event')->edit($data,'id='.$id,'_set');
	        	}
	        }
       }else{
	        $data['status']=$status;
	        $id_array=substr($ids,0,-1);
	        $id_array=explode(',', $id_array);
	        foreach ($id_array  as $id) {
	        	$id=intval($id);
	            model('event')->edit($data,'id='.$id,'_team');
	        }
        }
        $this->msg('操作执行完毕！',1);
    }
	//发布者检查
    public function check_user($id){
		$info=model('event')->info('id='.$id);
		$user=$this->usr();
		if ($info['bid']!=$user['id'] && $user['gid']!=1){
			$this->msg('该活动不是您发起的，所以您不能编辑该活动，请返回！',0);
		}else{
			return $info;	
		}
    }

}