<?php
class contentMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $aid = intval($_GET['aid']);
        if (empty($aid)) {
            $this->error404();
        }
        $info=model('content')->info($aid);
        if (!is_array($info)||$info['status']==0) {
            $this->error404();
        }
        //判断跳转
        if (!empty($info['url']))
        {
            $link=$this->display(html_out($info['url']),true,false);
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$link."");
			exit;
        }
        //查询栏目的信息
        $this->category = model('category')->info($info['cid']);
        $model_info = model('category')->model_info($this->category['mid']);
        //位置导航
        $this->nav=array_reverse(model('category')->nav($this->category['cid']));
        //查询上级栏目信息
        $this->parent_category = model('category')->info($this->category['pid']);
        if (!$this->parent_category) {
            $this->parent_category = array( "cid" => "0","pid" => "0","mid" => "0","name" => "无上级栏目");
        }

        //获取顶级栏目信息
        $this->top_category = model('category')->info($this->nav[0]['cid']);

        //更新访问计数
        model('content')->views_content($info['aid'],$info['views']);
        
        //读取内容信息
        $info_content=model('content')->info_content($info['aid']);
		if(empty($info_content['content'])){
			$info_content['content']='暂无内容';
		}
        $content=$info_content['content'];

        //读取内容替换
        if(!empty($content)){
            $content=model('content')->format_content($content);
        }
        //自动增加TAG链接
        if(!empty($content)&&$info['taglink']){
            $content=model('content')->tag_link($content,$info['aid']);
        }

        //MEDIA信息
        $this->common=model('pageinfo')->media($info['title'].' - '.$this->category['name'],$info['keywords'],$info['description']);
        
        //内容分页
        $url=model('content')->url_format($model_info['url_content_page'],$aid,$this->category['urlname'],$info);
        $page = new Page();
        $content = $page->contentPage(html_out($content), "<hr class=\"ke-pagebreak\" />",
        $url, 10, 4); //文章分页

        $info['content']=$content['content'];
        $this->page=$content['page'];

        /*hook*/
        $this->plus_hook('content','index',$this->info);
        $this->info=$this->plus_hook_replace('content','index_replace',$this->info);
        /*hook end*/
        
        $this->info=$info;
        //上下篇
        $prev=model('content')->prev_content($aid,$this->category['cid'],$this->category['expand']);
        $this->assign('prev', $prev);
        //下一篇
        $next=model('content')->next_content($aid,$this->category['cid'],$this->category['expand']);
        $this->assign('next', $next);
        if(empty($info['tpl'])){
        $this->display($this->category['content_tpl']);
        }else{
        $this->display($info['tpl']);
        }
		if ( $this->config['HTML_CACHE_ON'] ) {
			HtmlCache::write();
		}
    }
}
