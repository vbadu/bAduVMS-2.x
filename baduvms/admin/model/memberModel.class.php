<?php
class memberModel extends commonModel{
    protected $table = 'member';

    public function __construct()
    {
        parent::__construct();
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
    public function get_list($table=NULL,$where=NULL,$limit=NULL,$order='id DESC',$field='*'){ 
		$table=$this->table.$table;
        return $this->model->table($table)->field($field)->where($where)->limit($limit)->order('id desc')->select();
    }
    //统计
	public function get_count($table=NULL,$where=NULL) {
		$table=$this->table.$table;  
        return $this->model->table($table)->where($where)->count();
	}
    //获取用户内容
    public function infos($id){
		$data=$this->model->query("SELECT * FROM {$this->model->pre}{$this->table} A LEFT JOIN {$this->model->pre}{$this->table}_data B ON A.id = B.mid WHERE A.id={$id}");
        return $data[0]; 

    }

    //获取用户内容
    public function info($where,$table=NULL,$field='*'){
		$table=$this->table.$table;  
        return $this->model->table($table)->field($field)->where($where)->find();
    }
    //检测用户
    public function checkuser($id=1){
        return $this->model->table($this->table)->where('id='.$id)->count(); 
    }

    //检测重复用户
    public function count($where,$id=null,$table=NULL)
    {
        if(!empty($id) && $id>0){
            $where.=' AND id<>'.intval($id);
        }
        return $this->model->table($this->table.$table)->where($where)->count(); 
    }

    //添加用户内容
    public function add($data,$table=NULL){
		$table=$this->table.$table;
        $_POST['regtime']=time();
        return $this->model->table($table)->data($data)->insert();
    }
    //编辑用户内容
    public function edit($data,$where,$table=NULL){
		$table=$this->table.$table;
        return $this->model->table($table)->data($data)->where($where)->update();
    }
    //删除用户内容
    public function del($where,$table=NULL){
		$table=$this->table.$table;
        return $this->model->table($table)->where($where)->delete(); 
    }
	protected function make_vcard($_vcard='',$back=false){
		$config = $this->config;
		if (empty($_vcard)){
			$_vcard=(strlen($config['VIP_CARD_NOW'])>0)?in($config['VIP_CARD_NOW']):in($config['VIP_CARD_IDS']);
			if (empty($_vcard)){$this->alert("会员编号自动生成失败，请先进入【系统】设置会员相关默认配置项。");	}
		}
		$_vcard = str_replace(array("<", ">", "[","]", "{","}"), array("", "","","","",""), $_vcard);
		$vcard = $config['VIP_CARD_TPL'];
		$vcard = preg_replace ("/\{card\}/iU", $config['VIP_CARD'], $vcard);
		$vcard = preg_replace ("/\{yyyy\}/iU", date('Y'), $vcard);
		$vcard = preg_replace ("/\{yy\}/iU", date('y'), $vcard);
		$vcard = preg_replace ("/\{mm\}/iU", date('m'), $vcard);
		$vcard = preg_replace ("/\{dd\}/iU", date('d'), $vcard);
		$vcard = preg_replace ("/\{hh\}/iU", date('h'), $vcard);
		$vcard = preg_replace ("/\{ii\}/iU", date('i'), $vcard);
		//dump($vcard);
		$now_card=$vcard = preg_replace ("/\{id\}/iU", $_vcard, $vcard);
		//dump($vcard,1);
		if ($back){
			return array('card'=>$vcard,'now'=>$now_card);	
		}
		return $vcard;
	}
	//生成编号	
	public function new_vcard(){
		$config = $this->config;
		if (empty($config['VIP_AUTO']) || empty($config['VIP_CARD_TPL']) || empty($config['VIP_CARD_IDS'])) {
			exit("会员规则没有设置，请先进入【系统】设置会员相关默认配置项。");	
		}
		if (isset($config['VIP_AUTO']) && !$config['VIP_AUTO']) {
			return false;
		}
		$_vcard=(strlen($config['VIP_CARD_NOW'])>0)?in($config['VIP_CARD_NOW']):in($config['VIP_CARD_IDS']);
		$vcard=$this->make_vcard($_vcard);

		$where['vcard']=$vcard;
		$where['status']=1;
		//如果是自动累加
		if ($config['VIP_CARD_MOD']==true){
			if ($this->model->table($this->table)->where($where)->count()>0){
				$data=$this->model->table($this->table)->where('`status`=1')->field('MAX(vcard) as vcard')->find();
				$_vcard=substr(substr($data['vcard'],-strlen($config['VIP_CARD_IDS'])-1)+1,-strlen($config['VIP_CARD_IDS']));
				$vcard=$this->make_vcard($_vcard);
				config(array('VIP_CARD_NOW'=>$_vcard));
				Config::set('VIP_CARD_NOW','{'.$_vcard.'}');
				Config::set('VIP_CARD_DAY',date('Ym'));
				//echo  (int)$data['vcard'];
			}
		}else{
			//如果配置日期等于当前日期，且没有人使用该编号
			if ($config['VIP_CARD_DAY']==date('Ym') && 1>$this->model->table($this->table)->where($where)->count()){
				$_vcard=$_vcard;
			}else{
				$data=$this->model->table($this->table)->where('`status`=1')->field('MAX(vcard) as vcard,dtime')->find();
				if (date('Ym',$data['dtime'])==date('Ym')){
					$_vcard=substr(substr($data['vcard'],-strlen($config['VIP_CARD_IDS'])-1)+1,-strlen($config['VIP_CARD_IDS']));
					$vcard=$this->make_vcard($_vcard);
				}else{
					$_vcard=in($config['VIP_CARD_IDS']);
					$vcard = $config['VIP_CARD_TPL'];
					$vcard = preg_replace ("/\{card\}/iU", $config['VIP_CARD'], $vcard);
					$vcard = preg_replace ("/\{yyyy\}/iU", date('Y'), $vcard);
					$vcard = preg_replace ("/\{yy\}/iU", date('y'), $vcard);
					$vcard = preg_replace ("/\{mm\}/iU", date('m'), $vcard);
					$vcard = preg_replace ("/\{dd\}/iU", date('d'), $vcard);
					$vcard = preg_replace ("/\{hh\}/iU", date('h'), $vcard);
					$vcard = preg_replace ("/\{ii\}/iU", date('i'), $vcard);
					$vcard = preg_replace ("/\{id\}/iU", $_vcard, $vcard);
				}
				Config::set('VIP_CARD_NOW',$_vcard);
				Config::set('VIP_CARD_DAY',date('Ym'));
			}
		}
		return $vcard;
	}
	// 编号规则
	public function vcard($vcard='',$id='',$status=1){
		$config = $this->config;
		$id=intval($id);
		if (empty($config['VIP_AUTO']) || empty($config['VIP_CARD_TPL']) || empty($config['VIP_CARD_IDS'])) {
			return "会员规则没有设置，请先进入【系统】设置会员相关默认配置项。";	
		}
		if (empty($vcard) && empty($id)){
			$vcard=$this->new_vcard();
		}else if(3>strlen($vcard) && $id>0){
			if ($status==1){
				$where="`status`=1 and ";	
			}
			if ($this->model->table($this->table)->where($where."`id`={$id}")->count()>0){
				$vcard=$this->new_vcard();
			}else{
				return '';
			}
		}else{
			if ($this->model->table($this->table)->where("`status`=1 and `vcard`={$vcard} and `id`<>{$id}")->count()>0){
				$vcard=$this->new_vcard();
			}
		}
		//格式化模板
		$array=str_replace('}{','|',$config['VIP_CARD_TPL']);
		$array=str_replace('}','',$array);
		$array=str_replace('{','',$array);
		$array=explode('|',$array);
		$temp=$vcard;
		foreach ($array as $k=>$v){
			if ($v=='card' && !empty($config['VIP_CARD']) && $config['VIP_CARD']==substr($temp,0,strlen($config['VIP_CARD']))){
				$temp=substr_replace($temp,'',0,strlen($config['VIP_CARD']));
			}
			if ($v=='yyyy' && @preg_match("/[\d]{4}/", substr( $temp,0,4 ) ) ){
				$temp=substr_replace($temp,'',0,4);
			}
			if ($v=='yy' && @preg_match( "/[\d]{1,2}/", substr( $temp,0,2 ) ) ){
				$temp=substr_replace($temp,'',0,2);
			}
			if ($v=='mm' && @preg_match( "/[\d]{1,2}/", substr( $temp,0,2 ) ) ){
				$temp=substr_replace($temp,'',0,2);
			}
			if ($v=='dd' && @preg_match( "/[\d]{1,2}/", substr( $temp,0,2 ) ) ){
				$temp=substr_replace($temp,'',0,2);
			}
			if ($v=='hh' && @preg_match( "/[\d]{1,2}/", substr( $temp,0,2 ) ) ){
				$temp=substr_replace($temp,'',0,2);
			}
			if ($v=='ii' && @preg_match( "/[\d]{1,2}/", substr( $temp,0,2 ) ) ){
				$temp=substr_replace($temp,'',0,2);
			}
			$_vcard = str_replace(array("<", ">", "[","]", "{","}"), array("", "","","","",""), $config['VIP_CARD_IDS']);
			if ($v=='id' && !empty($config['VIP_CARD_IDS']) && strlen($temp)==strlen($_vcard) ){
				$temp='';
			}
		}
		if (strlen($temp)>0){
			$vcard=$this->new_vcard();
			//$this->alert("当前的会员编号不符合系统设置的编号规则，请重新修改。");	
		}
		return $vcard;		
	}
	public function auto_group($vtime=0){
		$vtime=@number_format($vtime);
		if (1>$vtime) return 0;
		$config_file= bAdu_PATH.'config/group.php';
		if (is_file($config_file)){
			$group=require $config_file;
			if (!is_array($group)) $group=module('member')->groupconfig();
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
	
}?>