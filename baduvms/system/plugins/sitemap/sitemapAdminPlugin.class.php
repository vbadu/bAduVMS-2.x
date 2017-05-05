<?php

class sitemapAdminPlugin extends common_pluginMod
{
    public function __construct()
    {
        $_GET['_module']='sitemap';
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('sitemap',true);
    }
    
    //插件附加表信息
    public function plugin_ini_data()
    {
        return array(
            'plugin_sitemap',
        );
    }

    //首页    
    public function index()
    {
        //模板内赋值		
        $this->info=$this->status();
        $this->show('admin_index.html');
    }
    //获取状态信息
    public function status(){
        return $this->model->table('plugin_sitemap')->where('name="status"')->find();
    }
    //保存配置
    public function config_save()
    {
        //模板内赋值
        $data['config']=$_POST['status'];
        $this->model->table('plugin_sitemap')->data($data)->where('name="status"')->update();
        $this->msg('配置保存成功！');
    }

    //内容添加编辑删除接口
    public function sitemap_data(){
        $info=$this->status();
        if($info['config']){
            $this->baidu_data();
            $this->google_data();
        }
    }
    public function hook_content_add(){
        $this->sitemap_data();
    }
    public function hook_content_edit(){
        $this->sitemap_data();
    }
    public function hook_content_del(){
        $this->sitemap_data();
    }

    //生成百度地图
    public function baidu()
    {
        if($this->baidu_data()){
            $this->msg('百度地图生成成功！');
        }else{
            $this->msg('百度地图生成失败！请检查根目录是否有写入权限');
        }
    }

    public function baidu_data()
    {
        require_once(APPS_PATH.'controller/labelMod.class.php');
        $module=new labelMod;
        $list=$this->model->table('content')->limit(200)->where('status=1')->order('updatetime desc')->select();
        $category_list=$this->model->table('category')->limit(200)->order('cid asc')->select();
        $html='<?xml version="1.0" encoding="UTF-8"?>
        <urlset>';
        $html.='
        <url>
        <loc>'.'http://'.$_SERVER['HTTP_HOST'].ROOTAPP.'</loc>
        <lastmod>'.date('Y-m-d').'</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        </url>';
        if(!empty($category_list)){
            foreach ($category_list as $value) {
                $html.='
                <url>
                <loc>'.$module->get_curl($value['cid'],'http://'.$_SERVER['HTTP_HOST'].ROOTAPP).'</loc>
                <lastmod>'.date('Y-m-d').'</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.9</priority>
                </url>';
            }
        }
        if(!empty($list)){
            foreach ($list as $value) {
                $html.='
                <url>
                <loc>'.$module->get_aurl($value['aid'],'http://'.$_SERVER['HTTP_HOST'].ROOTAPP).'</loc>
                <lastmod>'.date('Y-m-d',$value['updatetime']).'</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
                </url>';
            }
        }
        $html.='
        </urlset>
        ';
        $file=__ROOTDIR__.'/baidu_sitemap.xml';
        @file_put_contents($file, $html);
        if(!file_exists($file)){
            return false;
        }
        return true;
    }

    //生成google地图
    public function google()
    {
        if($this->google_data()){
            $this->msg('google地图生成成功！');
        }else{
            $this->msg('google地图生成失败！请检查根目录是否有写入权限');
        }
    }

    public function google_data()
    {
        require_once(APPS_PATH.'controller/labelMod.class.php');
        $module=new labelMod;
        $list=$this->model->table('content')->limit(200)->where('status=1')->order('updatetime desc')->select();
        $category_list=$this->model->table('category')->limit(200)->order('cid asc')->select();
        $html='<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $html.='
        <url>
        <loc>'.'http://'.$_SERVER['HTTP_HOST'].ROOTAPP.'</loc>
        <lastmod>'.date("Y-m-d\TH:i:s").'</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
        </url>';
        if(!empty($category_list)){
            foreach ($category_list as $value) {
                $html.='
                <url>
                <loc>'.$module->get_curl($value['cid'],'http://'.$_SERVER['HTTP_HOST'].ROOTAPP).'</loc>
                <lastmod>'.date('Y-m-d').'</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.9</priority>
                </url>';
            }
        }
        if(!empty($list)){
            foreach ($list as $value) {
                $html.='
                <url>
                <loc>'.$module->get_aurl($value['aid'],'http://'.$_SERVER['HTTP_HOST'].ROOTAPP).'</loc>
                <lastmod>'.date("Y-m-d\TH:i:s",$value['updatetime']).'</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.8</priority>
                </url>';
            }
        }
        $html.='
        </urlset>
        ';
        $file=__ROOTDIR__.'/sitemap.xml';
        @file_put_contents($file, $html);
        if(!file_exists($file)){
            return false;
        }
        return true;
    }

    //生成html地图
    public function html_sitemap()
    {
        if($this->html_sitemap_data()){
            $this->msg('html地图生成成功！');
        }else{
            $this->msg('html地图生成失败！请检查根目录是否有写入权限');
        }
    }

    public function html_sitemap_data()
    {
        require_once(APPS_PATH.'controller/labelMod.class.php');
        $module=new labelMod;
        $list=$this->model->table('content')->limit(200)->where('status=1')->select();
$html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>站点地图 - '.$this->config['sitename'].'</title>
<meta name="keywords" content="'.$this->config['keywords'].'" />
<meta name="generator" content="Baidu SiteMap Generator" />
<style type="text/css">
body { font-family: Verdana; FONT-SIZE: 12px; MARGIN: 0; color: #000000; background: #ffffff; }
img { border:0; }
li { margin-top: 8px; }
.page { padding: 4px; border-top: 1px #EEEEEE solid }
.author { background-color:#EEEEFF; padding: 6px; border-top: 1px #ddddee solid }
#nav,
#content,
#footer { padding: 8px; border: 1px solid #EEEEEE; clear: both; width: 95%; margin: auto; margin-top: 10px; }
</style>
</head>
<body vlink="#333333" link="#333333">
<h2 style="text-align: center; margin-top: 20px">'.$this->config['sitename'].'\'s SiteMap </h2>
<center>
</center>
<div id="nav"><a href="http://'.$_SERVER['HTTP_HOST'].ROOTAPP.'"><strong>'.$this->config['seoname'].'</strong></a> &raquo; <a href="/sitemap.html">站点地图</a></div>
<div id="content">
    <h3>最新文章</h3>
    <ul>
        ';
        if(!empty($list)){
        foreach ($list as $value) {
        $html.='<li><a href="'.$module->get_aurl($value['aid'],'http://'.$_SERVER['HTTP_HOST'].ROOTAPP).'" title="'.$value['title'].'" target="_blank">'.$value['title'].'</a></li>';
        }
        }
        $html.='
</ul>
</div>
<div id="footer">网站首页: <strong><a href="http://'.$_SERVER['HTTP_HOST'].ROOTAPP.'">'.$this->config['sitename'].'</a></strong></div>
<br />
<center>
    <div style="text-algin: center; font-size: 11px"><strong>Baidu-SiteMap</strong> &nbsp;
        Latest Update: '.date('Y-m-d H:i:s').'<br />
        <br />
    </div>
</center>
</body>
</html>
        ';
        $file=__ROOTDIR__.'/sitemap.html';
        @file_put_contents($file, $html);
        if(!file_exists($file)){
            return false;
        }
        return true;
    }

    //生成robots.txt地图
    public function robots()
    {
        if($this->robots_data()){
            $this->msg('robots.txt生成成功！');
        }else{
            $this->msg('robots.txt生成失败！请检查根目录是否有写入权限');
        }
    }

    public function robots_data()
    {

$html='User-agent: *
Disallow: /system/
Disallow: /data/
Disallow: /themes/
Disallow: /system/
Disallow: /install/
Disallow: /public/

Sitemap: http://'.$_SERVER['HTTP_HOST'].__ROOTURL__.'/sitemap.xml
Sitemap: http://'.$_SERVER['HTTP_HOST'].__ROOTURL__.'/baidu_sitemap.xml
';
        $file=__ROOTDIR__.'/robots.txt';
        @file_put_contents($file, $html);
        if(!file_exists($file)){
            return false;
        }
        return true;
    }



}

?>