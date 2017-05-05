<?php
class eventModel extends commonModel{
    protected $table = 'event';

    public function __construct()
    {
        parent::__construct();
    }
    //统计
	public function get_sum($id=1,$table=NULL) {
		$data=$this->model->query("SELECT sum(vtime) as vtimes FROM {$this->model->pre}{$this->table}{$table} WHERE uid={$id}");
		return $data[0]['vtimes'];
	}
	private function getWhere($data) {
		if (!is_array($data)) return $data;
		ksort($data);  
		$items = array();  
		foreach ($data as $key => $value) {  
			$items[] = "{$key}='{$value}'";  
		}  
		return join(" AND ", $items);  
	} 
	
    public function get_list($where='',$limit=NULL,$order='B.id DESC'){  
        if(!empty($where) && is_array($where)){
            $where='WHERE '.$this->getWhere($where);
        }elseif(!empty($where) && strpos($where, "=") !== false){
            $where='WHERE '.$where;
		}
        if(!empty($limit)){
            $limit='limit '.$limit;
        }
        if(!empty($order)){
            $order=$order.',';
        }
        echo $sql="SELECT * FROM {$this->model->pre}event A LEFT JOIN {$this->model->pre}event_data B ON A.id = B.eid {$where} ORDER BY {$order}A.dtime desc {$limit}";
        $data=$this->model->query($sql);
        return $data; 
    }
    public function get_count($where=''){  
        if(!empty($where) && is_array($where)){
            $where='WHERE '.$this->getWhere($where);
        }elseif(!empty($where) && strpos($where, "=") !== false){
            $where='WHERE '.$where;
		}
        $data=$this->model->query("SELECT count(A.id) as num FROM {$this->model->pre}event A LEFT JOIN {$this->model->pre}event_data B ON A.id=B.eid {$where}");
        return $data[0]['num']; 
    }
	
	public function get_list_my($uid=0,$limit=NULL,$zt=0,$order='B.id DESC'){  
        if(!empty($uid) && $uid>0){
            $where='WHERE B.uid='.intval($uid);
        }
        if(!empty($zt) && $zt>0){
            $where.=' AND A.zt='.intval($zt);
        }
        if(!empty($limit)){
            $limit='limit '.$limit;
        }
        if(!empty($order)){
            $order=$order.',';
        }
        $sql="SELECT A.id as eid,A.bid,A.bname,A.title,A.font_color,A.font_bold,A.image,A.type,A.area,A.zmr,A.zmtel,A.weekday,A.stime,A.etime,A.ztime,A.zmrs,A.bmrs,A.status as event_status,A.vtime as event_vtime,A.zt,B.* FROM {$this->model->pre}event A LEFT JOIN {$this->model->pre}event_team B ON A.id = B.eid {$where} ORDER BY {$order}B.dtime desc {$limit}";
        $data=$this->model->query($sql);
        return $data; 
    }
    public function get_count_my($uid=0,$zt=0){  
        $where='WHERE B.uid='.intval($uid);
        if(!empty($zt) && $zt>0){
            $where.=' AND A.zt='.intval($zt);
        }
        $data=$this->model->query("SELECT count(B.id) as num FROM {$this->model->pre}event A LEFT JOIN {$this->model->pre}event_team B ON A.id=B.eid {$where}");
        return $data[0]['num']; 
    }
	public function getinfo($id=0){  
		$id=intval($id);
        if(empty($id) && 1>$id) return;
        $sql="SELECT A.*,B.* FROM {$this->model->pre}event A LEFT JOIN {$this->model->pre}event_data B ON A.id = B.eid WHERE A.id= {$id}";
        $data=$this->model->query($sql);
        return $data[0]; 
    }
    //列表
    public function getlist($where=NULL,$table=NULL,$limit=NULL,$order='id DESC'){  
		$table=$this->table. $table;
        $data=$this->model->table($table)->where($where)->limit($limit)->order($order)->select();
        return $data;
    }
    //统计
	public function getcount($where=NULL,$table=NULL) {
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->count();
	}
    //获取内容
    public function info($where=NULL,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->find();
    }
    //更新项目
    public function edit($data,$condition=null,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->data($data)->where($condition)->update();
    }
    //添加内容
    public function add($data,$table=NULL){
		$table=$this->table. $table;
        $data['dtime']=time();
        return $this->model->table($table)->data($data)->insert();
    }
    //删除内容
    public function del($where=0,$table=NULL){
		$table=$this->table. $table;
        return $this->model->table($table)->where($where)->delete(); 
    }
    //检测重复
    public function count($title,$id=null)
    {
        if(!empty($id)){
            $where=' AND id<>'.$id;
        }
        return $this->model->table($this->table)->where('title="'.$title.'"'.$where)->count(); 
    }
    //获取设置
    public function get_set_radio($name='weekday',$id=1,$data=1){
        $list=$this->get_list('`key`='.$id,'_set','','id asc');
        if (!is_array($list)) return '<font color=red>请先设置操作选项</font>';
        $html='';
            foreach ($list as $vo) {
            	($data==$vo['id'])?$checked=" checked='checked' ":$checked=" ";
                $html .= "<input name='".$name."'".$checked."value='".$vo['id']."' type='radio'> ".$vo['name']."  ";
    		}
         return $html;
    }
    //匹配数据
    public function get_set_list($name='type',$id=1,$data=1,$title='分类',$config=array()){
		if (is_null($id) || (strlen($id)==0)) $id=0;
		if (is_null($data) || (strlen($data)==0)) $data=0;
		if ($data==0) $selected = " class='active'";
		$url='';
		$catid=intval($_GET['id']);
		$status=intval($_GET['status']);
		$data=intval($data);
		$key=urldecode(in($_GET['key']));
        $html .= "<li " . $selected . "><a href=".__URL__."/index/id-".$catid."-".$name."-0-status-".$status."-key-".$key.$config['URL_HTML_SUFFIX'].">不限".$title."</a></li>";
        $list=$this->getlist('`key`='.$id,'_set','','id asc');
		if (count($list)>1) {
            foreach ($list as $vo) {
            	($data==$vo['id'])?$selected=" class='active'":$selected="";
				$html .= "<li " . $selected . "><a href=".__URL__."/index/id-".$catid."-".$name."-".$vo['id']."-status-".$status."-key-".$key.$config['URL_HTML_SUFFIX'].">".$vo['name']."</a></li>";
    		}
		}
        return $html;
    }
	//匹配数据
    public function get_set_select($name='type',$id=1,$data=1){
    	$html='<select name="'.$name.'" reg="." id="cid" msg="请选择">';
		if (is_null($id) || (strlen($id)==0)) $id=0;
		if ($id==0) $selected = " selected='selected'";
        $html .= "<option " . $selected . " value=''>== 请选择 ==</option>";
        $list=$this->getlist('`key`='.$id,'_set','','id asc');
		if (count($list)>1) {
            foreach ($list as $vo) {
            	($data==$vo['id'])?$selected=" selected='selected'":$selected="";
                $html .= "<option ".$selected." value='".$vo['id']."'>".$vo['name']."</option>";
    		}
		}
    	$html.='</select>';
         return $html;
    }
    //收费类别
    public function get_rmb($data=1) {
    	$html='<select name="about[rmb]" reg="." id="cid" msg="请选择" class="u-slt">';
         ($data==1)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='1'>免费</option>";
         ($data==2)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='2'>AA制</option>";
         ($data==3)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='3'>自助</option>";
         ($data==4)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='4'>收费</option>";
         ($data==5)?$selected=" selected='selected'":$selected="";
         $html .= "<option ".$selected." value='5'>其他</option>";
      	$html.='</select>';
       return $html;
    }
    //审核类别
    public function echo_status($data=1) {
		$data=intval($data);
		if (empty($data)) $data=1;
        switch ($data) {
            case 1:
                $html = '通过';
                break;
            case 2:
                $html = '未通过';
                break;
            case 3:
                $html =	'请假中';
                break;
            case 4:
                $html =	'请假已同意';
                break;
            case 5:
                $html =	'缺席活动';
                break;
            case 6:
                $html =	'申请撤销中';
                break;
			default:
				$html = '待审中';
				break;
        }

        return $html;
    }

	//检查活动状态
    public function check_event($id){
		$id=intval($id);
		if (1>$id || $id>99999999) $this->msg('活动不存在或已下架！',0);
		$where['id']=$id;
		$info=$this->info($where);
		if (is_array($info)){
			if ($info['status']==1){
				$now=date('YmdHi');
				$ztime=date('YmdHi',$info['ztime']);
				$etime=date('YmdHi',$info['etime']);
				$ntime=$ztime-$now;
				$otime=$etime-$now;
				if ($ntime>0){
					return $info;
				}else{
					if (1>$otime){
						$data['status']=0;
						$data['zt']=3;
						$this->edit($data,$where);
					}
					$this->msg('活动招募已截止，您的操作无法受理，请向活动负责人联系！',0);
				}
			}
			$this->msg('活动已结束，您的操作无法受理！',0);
		}else{
			$this->msg('活动不存在或已下架！',0);
		}
    }
	//检查活动状态
    public function check_event_status($info,$do=true){
		$id=intval($info['eid']);//活动ID
		$zt=intval($info['zt']);//当前活动状态（招募中0，即将开1、进行中2、结束3）
		$zts=intval($info['event_status']);//当前活动状态（开启或关闭）
		$pass=intval($info['status']);//是否通过审批
		$vzt=intval($info['vzt']);//是否已设置服务时间
		$ztime=intval($info['ztime']);
		$stime=intval($info['stime']);
		$etime=intval($info['etime']);
		$vtime=in($info['vtime']);
		if (1>$id) return false;
		if ($pass>7) return false;
		$where['id']=$id;
		$typete=(1>($etime-time()))?true:false;
		$typets=(1>($stime-time()))?true:false;
		$typetz=(1>($ztime-time()))?true:false;
		$typeet=(($etime-time())>0)?true:false;
		$typest=(($stime-time())>0)?true:false;
		$typezt=(($ztime-time())>0)?true:false;
		//招募、开始、结束都大于当前时间
		if ($typezt && $typest && $typeet){
			if($do==true){
				echo '招募中';
				if($pass==true)	echo ' 召集人：'.$info['zmr'].'('.$info['zmtel'].')';
			}
			if($zt!=0 && $zts>0){
				$data['zt']=0;
				$this->edit($data,$where);
			}
			return;
		}//如果开始、结束大于当前时间，招募小于当前时间
		elseif ($typetz && $typest && $typeet){
			if($do==true){
				echo '招募已结束';
				if($pass==true)	echo ' 召集人：'.$info['zmr'].'('.$info['zmtel'].')';
			}
			if($zt!=1) 	$data['zt']=1;
			if ($zts>0) $data['status']=0;
			if (is_array($data)) $this->edit($data,$where);
			return;
		}//如果招募、结束大于当前时间，开始小于当前时间
		elseif ($typezt && $typets && $typeet){
			if($do==true){
				echo '活动进行中';
				if($pass==true)	echo ' 召集人：'.$info['zmr'].'('.$info['zmtel'].')';
			}
			if($zt!=2) 	$data['zt']=2;
			if (is_array($data)) $this->edit($data,$where);
			return;
		}//如果结束大于当前时间，招募、开始小于当前时间
		elseif ($typetz && $typets && $typeet){
			if($do==true){
				echo '活动进行中';
				if($pass==true)	echo ' 召集人：'.$info['zmr'].'('.$info['zmtel'].')';
			}
			if($zt!=2) 	$data['zt']=2;
			if ($zts>0) $data['status']=0;
			if (is_array($data)) $this->edit($data,$where);
			return;
		}else{
			if($do==true)	echo '活动已结束';
			if($zt!=3) 	$data['zt']=3;
			if ($zts>0) $data['status']=0;
			if (is_array($data)) $this->edit($data,$where);
			if($vzt>0){
				echo '您的服务时数：'.$vtime.'小时';
			}
			return;
		}

		return true;
    }
	//检查活动状态  活动状态，活动条件状态,招募时间，开始时间，结束时间，活动ID
    public function get_event_status($id,$status=1,$zt=1,$ztime='',$stime='',$etime=''){
		$id=intval($id);
		$zts=intval($status);
		$zt=intval($zt);
		if (1>$id) return false;
		if (0>$zts) return false;
		if (0>$zt) return false;
		$where['id']=$id;
		if ($zt==3){
			return "活动已结束";	
		}elseif ($zt==2){
			return "活动正在进行";	
		}elseif ($zt==1){
			return "活动即将开始";	
		}elseif (1>$zt && $zts==1){		
			if (1>($ztime-time()) && 1>($stime-time()) && 1>($etime-time()) ){
				if ($zt!=3){
					$data['zt']=3;	
					$this->edit($data,$where);
				}
				return "活动已结束";
			}elseif (1>($ztime-time()) && 1>($stime-time()) && ($etime-time())>0 ){
				if ($zt!=2){
					$data['zt']=2;	
					$this->edit($data,$where);
				}
				return "活动正在进行";
			}elseif (1>($ztime-time()) && ($stime-time())>0 && ($etime-time())>0 ){
				if ($zt!=1){
					$data['zt']=1;	
					$this->edit($data,$where);
				}
				return "活动即将开始";
			}else{
				if ($zt!=1){
					$data['zt']=1;	
					$this->edit($data,$where);
				}
				return "正在招募";
			}
		}
		if ($zt!=3){
			$data['zt']=3;	
			$this->edit($data,$where);
		}
		return "活动已结束";	
    }












}?>