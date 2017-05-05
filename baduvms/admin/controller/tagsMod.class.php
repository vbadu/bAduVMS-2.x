<?php
//tag管理
class tagsMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
		$this->model_url=$_GET['_module'];
        if(!model('user_group')->menu_power('article',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
	}
	public function index() {

        $where_url='';
        $order='A.id desc';
        $sequence=intval($_GET['sequence']);
        if($sequence==1){
            $where_url.='sequence-1-';
            $order=' A.click desc ';
        }
        if($sequence==2){
            $where_url.='sequence-2-';
            $order=' A.click asc ';
        }

        if(isset($_GET['cid'])){
            $cid=intval($_GET['cid']);
            if($cid){
                $where_url.='cid-'.$cid.'-';
                $where=' AND A.cid='.$cid;

            }else{
                $where_url.='cid-0-';
                $where=' AND (A.cid=0 OR A.cid is null)';
            }
        }

        $search=in(urldecode($_GET['search']));
        if(!empty($search)){
            $where_url.='cid-'.urlencode($search).'-';
            $where=' AND A.name like "%'.$search.'%"';
        }

        //分页信息
        $url = __URL__ . '/index/'.$where_url.'page-{page}.html'; //分页基准网址
        $listRows = 50;
        $limit=$this->pagelimit($url,$listRows);
        //内容列表
        $this->list=model('tags')->tag_list($where,$limit,$order);
        //统计总内容数量
        $count=model('tags')->count($where);
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->category_list=model('tags')->tag_category();
		$this->show();  
	}

    public function category() {
        $this->list=model('tags')->tag_category();
        $this->show();
    }


    //添加TAG分类
    public function category_add()
    {
        $this->action_name='添加';
        $this->action='add';
        $this->show('tags/category_info'); 
    }

    public function category_add_save() {
        //录入模型处理
        model('tags')->category_add($_POST);
        $this->msg('TAG组添加成功！',1);
    }

    //修改
    public function category_edit() {
        $cid=intval($_GET['cid']);
        $this->alert_str($cid,'int');
        $this->info=model('tags')->category_info($cid);
        $this->action_name='编辑';
        $this->action='edit';
        $this->show('tags/category_info'); 
    }

    //修改数据
    public function category_edit_save() {
        $cid=$_POST['cid'];
        $this->alert_str($cid,'int',true);
        //录入模型处理
        model('tags')->category_edit($_POST);
        $this->msg('TAG组修改成功! ',1);
    }

    //删除
    public function category_del() {
        $cid=intval($_POST['cid']);
        $this->alert_str($cid,'int',true);
        //录入模型处理
        model('tags')->category_del($cid);
        $this->msg('TAG组删除成功！',1);
    }

    //批量操作
    public function batch() {
        $status=intval($_POST['status']);
        $cid=intval($_POST['cid']);
        $this->alert_str($status,'',true);
        $this->alert_str($_POST['id'],'',true);
        $id_array=substr($_POST['id'],0,-1);
        $id_array=explode(',', $id_array);

        switch ($status) {
            case 1:
                foreach ($id_array as $value) {
                    model('tags')->del($value);
                }
                $this->msg('tag删除成功！',1);
                break;
            case 2:
                foreach ($id_array as $value) {
                    $data['id']=$value;
                    $data['cid']=$cid;
                    model('tags')->edit($data);
                }
                $this->msg('tag分组成功！',1);
                break;
        }
    }
}