<?php
//附件管理
class upload_fileMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('upload_file',true);
	}
	public function index() {

        $type=$_GET['type'];
        if(!empty($type)){
            $url_type='-type-'.$type;
            if($type=='no'){
                $where='type is Null';
            }else{
                $where='type="'.$type.'"';
            }
        }

        $ext=intval($_GET['ext']);
        if(!empty($ext)){
        $ext1='"jpg","gif","jpeg","bmp","png"';
        $ext2='"flv","wmv","wma","mp3","jpeg","mp4","3gp","avi","swf","mkv"';
        $ext3='"doc","xsl","wps","docx","xslx","ppt","pptx"';
        $ext4='"zip","rar","7z"';
        $ext5=$ext1.','.$ext2.','.$ext3.','.$ext4;
        $url_ext='-ext-'.$ext;
        switch ($ext) {
            case 1:
                $where='ext in('.$ext1.')';
                break;
            case 2:
                $where='ext in('.$ext2.')';
                break;
            case 3:
                $where='ext in('.$ext3.')';
                break;
            case 4:
                $where='ext in('.$ext4.')';
                break;
            case 5:
                $where='ext not in('.$ext5.')';
                break;
        }
        }

        $search=in(urldecode($_GET['search']));
        if(!empty($search)){
            $where=' title like "%' . $search . '%"';
            $url_search='-search-'.$search;
        }

        //分页处理
        //分页信息
        $url = __URL__ . '/index/page-{page}'.$url_type.$url_ext.$url_search; //分页基准网址
        $listRows = 50;
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        $limit = $limit_start . ',' . $listRows;

        //内容列表
        $this->list=model('upload')->file_list($where,$limit);
        //统计总内容数量
        $count=model('upload')->count($where);
        $this->assign('page', $this->page($url, $count, $listRows));
        $this->module_list=model('upload')->module_list();
		$this->show();  
	}

    //上传删除
    public function del() {
        $id=intval($_POST['id']);
        $this->alert_str($id,'int',true);
        /*hook*/
        $this->plus_hook('upload_file','del',$id);
        /*hook end*/
        //录入模型处理
        $status=model('upload')->del($id);
        $this->msg('附件删除成功！',1);
    }
	

	

}