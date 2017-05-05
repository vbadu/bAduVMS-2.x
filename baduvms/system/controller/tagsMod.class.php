<?php
//tag显示
class tagsMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
    }

    public function index() {

        //分页处理
        $url=__INDEX__.'/tags-pages-{page}.html';

        $listrows = $this->config['TPL_TAGS_INDEX_PAGE'];
        if(empty($listrows)){
            $listrows=20;
        }
        $limit=$this->pagelimit($url,$listrows);

        $nav=array(
            0=>array('name'=>'TAG','url'=>__INDEX__.'/tags-index'),
        );

        $this->info=array('name'=>'TAG 列表');

        //MEDIA信息
        $this->common=model('pageinfo')->media('TAGS 列表',$tag);

        //内容列表
        $loop=model('tags')->tag_index_list($limit);

        //统计总内容数量
        $count=model('tags')->tag_index_count();
        //分页处理
        $this->page=$this->page($url, $count, $listrows);
        //获取上一页代码
        $this->prepage=$this->page($url, $count, $listrows,'',1);
        //获取下一页代码
        $this->nextpage=$this->page($url, $count, $listrows,'',2);

        $this->assign('loop',$loop);
        $this->assign('nav',$nav);
        $this->display($this->config['TPL_TAGS_INDEX']);  
    }


	public function info() {
        $tag=urldecode($_GET['tag']);
        if(!is_utf8($tag))
        {
            $tag=auto_charset($tag,'gbk','utf-8');
        }
        
        $tag = msubstr(in($tag),0,20);
        //查找tag信息
        if(!empty($tag)){
            $info=model('tags')->tag_info($tag);
        }else{
            $this->error404();
        }

        if(empty($info)){
            $this->error404();
        }

        //更新点击计数
        model('tags')->views_content($info['id'],$info['click']);

        /*hook*/
        $this->plus_hook('tags','index',$info);
        /*hook end*/

        //分页处理
        $url=__INDEX__.'/tags-'.$tag.'-pages-{page}.html';

        $listrows = $this->config['TPL_TAGS_PAGE'];
        if(empty($listrows)){
            $listrows=20;
        }
        $limit=$this->pagelimit($url,$listrows);

        $nav=array(
            0=>array('name'=>'TAG','url'=>__INDEX__.'/tags-index'),
            1=>array('name'=>$tag,'url'=>__INDEX__.'/tags-'.$tag.'/'),
            );

        //MEDIA信息
        $this->common=model('pageinfo')->media($info['name'].' - TAGS',$tag);

        //内容列表
        $loop=model('tags')->tag_list($info['id'],$limit);

        //统计总内容数量
        $count=model('tags')->tag_count($info['id']);
        //分页处理
        $this->page=$this->page($url, $count, $listrows);
        //获取上一页代码
        $this->prepage=$this->page($url, $count, $listrows,'',1);
        //获取下一页代码
        $this->nextpage=$this->page($url, $count, $listrows,'',2);

		$this->assign('loop',$loop);
        $this->assign('nav',$nav);
        $this->assign('info', $info);
		$this->display($this->config['TPL_TAGS']);  
	}
	

	

}