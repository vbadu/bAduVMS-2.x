<?php
class formMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }
    //查看
    public function info(){
    	//dump($_GET);
		//echo "<pre>";
    	$table=in($_GET[0]);
		$id=in($_GET[1]);
		if (strlen($table)<3) $this->error404();
        $this->form_info=model('form')->info($table);
		if (is_array($this->form_info)){
			$fid=$this->form_info['id'];
			$this->field_list=model('form')->field_list($fid);
			$this->info=model('form')->info_table($id,$this->form_info['table']);
			//dump($this->form_info);
			//导航
			$url=__INDEX__."/".$table.'/';
			$this->nav=array(
				0=>array('name'=>$this->form_info['name'],'url'=>$url),
			);
			$tpl=empty($this->form_info['tpl_page'])?'page_'.$this->form_info['tpl']:$this->form_info['tpl_page'];
			$this->show($tpl);
		}else{
			$this->error404();	
		}
    }

    public function index()
    {
    	$form=substr(in(urldecode($_GET['f'])),0,15);
    	$this->get=in($_GET);
    	if(empty($form)){
    		$this->error404();
    	}
    	//获取表单信息
    	$info=model('form')->info($form);
    	if(empty($info)||$info['display']==0){
    		$this->error404();
    	}
        /*hook*/
        $this->plus_hook('form','index',$info);
        /*hook end*/
        $this->field_list=model('form')->field_list($info['id']);
    	//分页处理
        $url=__INDEX__.'/'.$form.'/pages-{page}.html';

        $listrows = $info['page'];
        $limit=$this->pagelimit($url,$listrows);
        //导航
        $this->nav=array(
            0=>array('name'=>$info['name'],'url'=>__INDEX__.'/'.$form.'/'),
        );
        //内容列表
        $this->loop=model('form')->form_list($info['table'],$limit,$info['order'],$info['where']);
        //统计总内容数量
        $count=model('form')->form_count($info['table'],$info['where']);
        //分页处理
        $this->page=$this->page($url, $count, $listrows);

        $this->info=$info;
        $this->listrows=$info['page'];
        $this->cur_page=$cur_page;
        //MEDIA信息
        $this->common=model('pageinfo')->media($info['name']);
        //判断使用模板
        if($info['alone_tpl']==0){
        $this->show($info['tpl']);
        }else{
        $this->display($info['tpl']);
        }
    }

    //提交表单
    public function post()
    {
    	$form=in($_POST['form']);
    	if(empty($form)){
    		$this->error404('提交内容不能为空;');
    	}
    	$info=model('form')->info($form);
    	if(empty($info)||$info['display']==0){
    		$this->error404($form.'表单不存在;');
    	}
    	//获取所有字段
    	$field_list=model('form')->field_list($info['id']);
    	if(empty($field_list)){
            if($info['return_type']){
                $this->msg('未发现表单信息！',0);
            }else{
                $this->alert('未发现表单信息！');
            }
        }

        //过滤机器信息
        if(!empty($_POST['test_value'])){
        	return;
        }

        $data=array();
        foreach ($field_list as $value) {
            if($value['must']==1){
                if(empty($_POST[$value['field']])){
                    if($info['return_type']){
                        $this->msg($value['name'].'不能为空！',0);
                    }else{
                        $this->alert($value['name'].'不能为空！');
                    }
                }
            }
            $data[$value['field']]=model('form')->field_in($_POST[$value['field']],$value['type'],$value['field']);
            if(!isset($_POST[$value['field']])){
                $data[$value['field']]=$value['default'];
            }
        }

        if ($_POST['checkcode'] != $_SESSION['verify']) {
            if($info['return_type']){
                $this->msg('验证码错误!',0);
            }else{
                $this->alert('验证码错误!');
            }
            return;
        }

        //过滤完后提交表单
        model('form')->add($data,$form);
        model('cache')->clear_html('/'.$form);
        if($info['return_url']){
            $url=$this->display($info['return_url'],true,false);
        }else{
            $url=__INDEX__.'/'.$form;
        }
        if($info['return_type']){
            $this->msg($info['return_msg']);
        }else{
            $this->alert($info['return_msg'],$url);
        }
        
    }

    //验证码
    public function verify(){
        Image::buildImageVerify();
    }


}

?>