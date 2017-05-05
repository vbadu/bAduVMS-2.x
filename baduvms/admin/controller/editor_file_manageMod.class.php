<?php
class editor_file_manageMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //KE文件JSON管理
    public function index() {

    	$order=in($_GET['order']);
        if(!empty($order)){
            $where='type="'.$order.'"';
        }

        $search=in(urldecode($_GET['search']));
        if(!empty($search)){
            $where=' title like "%' . $search . '%"';
            $url_search='-search-'.$search;
        }
    	

    	$url = __URL__ . '/index.html?order='.$order.'&page={page}&search='.$url_search; //分页基准网址

    	$cur_page=$_GET['page'];

    	$count=model('upload')->count($where);
        $listRows = 20;

        $totalPage=ceil($count/$listRows);
		if($cur_page>=$totalPage){
			$cur_page=$totalPage;
		}
		if($cur_page<=1){
			$cur_page=1;
		}
        $limit=$this->pagelimit($url,$listRows);
        
    	//图片扩展名
		$ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
    	$list=model('upload')->file_list($where,$limit);
    	$file_list = array();
    	if(!empty($list)){
    		foreach ($list as $key => $value) {
    			$file_list[$key]['is_dir']=false;
    			$file_list[$key]['has_file']=false;
    			$file_list[$key]['filesize']=$value['size'];
    			$file_list[$key]['dir_path']='';
    			$file_list[$key]['is_photo']=in_array($value['ext'], $ext_arr);
    			$file_list[$key]['filetype'] = $value['ext'];
    			$file_list[$key]['filename'] = $value['title'];
    			$file_list[$key]['filedir'] = $value['file'];
				$file_list[$key]['datetime'] = date('Y-m-d H:i:s',$value['time']);
    		}
    	}


		$result = array();
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = '';
		//相对于根目录的当前目录
		$result['current_dir_path'] = '';
		//当前目录的URL
		$result['current_url'] =	'';
		//文件数
		$result['total_count'] = count($file_list);
		//文件列表数组
		$result['file_list'] = $file_list;

		//总页数
		$result['cur_page'] = $cur_page;

		//总页数
		$result['totalPage'] = $totalPage;

		//所属模块
		$module_list=model('upload')->module_list();
		$module='<option value="0">全部</option>';
		if(!empty($module_list)){
			foreach ($module_list as $value) {
				$module.='<option value="'.$value['type'].'"';
				if($order==$value){
					$module.='  selected="selected" ';
				}
				$module.=' >'.$value['name'].'</option>';
			}
		}
		$result['module_list'] =$module; 

    	$page = new Page();
        $result['page'] = $page->show($url, $count, $listRows, 5);

        $result['search'] = $search;
        @header("Content-type:text/html");
        echo json_encode($result);


    }

}

?>