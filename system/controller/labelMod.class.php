<?php
class labelMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
    }

    //解析循环标签
	public function getlist($data) {
        if(empty($data)){
            return;
        }
		$data = stripslashes($data);
		preg_match_all('/([a-z0-9_]+)=[\"|\'](.*)[\"|\']/iU', $data, $matches);
		$label = array_combine($matches[1], $matches[2]);
		$table=$label['table'];

		//转换标签
		$where=$this->get_where($label);

		switch ($table) {
			case 'content':
				return $this->get_content($where,$label);
				break;
			case 'category':
				return $this->get_category($where,$label);
				break;
            case 'tags':
                return $this->get_tags($where,$label);
                break;
			default:
				return $this->get_common($where,$label);
				break;
		}
	}

	//栏目获取
	public function get_category($where,$data) {

        $show='A.`show`=1';
        if(!empty($data['show'])){
            if($data['show']=='true'){
                $show=' A.`show`<>3 ';
            }
        }
		$condition=$show.$where['mid'].$where['cid'].$where['pid'].$where['type'].$where['where'];
        $sequence='desc';
        if(!empty($data['sequence'])){
            if($data['sequence']=='desc'){
                $sequence='desc';
            }
            if($data['sequence']=='asc'){
                $sequence='asc';
            }
        }
		return $this->model->table('category','A')->where($condition)->cache($where['cache'])->limit($where['limit'])->order('sequence '.$sequence.' , ' .$where['order'])->select();
	}

    //内容获取
    public function get_content($where,$data) {
        $condition='A.status=1'.$where['mid'].$where['cid'].$where['image'].$where['position'].$where['where'];
        //获取推荐位
        if($where['position']){
            $position="LEFT JOIN {$this->model->pre}position_relation R ON R.aid = A.aid LEFT JOIN {$this->model->pre}position P ON P.id = R.pid";
        }
        //获取扩展模型
        $data['cid']=intval($data['cid']);
        if(is_int($data['cid'])||!empty($data['expand'])){
            if(empty($data['expand'])){
                $category=model('category')->info($data['cid']);
                $expand_id=$category['expand'];
            }else{
                $expand_id=$data['expand'];
            }
        }
        if($where['order']<>'rand()'){
            if(substr($where['order'],1)<>'B'||substr($where['order'],1)<>'C'){
                $where['order']='A.'.$where['order'];
            }
        }
        //获取相关文章
        if(!empty($data['related'])){
            if(preg_match("/[^\d-., ]/",$data['related'])){
                $tag_array=explode(',', $data['related']);
                $tagid='';
                if(!empty($tag_array)){
                    foreach ($tag_array as $value) {
                        $tag=model('tags')->tag_info($value);
                        if(!empty($tag)){
                        $tagid.=$tag['id'].',';
                        }
                        unset($tag);
                    }
                }
            }else{
                $tag_array=model('tags')->tags_relation_aid($data['related']);
                if(!empty($tag_array)){
                    $tagid='';
                    foreach ($tag_array as $value) {
                        $tagid.=$value['tid'].',';
                    }
                }

            }
            $tagid=substr($tagid,0,-1);
            if(!empty($tagid)){
                $tags_relation=model('tags')->tags_relation_tid($tagid);
                if(!empty($tags_relation)){
                    $tags_aid='';
                    foreach ($tags_relation as $value) {
                        $tags_aid.=$value['aid'].',';
                    }
                    $tags_aid=substr($tags_aid, 0,-1);
                }
            }
            if(!empty($tags_aid)){
                $related=" AND A.aid in(".$tags_aid.") ";
                if(preg_match("/[^\d-., ]/",$data['related'])){
                    $related.=' AND A.aid<>'.$related_id;
                }
            }else{
                return ;
            }
        }

        $loop="
            SELECT {$expand_field}A.*,B.name as cname,B.subname as csubname,B.mid
             FROM {$this->model->pre}content A 
             LEFT JOIN {$this->model->pre}category B ON A.cid = B.cid
             {$position}
             {$expand}
             WHERE {$condition}{$related} ORDER BY {$where['order']} LIMIT {$where['limit']}
            ";
        return $this->model->query($loop);
    }

    //其他表获取
    public function get_common($where,$data)
    {
        $condition=@substr($where['where'], 4);
        $list=@$this->model->table($data['table'])->where($condition)->limit($where['limit'])->order($where['order'])->select();
        return $list; 
    }

    //获取数据统计
    public function get_count($data)
    {
        if(empty($data)){
            return;
        }
		$data = stripslashes($data);
		preg_match_all('/([a-z0-9_]+)=[\"|\'](.*)[\"|\']/iU', $data, $matches);
		$data = array_combine($matches[1], $matches[2]);
		$table=$data['table'];
        unset($data['table']);
		$condition=@$where=$data;
        //$condition=@substr($where, 4);
        $count=@$this->model->table($table)->where($condition)->count();
        return $count; 
    }

    //获取TAG列表
    public function get_tags($where,$data)
    {

        $condition=$where['aid'].$where['cid'].$where['where'];
        if($where['order']){
            $where['order']=$where['order'].', A.id desc';
        }else{
            $where['order']=' A.id desc';
        }
        $sql="
            SELECT A.*,B.aid,C.name as cname , count(distinct A.name)
            FROM {$this->model->pre}tags A 
            LEFT JOIN {$this->model->pre}tags_relation B ON A.id=B.tid
            LEFT JOIN {$this->model->pre}tags_category C ON A.cid=C.cid 
            WHERE 1=1 {$condition}
            GROUP BY name
            ORDER BY {$where['order']} 
            LIMIT {$where['limit']} 

            ";
        return $this->model->query($sql);
    }

    //解析万能表单
    public function get_form($data)
    {
        if(empty($data)){
            return;
        }
        //解析标签
        $data = stripslashes($data);
        preg_match_all('/([a-z0-9_]+)=\"(.*)\"/iU', $data, $matches);
        $label = array_combine($matches[1], $matches[2]);
        $table='form_data_'.$label['table'];

        //获取表单信息
        $list=$this->model->table($table)->where($label['where'])->limit($label['limit'])->order($label['order'])->select();
        return $list;
    }

    public function admin_aurl()
    {
        $aid=intval($_GET['aid']);
        if(!empty($aid)){
            $this->redirect($this->get_aurl($aid));
        }else{
            $this->error404();
        }
    }

    public function admin_curl()
    {
        $cid=intval($_GET['cid']);
        if(!empty($cid)){
            $this->redirect($this->get_curl($cid));
        }else{
            $this->error404();
        }
    }



	//栏目超链接
    public function get_curl($cid,$app='')
    {
            if(empty($app)){
                $app=__APP__;
            }
            $condition['cid']=$cid;
        	$info = $this->model->field('cid,mid,urlname')->table('category')->where($condition)->find();
            if(!empty($info)){
                $model= $this->model->field('url_category')->table('model')->where('mid='.$info['mid'])->find();
            	$url_catrgory=$model['url_category'];
            	$patterns =array(
            	"/{EXT}/",
            	"/{CDIR}/",
            	);
            	$replacements=array(
            	'.html',
            	$info['urlname'],
            	);
            	$url_catrgory=preg_replace($patterns,$replacements,$url_catrgory);
            }
        	return $app .'/'. $url_catrgory;
    }

    //内容超链接
    public function get_aurl($aid,$app='')
    {
            if(empty($app)){
                $app=__APP__;
            }
            $condition['aid']=$aid;
            $info = $this->model->table('content')->where($condition)->find();
            if(empty($info)){
                return;
            }
            $channel_info = $this->model->field('cid,mid,urlname')->table('category')->where('cid='.$info['cid'])->find();
            if(empty($channel_info)){
                return;
            }
            $model= $this->model->field('url_content')->table('model')->where('mid='.$channel_info['mid'])->find();
            if(empty($model)){
                return;
            }

            $url_content=$model['url_content'];
            $patterns =array(  
                "/{EXT}/",
                "/{CDIR}/", 
                "/{YY}/",
                "/{YYYY}/",
                "/{M}/",
                "/{D}/",
                "/{AID}/",
                "/{URLTITLE}/",
                );
            $replacements=array(  
                '.html',
                $channel_info['urlname'],
                date('y',$info['updatetime']),
                date('Y',$info['updatetime']),
                date('m',$info['updatetime']),
                date('d',$info['updatetime']),
                $info['aid'],
                $info['urltitle'],
                );
            $url_content=preg_replace($patterns,$replacements,$url_content);
            return $app .'/'. $url_content;
    }

    //表单超链接
    public function get_furl($name)
    {
        return __INDEX__ .'/'. $name.'/';
    }

    //TAG超链接
    public function get_turl($name)
    {
        return __INDEX__ .'/tags-'. $name.'/';
    }


	//转换条件
	public function get_where($get) {

		//基本条件
		$where['limit'] = $get['limit'];
        $where['order'] = $get['order'];

        //缓存
        if($get['cache']){
            $where['cache'] = $get['cache'];
        }

		//模型ID
		if ($get['mid']) {
            if($get['table']=='category'){
                $where['mid'] = ' AND A.mid=' . $get['mid']; 
            }else{
                $where['mid'] = ' AND B.mid=' . $get['mid']; 
            }
        }
        //栏目ID
        if ($get['cid']) {
            switch ($get['table']) {
                case 'category':
                    $where['cid'] = ' AND A.cid in(' . $get['cid'] . ')';
                    break;
                case 'content':
                    $where['cid'] = ' AND B.cid in(' . $get['cid'] . ')';
                    break;
                case 'tags':
                    $where['cid'] = ' AND C.cid in(' . $get['cid'] . ')';
                    break;
            }
        }
        
        //上级栏目ID
        if ($get['pid']) {
            $where['pid'] = ' AND A.pid in(' . $get['pid']. ')';
        }
        //调用所有次级栏目
        if ($get['type'] == 'sub'&&!empty($get['pid'])) {
            $where['pid'] = ' AND A.pid in (' . model('category')->getcat($get['pid']) . ')';
        }

        //获取次级栏目内容
        if ($get['type']=='sub'&&!empty($get['cid'])) {
            if($get['table']=='content'){
                $where['cid'] = " AND B.cid in (" . model('category')->getcat($get['cid']).")";
            }
        }

        //调用顶级栏目
        if ($get['type'] == 'top') {
            $where['pid'] = ' AND A.pid=0';
        }
        
        //判断图片是否显示
        if (!empty($get['image'])) {
            if($get['image']=='true')
            {
            $where['image'] = ' AND A.image<>"" ';
            }
            if($get['image']=='false')
            {
            $where['image'] = ' AND A.image is Null ';
            }
        }
        //判断推荐内容
        if ($get['position']) {
            $where['position'] = " AND P.id in(".in($get['position']).') ';
        }

        //调用栏目类型
        if ($get['att']=='list') {
            $where['type'] = ' AND A.type=1 ';            
        }

        if ($get['att']=='class') {
            $where['type'] = ' AND A.type=0 ';            
        }

        //调用附加条件
        if (!empty($get['where'])) {
            $where['where'] = ' AND '.$get['where'];
        }

        //随机排序
        if (!empty($get['rand'])) {
            unset($where['order']);
            $where['order'] = 'rand()';
        }

        //针对TAG
        if (!empty($get['aid'])) {
            $where['aid'] = ' AND B.aid ='.$get['aid'];
        }

        return $where;
	}

    //图片裁剪
    public function get_cutout($file=null,$size=null) {

        if(empty($file)||empty($size)){
            return $file;
        }

        if(!file_exists(__ROOTDIR__.$file)){
            return $file;
        }
		$ext=explode('.',$file);
        $ext=end($ext);
        $ext=strtolower($ext);
        if($ext<>'jpg'&&$ext&&'jpeg'&&$ext<>'png'&&$ext<>'gif'){
            return $file;
        }

        $size=explode(',', $size);
        if(empty($size[0])||empty($size[1])){
            return $file;
        }
        $width=intval($size[0]);
        $height=intval($size[1]);

        $file_name=substr($file, 0,-strlen('.'.$ext));
        $file_dir=__ROOTDIR__.$file_name.'_'.$width.'_'.$height.'_cutout'.'.'.$ext;
        $file_url=$file_name.'_'.$width.'_'.$height.'_cutout'.'.'.$ext;

        if(file_exists($file_dir)){
            return $file_url;
        }

        //判断原图
		$name=explode('/', $file);
        $name=end($name);
        if(substr($name,0,6)=='thumb_'){
            $old_size=@getimagesize($file_dir);
            if($old_size[0]<$width||$old_size[1]<$height){
                $old_name=substr($name,6);
                $file=substr($file, 0,-strlen($name)).$old_name;
            }
        }
        
        $thumb=@Image::thumb(__ROOTDIR__.'.'.$file,$file_dir,'',$width,$height,'',true);
        if($thumb){
            return $file_url;
        }else{
            return $file;
        }

    }



}