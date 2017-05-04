<?php
class categoryMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }
    public function _empty(){
		$this->error404(3);
	}
    public function index()
    {
        $cid = intval($_GET['cid']);
        if (empty($cid)) {
            $this->error404();
        }
        //读取栏目信息
        $info=model('category')->info($cid);
        if (!is_array($info)){
            $this->error404();
        }
        //模块自动纠正
		$mid=intval($info['mid']);
		if ($mid==3){
			$link=$this->return_tpl(html_out($info['content']));
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$link."");
			exit;
		}
		//URL路径
		$model_info = model('category')->model_info($mid);
		$url=model('category')->url_format($model_info['url_category_page'],$cid,$info['urlname']);
		if ($mid==2){
			if(empty($info['content'])){
				$info['content']='暂无内容';
			}
        	$info['content']=html_out($info['content']);
			//读取内容替换
			$content=model('content')->format_content($info['content']);
			$page = new Page();
			$content = $page->contentPage(html_out($content),"<hr class=\"ke-pagebreak\" />",
			$url, 10, 4); //文章分页
			$info['content']=$content['content'];
			$this->page=$content['page'];
		}elseif ($mid==1){
			//设置分页
			$size = intval($info['page']); 
			if (empty($size)) {
				$listrows = 10;
			} else {
				$listrows = $size;
			}
			$model_info = model('category')->model_info($info['mid']);
			$url=model('category')->url_format($model_info['url_category_page'],$cid,$info['urlname']);
			$limit=$this->pagelimit($url,$listrows);
	
			//设置栏目属性
			if ($info['type'] == 0) {
				$son_id = model('category')->getcat($info['cid']);
				$where = 'A.status=1 AND B.cid in (' . $son_id . ')';
			} else {
				$where = 'A.status=1 AND B.cid=' . $info['cid'];
			}
			//执行查询
			$this->loop=model('category')->content_list($cid,$where,$limit,$info['content_order']);
			$count = model('category')->content_count($cid,$where);
			//查询下级栏目信息
			$this->get_category = model('category')->get_list($cid);
			if (!$this->get_category) {
				$this->get_category = array("cid" => "0","pid" => "0","mid" => "0","name" => "无下级栏目");
			}
			//获取分页
			$this->page=$this->page($url, $count, $listrows);
			//获取上一页代码
			$this->prepage=$this->page($url, $count, $listrows,'',1);
			//获取下一页代码
			$this->nextpage=$this->page($url, $count, $listrows,'',2);
			$this->count=$count;
		}else{
			$this->error404();	
		}
        /*hook*/
        $this->plus_hook('category','index',$info);
        $info=$this->plus_hook_replace('category','index_replace',$info);
        /*hook end*/
        //位置导航
        $this->nav=array_reverse(model('category')->nav($info['cid']));
        //查询上级栏目信息
        $this->parent_category = model('category')->info($info['pid']);
        if (!$this->parent_category) {
            $this->parent_category = array("cid" => "0","pid" => "0","mid" => "0","name" => "无上级栏目");
        }
		$nav['id']=$info['cid'];
		$nav['pid']=empty($info['pid'])?20:$info['pid'];
        switch ($nav['id']) {
            case 2:
               $nav['aq']=" class='aon'";
                break;
            case 3:
               $nav['zl']=" class='aon'";
                break;
            case 4:
               $nav['jc']=" class='aon'";
                break;
            case 5:
               $nav['news']=" class='aon'";
                break;
            case 10:
               $nav['fg']=" class='aon'";
                break;
            case 8:
               $nav['bs']=" class='aon'";
                break;
            case 1:
               $nav['about']=" class='aon'";
                break;
            case 6:
               $nav['tz']=" class='aon'";
                break;
            case 9:
               $nav['bg']=" class='aon'";
                break;
      		default:
               $nav['home']=" class='aon'";
				break;
		}
        $this->navs = $nav;
        $this->info = $info;
        //MEDIA信息
        $this->common=model('pageinfo')->media($info['name'],$info['keywords'],$info['description']);
        
        //获取顶级栏目信息
        $this->top_category = model('category')->info($this->nav[0]['cid']);
        $this->display($info['class_tpl']);
        if ( $this->config['HTML_CACHE_ON'] ) {
           HtmlCache::write();
        }
		exit;
    }




}

?>