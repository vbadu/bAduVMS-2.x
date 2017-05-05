<?php
//分类页
class categoryModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getid($id)
    {
        return $this->model->field('cid')->table('category')->where('pid='.$id)->select();
    }
    public function getall($id)
    {
        return $this->model->field('cid')->table('category')->where('model='.$id)->select();
    }

    //栏目信息
    public function info($cid)
    {
        return $this->model->table('category')->where('cid=' . $cid)->find(); 
    }

    //模型信息
    public function model_info($mid)
    {
        return $this->model->table('model')->where('mid='.$mid)->find();
    }

    //模块修正
    public function model_jump($info){
		//dump($info,1);
		$mid=intval($info['mid']);
		if ($mid==3){
			$link=$this->return_tpl(html_out($info['content']));
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$link."");
			exit;
		}elseif ($mid==2){
			if(empty($info['content'])){
				$info['content']='暂无内容';
			}
        	$info['content']=html_out($info['content']);
	
		}

        /*hook*/
        $this->plus_hook('category','index',$this->info);
        $this->info=$this->plus_hook_replace('category','index_replace',$this->info);
        /*hook end*/

        //位置导航
        $this->nav=array_reverse(model('category')->nav($this->info['cid']));

        if ( $this->config['HTML_CACHE_ON'] ) {
           HtmlCache::write();
        }
        exit;
    }
    //内容列表
    public function get_list($id='',$limit='0,100',$order='sequence desc,cid desc'){
        if($id){
        	$where['pid']=$id;
            $data=$this->model->table('category')->limit($limit)->where($where)->order($order)->select();
        }else{
            $data=$this->model->table('category')->limit($limit)->order($order)->select();
        }
    	return $data;
    }
    //内容列表
    public function content_list($cid,$where,$limit,$list_sort)
    {
        $loop="SELECT A.*,B.name as cname,B.subname as csubname,B.mid
             FROM {$this->model->pre}content A 
             LEFT JOIN {$this->model->pre}category B ON A.cid = B.cid
             WHERE {$where} ORDER BY {$list_sort} LIMIT {$limit}";
            return $this->model->query($loop);
    }

    //内容统计
    public function content_count($cid,$where)
    {
        $count=$this->model
                ->table('content','A')
                ->add_table('category','B','A.cid = B.cid')
                ->where($where)
                ->count();
        return $count;
    }


    //栏目导航
    public function nav($id)
    {
        $data = $this->model->field('cid,pid,name,urlname')->table('category')->select();
        $cat = new Category(array(
            'cid',
            'pid',
            'name',
            'urlname',
            'cname'));
        if(empty($data)){
             return;
        }
        $cat = $cat->getPath($data, $id);
        return $cat; 
    }

    //栏目树
    public function getcat($cid)
    {
        $id = $cid;
        $data = $this->model->field('cid,pid,name')->table('category')->select();
        $cat = new Category(array(
            'cid',
            'pid',
            'name',
            'cname')); //初始化无限分类

        $cat_for = $cat->getTree($data, $id); //获取分类数据树结构
        if(empty($cat_for)){
            return $id;
        }
        foreach ($cat_for as $v) {
            $cat_id .= $v['cid'] . ",";
        }

        if (!empty($cat_id)) {
            return $cat_id . $id;
        } else {
            return $id;
        }
    }

    //URL路径
    public function url_format($dir,$cid,$cname){
        $patterns =array(  
        "{EXT}",
        "{CDIR}",
        "{P}", 
        );
        $replacements=array(  
        '.html',
        $cname,
        '{page}',
        );
        $url_catrgory_page=str_replace($patterns,$replacements,$dir);
        return __INDEX__ .'/'.$url_catrgory_page;
    }


}

?>