<?php
class areaMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('area',true);
	}
    public function index()
    {
        $this->newid=model('area')->get_maxid();
		$this->show();
    }
	public function getJson(){
		$id=intval($_POST['id']);
		$list=model('area')->get_list($id);
        if(!empty($list)){
            $data='';
            foreach ($list as $v) {
				$isParent=($v['isParent'])?'true':'false';
            	$data.='{id:'.$v['cid'].',name:"'.$v['name'].'",isParent:'.$isParent.',sort:"'.$v['sort'].'"}, '."";
            }
        }
		echo $data='['.$data.']';
	}
    public function sequence(){
		$post=in($_POST);
		$post=str_ireplace("&quot;","",$post);
		$post=str_ireplace("[","",in($post));
		$post=str_ireplace("]","",in($post));
		$post=str_ireplace("{","",in($post));
		$post=str_ireplace("}","",in($post));
		$post['data']=explode(',',$post['data']);
		$post['target']=explode(',',$post['target']);
		foreach ($post['data'] as $value) {
			$temp=explode(':',$value);
			$data[strtolower($temp[0])]=$temp[1];
		}		
		foreach ($post['target'] as $key=>$value) {
			if ($key>=21) continue;
			$temp=explode(':',$value);
			$target[strtolower($temp[0])]=$temp[1];
		}
		
		$new['cid']=intval($data['id']);
		$new['type']=intval($data['level']);
		$new['name']=in($data['name']);
		$new['pid']=intval($data['pid']);
		$new['isParent']=($data['isparent']=='true')?1:0;
		if ($post['type']=='next'){
			$new['sort']=intval($target['sort'])-1;
		}elseif ($post['type']=='prev'){
			$new['sort']=intval($target['sort'])+1;
		}else{
			$new['sort']=intval($data['id']);
		}
		//dump($data);
		//dump($target);
		//dump($new);
		model('area')->data_save($new);
		unset($new);
		unset($data);
		//更新上一级节点
		$new['cid']=intval($target['id']);
		$new['type']=intval($target['level']);
		$new['isParent']=($target['isparent']=='true')?1:0;
		model('area')->data_save($new);
		unset($new);
		unset($target);
        $this->msg('更新成功！');
    }
    public function edit()
    {
		$post=in($_POST);
		$new['cid']=intval($post['id']);
		$new['pid']=intval($post['pid']);
		$new['name']=in($post['name']);
		$new['isParent']=(in($post['parent'])=='true')?1:0;
		$new['type']=intval($post['level']);
		
		if (1>model('area')->get_count($new['cid'])){
			$new['sort']=$new['pid'];
			$new['show']=1;
			unset($new['cid']);
			model('area')->data_in($new);
			//更新上一级节点
			$data['cid']=$new['pid'];
			$data['isParent']=1;
			model('area')->data_save($data);
		}else{
			model('area')->data_save($new);
		}
        $this->msg('更新成功！');
    }


    public function del()
    {
		$id=intval($_POST['id']);
		if (1==model('area')->get_count($id)){
			if (1>model('area')->get_count($id,'pid')){
				model('area')->del($id);
				$this->msg('删除成功！');
			}
        	$this->msg('删除失败！请先将隶属区域删除',0);
		}
        $this->msg('删除失败！如果是刚创建的要删除，请先刷新后再执行删除操作！',0);
    }


}

?>