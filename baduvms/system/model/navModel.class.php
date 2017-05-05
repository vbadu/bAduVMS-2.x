<?php
class navModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取栏目树形列表
    public function get_list($type=0,$field = '*') {
		return $this->model->field($field)->table('nav')->where('type='.$type)->select();
    }
    //获取子栏目统计
    public function get_count($where)
    {
        return $this->model->table('nav')->where($where)->count();
    }

    //获取子栏目统计
    public function list_count($pid)
    {
        return $this->model->table('nav')->where('pid='.$pid)->count();
    }

    //获取所有栏目统计
    public function nav_count()
    {
        return $this->model->table('nav')->count();
    }

    //获取内容基本信息
    public function getid($id)
    {
        return $this->model->field('cid')->table('nav')->where('pid='.$id)->select();
    }
    //获取内容基本信息
    public function getall($id)
    {
        return $this->model->field('cid')->table('nav')->where('type='.$id)->select();
    }

    //获取栏目基本信息
    public function info($id)
    {
        return $this->model->table('nav')->where('cid='.$id)->find();
    }

    //字段保存格式化
    public function common_data_save($data){
        $cid=intval($data['cid']);
        //转换拼音栏目
        $data['urlname']=$this->get_urlname($data['name'],$data['urlname'],$cid);
        //获取语言信息
        $data['seo_content']=html_in($data['seo_content']);
        return $data;

    }

    //栏目保存
    public function add_save($data)
    {   
        //录入数据
        $cid=$this->model->table('nav')->data($data)->insert();
        return $cid;
    }

    //栏目保存
    public function edit_save($data)
    {
        //录入数据
        $status=$this->model->table('nav')->data($data)->where('cid='.$data['cid'])->update();
        model('upload')->relation('nav',$data['file_id'],$data['cid']);
        /*hook*/
        $this->plus_hook('nav','edit_data',$data);
        /*hook end*/
        return $status;
    }

    //栏目删除
    public function del($cid)
    {
        /*hook*/
        $this->plus_hook('nav','del_data',$cid);
        /*hook end*/
        $status=$this->model->table('nav')->where('cid='.$cid)->delete(); 
        model('upload')->del_file('nav',$cid);
        return $status;
    }

    //获取栏目拼音
    public function get_urlname($name='', $urlname = null, $cid = null)
    {
        if(empty($name)){
            return false;
            exit;
        }
        if (empty($urlname))
        {
            $pinyin = new Pinyin();
            $name = preg_replace('/\s+/', '-', $name);
            $pattern = '/[^\x{4e00}-\x{9fa5}\d\w\-]+/u';
            $name = preg_replace($pattern, '', $name);
            $urlname = substr($pinyin->output($name, true),0,30);
            if(substr($urlname,0,1)=='-'){
                $urlname=substr($urlname,1);
            }
            if(substr($urlname,-1)=='-'){
                $urlname=substr($urlname,0,-1);
            }
        }

        $where='';
        if (!empty($cid))
        {
            $where = 'AND cid<>' . $cid;
        }
        
        $info = $this->model->table('nav')->where("urlname='".$urlname."'" .$where)->count(); 

        if (empty($info))
        {
            return $urlname;
        }
        else
        {
            return $urlname.substr(cp_uniqid(),8);
        }
    }

    //获取模板列表
    public function tpl_list()
    {
        require (__ROOTDIR__ . '/data/config.php');
        $tpl_dir=__ROOTDIR__.'/'.$config['TPL_TEMPLATE_PATH'];

        $list_file=glob($tpl_dir.'*.html');
        if(is_array($list_file)){
        foreach ($list_file as $value) {
            $array=explode('/', $value);
            $list[]=end($array);
        }
        }
        return $list;
    }


    public function sequence($cid,$sort){
        $data['sort']=$sort;
        return $this->model->table('nav')->data($data)->where('cid='.$cid)->update();
        
    }
}

?>