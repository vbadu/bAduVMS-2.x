<?php
class categoryModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }
    //获取列表
    public function select($where=null,$limit=10,$order="id desc",$tree=false,$pid = 0){
        $data=$this->model->table('category')->where($where)->limit($limit)->order($order)->select();
        if ($tree){
			$cat = new Category(array('cid', 'pid', 'name', 'cname'));
			return $cat->getTree($data, $pid);
		}
        return $data;
    }
	public function count($where=null) {
        return $this->model->table('category')->where($where)->count();
	}
    //获取栏目树形列表
    public function category_list($model=1,$id = 0) {
        $sql="SELECT * FROM {$this->model->pre}category WHERE model={$model} ORDER BY sequence DESC,cid ASC";
        $data=$this->model->query($sql); 
        $cat = new Category(array('cid', 'pid', 'name', 'cname'));
        $data= $cat->getTree($data, $id);
		return $data;
    }
	//获取栏目树形列表
    public function category_model_list($model=1,$id = 0) {
        $sql="
        SELECT A.*,B.model,B.name as mname
        FROM {$this->model->pre}category A 
        LEFT JOIN {$this->model->pre}model B ON A.mid = B.mid
		WHERE A.model={$model}
        ORDER BY A.sequence DESC,A.cid ASC
        ";
        $data=$this->model->query($sql); 
        $cat = new Category(array('cid', 'pid', 'name', 'cname'));
        $data= $cat->getTree($data, $id);
		return $data;
    }
    //获取子栏目统计
    public function list_count($pid)
    {
        return $this->model->table('category')->where('pid='.$pid)->count();
    }

    //获取所有栏目统计
    public function category_count()
    {
        return $this->model->table('category')->count();
    }
    public function getall($id)
    {
        return $this->model->field('cid')->table('category')->where('model='.$id)->select();
    }
    public function getid($id)
    {
        return $this->model->field('cid')->table('category')->where('pid='.$id)->select();
    }
    public function getModelId($id)
    {
        return $this->model->field('mid')->table('category')->where('cid='.$id)->select();
    }
    //获取栏目基本信息
    public function info($id)
    {
        return $this->model->table('category')->where('cid='.$id)->find();
    }

    //字段保存格式化
    public function common_data_save($data){
        $cid=intval($data['cid']);
        //转换拼音栏目
        $data['urlname']=$this->get_urlname($data['name'],$data['urlname'],$cid);
        //获取语言信息
        $data['seo_content']=html_in($data['seo_content']);
		$data['content']=html_in($data['content']);
        return $data;

    }

    //栏目保存
    public function add_save($data)
    {   
        //格式化部分字段
        $data=$this->common_data_save($data);
        //录入数据
        $cid=$this->model->table('category')->data($data)->insert();
        model('upload')->relation('category',$data['file_id'],$cid);
        /*hook*/
        $this->plus_hook('category','add_data',$data);
        /*hook end*/
        return $cid;
    }

    //栏目保存
    public function edit_save($data)
    {
		//格式化部分字段
        $data=$this->common_data_save($data);
		//dump($data,1);
        //录入数据
        $status=$this->model->table('category')->data($data)->where('cid='.$data['cid'])->update();
        model('upload')->relation('category',$data['file_id'],$data['cid']);
        /*hook*/
        $this->plus_hook('category','edit_data',$data);
        /*hook end*/
        return $status;
    }

    //栏目删除
    public function del($cid)
    {
        /*hook*/
        $this->plus_hook('category','del_data',$cid);
        /*hook end*/
        $status=$this->model->table('category')->where('cid='.$cid)->delete(); 
        model('upload')->del_file('category',$cid);
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
        
        $info = $this->model->table('category')->where("urlname='".$urlname."'" .$where)->count(); 

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
        require __CONFIG__;
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


    public function sequence($cid,$sequence){
        $data['sequence']=$sequence;
        return $this->model->table('category')->data($data)->where('cid='.$cid)->update();
        
    }

}

?>