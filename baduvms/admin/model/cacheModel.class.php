<?php
//缓存处理
class cacheModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //删除模板缓存
    public function clear_tpl()
    {
    	$dir=__ROOTDIR__.'/data/tpl_cache/';
        return del_dir($dir);
    }

    //删除html缓存
    public function clear_html()
    {
    	$dir=__ROOTDIR__.'/data/html_cache/';
        return del_dir($dir);
    }

    //删除数据缓存
    public function clear_data()
    {
    	$dir=__ROOTDIR__.'/data/db_cache/';
        return del_dir($dir);
    }



    //删除所有缓存
    public function clear_all()
    {
/*    	$this->clear_tpl();
    	$this->clear_html();
        $this->clear_data();
*/        
        del_dir(__ROOTDIR__.'/data/tpl_cache/');
        del_dir(__ROOTDIR__.'/data/html_cache/');
        del_dir(__ROOTDIR__.'/data/db_cache/');
        del_dir(__ROOTDIR__.'/data/tpl_cache/');
         return  $this->cache->clear();


    }

}