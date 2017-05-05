<?php
class emptyMod extends commonMod
{

    public function _empty()
    {
        header("content-type:text/html; charset=utf-8");
        //执行插件部分
        $module_name = in($_GET['_module']);
        $action_name = in($_GET['_action']);
        $plugin_info=$this->model->table('plugin')->where('file="'.$module_name.'"')->find(); 
        if(!empty($plugin_info)){
            if($plugin_info['status']==1){
                if (Plugin::run($module_name, $action_name)) {
                    return;
                }
            }
        }
        //处理根目录
        $root=substr(str_replace('/','\/', __ROOT__),1);
        if(empty($root)){
            $root='/';
        }
        //过滤URL
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url=str_replace('index.php/','', $url['path']);
        $url=str_replace('index.php','',$url);
        $url=str_replace('//','/',$url);
        $url=str_replace('///','/',$url);
        $url=explode('?',$url);
        $url=$url[0];
        $url=urldecode($url);
        if(substr($url, -10)=='index.html'){
            $url=substr($url, 0,-10);
        }
        $urls=$url;
        $urlm=__URL__;
        //表单URL
        $form_list=$this->model->table('form')->where('display=1')->select();
        
        if(substr($root, -1)<>'/'){
            $rooturl=$root.'/';
        }else{
            $rooturl=$root;
        }
        if(!empty($form_list)){
            foreach ($form_list as $value) {
				//echo $urlm.$rooturl.$value['table'];
                if($urlm==$rooturl.$value['table']||$urlm==$rooturl.$value['table'].'/'||$urlm==$rooturl.$value['table'].'/index.html'||$urlm==$rooturl.$value['table'].'/pages.html'){
					model('rewrite')->form_url($value['table']);
					return;
                }
                preg_match($root.'\/'.$value['table'].'\/pages-(\d+).html/i',$urls,$match);
                if($match[0]==$url&&!empty($match)){
                    model('rewrite')->form_url($value['table'],$match[1]);
                    return;
                }
            }
        }

        //处理TAGURL
        $tagurl=explode('-', urldecode($module_name));
        if($tagurl[0]=='tags'){
            $tag=str_replace('tags-','', urldecode($module_name));
            if($urls==$rooturl.'tags-'.$tag||$urls==$rooturl.'tags-'.$tag.'/'||$urls==$rooturl.'tags-'.$tag.'/index.html'||$urls==$rooturl.$tag.'/pages.html'){
                    model('rewrite')->tags_url($tag);
                    return;
            }
            preg_match($root.'\/tags-'.$tag.'\/pages-(\d+).html/i',$urls,$match);
            if($match[0]==$url&&!empty($match)){
                model('rewrite')->tags_url($tag,$match[1]);
                return;
            }
            
        }
		
        $mod_list=$this->model->table('model')->select();

        foreach ($mod_list as $value) {
			$pageurl=explode('/',$url);
			//dump(count($pageurl));
			if (count($pageurl)>2){
				$this->page_url($root,$url,$value);
				return;
			}else{
				$this->cat_url($root,$url,$value);
				return;
			}
        }
        $this->error404();

    }
	public function cat_url($root,$url,$value){
	    //栏目列表规则
	    $url_catrgory=$value['url_category'];
	    $url_catrgory_page=$value['url_category_page'];
        //栏目列表替换
        $patterns =array(".","-","{EXT}","{CDIR}","{P}",);
        $replacements=array("\.","-",str_replace('.','\.','.html'),"([a-z0-9_\-]+)","(\d+)");
		
        if(substr($url, -1)<>'/'){
            //$url=$url.'-';
        }
        if(substr($url_catrgory, -1)<>'/'){
            //$url_catrgory=$url_catrgory.'-';
        }

        //处理栏目URL规则
        $url_catrgory=str_replace($patterns,$replacements,$url_catrgory);
        $url_catrgory_page=str_replace($patterns,$replacements,$url_catrgory_page);

        //判断栏目分页规则
        $url=$url;
        preg_match($root.'\/'.$url_catrgory_page.'/i',$url,$match);
        if(substr($match[0], 0,1)<>'/'){
           $match[0]='/'.$match[0];
        }
        if($match[0]==$url){
            model('rewrite')->category($match[1],$match[2]);
            return;
        }
        unset($match);
        //判断栏目规则
        preg_match($root.'\/'.$url_catrgory.'/i',$url,$match);
        if(substr($match[0], 0,1)<>'/'){
           $match[0]='/'.$match[0];
        }
        if($match[0]==$url){
            model('rewrite')->category($match[1]);
            return;
        }
        unset($match);
        $this->error404('<b>该网页链接已失效。</b>');
        return;
	}
	public function page_url($root,$url,$value){
        //内容列表规则
        $url_content=$value['url_content'];
        $url_content_page=$value['url_content_page'];
        //内容替换
        $patterns2 =array(".", "/", "{EXT}","{CDIR}","{P}","{YY}","{YYYY}","{M}","{D}","{AID}","{URLTITLE}",);
        $replacements2=array("\.","\/",str_replace('.','\.','.html'),"[a-z0-9_\-]+","(\d+)","\d{2}","\d{4}","\d{2}","\d{2}","(\d+)","([a-z0-9_\-]+)",);
        //处理内容URL规则
        $url_content=str_replace($patterns2,$replacements2,$url_content);
        $url_content_page=str_replace($patterns2,$replacements2,$url_content_page);
        $url=$url;
        //判断内容分页规则
        preg_match($root.'\/'.$url_content_page.'/i',$url,$match);
       if(substr($match[0], 0,1)<>'/'){
           $match[0]='/'.$match[0];
        }
        if($match[0]==$url){
            model('rewrite')->content($match[1],end($match));
            return;
        }
        unset($match);
        //判断内容规则
        preg_match($root.'\/'.$url_content.'/i',$url,$match);
        if(substr($match[0], 0,1)<>'/'){
           $match[0]='/'.$match[0];
        }
        
        if($match[0]==$url){
            model('rewrite')->content($match[1]);
            return;
        }
        unset($match);	
	}

    
}?>