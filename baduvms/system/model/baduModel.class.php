<?php
class baduModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }
    //获取栏目树形列表
    public function category($model=1,$id = 0) {
		$model=intval($model);
		$id=intval($id);
        $sql="
        SELECT A.*,B.model,B.name as mname
        FROM {$this->model->pre}category A 
        LEFT JOIN {$this->model->pre}model B ON A.mid = B.mid
		WHERE A.model={$model}
        ORDER BY A.sequence DESC,A.cid ASC
        ";
        $data=$this->model->query($sql); 
        return $data;
    }
    //获取数据
    public function info($where,$table='vote',$field=NULL){
        return $this->model->table($table)->field($field)->where($where)->find();
    }
    //随机列表
    public function get_sum($id){
        $data=$this->model->field('sum(vote) as num')
                ->table('vote_list')
                ->where('vid='.$id)
                ->order('id asc')
                ->select();
        return $data;
    }
    //列表
    public function get_list($limit=null,$where=null,$table='vote',$order='id DESC'){
        return $this->model->table($table)->where($where)->limit($limit)->order($order)->select();
    }
    //统计
	public function get_count($where=null,$table='vote') {
        return $this->model->table($table)->where($where)->count();
	}
    //更新数据
	public function set_data($where=null,$data,$table='vote') {
        return $this->model->table($table)->data($data)->where($where)->update();
    }
    //更新数据
	public function del_data($where=null,$table='vote') {
        return $this->model->table($table)->where($where)->delete();
    }
	//提交数据
    public function in_data($data,$table='vote_data'){
        return $this->model->table($table)->data($data)->insert();
    }
	//提交数据
    public function QR($id,$path){
        echo $this->getQR($id,$path);
    }
    
    //远程存图
    public function get_remote_image($url){
        $value=in($url);
        if(empty($value)) return;
     	if(!defined('DIRX')) define('DIRX',DIRECTORY_SEPARATOR);
    	//文件路径
        $file_path =  getcwd(). DIRX.'upload'.DIRX.date('Y-m').DIRX.date('d').DIRX;

        //文件URL路径
        $file_url = __ROOT__ . '/upload/'.date('Y-m').'/'.date('d').'/';
        
        $milliSecond = date("dHis");
        if(!is_dir($file_path)) @mkdir($file_path,0777,true);
        /* PHP5.3以上适用
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeTipe = finfo_file($finfo, $file) ;
		echo $mimeTipe;
		finfo_close($finfo); 
        $ext=explode('.', $value);
        $ext=end($ext);
		*/
		$ext=$this->check_image_mime($value);

        $get_file = @Http::doGet($value,5);
        //物理路径
        $filepath = $file_path.$milliSecond.$key.'.'.$ext;
        $thumb_filepath = $file_path.DIRX.'thumb_'.$milliSecond.$key.'.'.$ext;
        //访问路径
        $fileurl = $file_url.$milliSecond.$key.'.'.$ext;
        $thumb_fileurl = $file_url.'/thumb_'.$milliSecond.$key.'.'.$ext;
        if($get_file){
            $status=@file_put_contents($filepath, $get_file);
            if($status){
				$filesize=abs(filesize($filename));
				if(212000>$filesize){ //小于200K
					$file[1]=$fileurl;
				}else{
	              	//设置高度和宽度
	              	$thumbwidth=540;
	              	$thumbheight=600;
	              	$thumb_cutout=true;
				  	$thumb=Image::thumb($filepath,$filepath,'',$thumbwidth,$thumbheight,'',$thumb_cutout);
		           	//根据缩图返回数据           
		           	if($thumb){
		           		$file[1]=$thumb;
		           	}
				}
            	$file[0]=$fileurl;
              	//设置高度和宽度
              	$thumbwidth=210;
              	$thumbheight=180;
              	$thumb_cutout=true;
			  	$thumb=Image::thumb($filepath,$thumb_filepath,'',$thumbwidth,$thumbheight,'',$thumb_cutout);
	           	//根据缩图返回数据           
	           	if($thumb){
	           		$file[0]=$thumb_fileurl;
	           	}
	            //录入数据库
	            $data['file']=$file[1];
	            $data['title']='微信投票上传'.$milliSecond.$key.'.'.$ext;
	            $data['folder']=date('Y-m-d');
	            $data['ext']=$ext;
	            $data['size']=$filesize;
	            $data['time']=time();
	            $id=$this->add($data);
	            $file[2]=$id;
                return $file;
            }
        }
    }
    //校验文件类型
    public function check_image_mime($img){
        if(empty($img)) return;
        $ban_ext=array('jpg','jpge','png','gif','bmp','WebP','jpeg','WBMP');
	    $ext=explode('.', $img);
	    $ext=end($ext);
       	if(in_array($ext, $ban_ext)) return $ext;

	    $tempfile = @fopen($img, "rb");
        $bin = fread($tempfile, 2); //只读2字节 
        fclose($tempfile);
        $strInfo = @unpack("C2chars", $bin);
        $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        $fileType = '';
        switch ($typeCode){ // 6677:bmp 255216:jpg 7173:gif 13780:png 7790:exe 8297:rar 8075:zip tar:109121 7z:55122 gz 31139
            case '255216':
                $fileType = 'jpg';
                break;
            case '7173':
                $fileType = 'gif';
                break;
            case '13780':
                $fileType = 'png';
                break;
            default:
                $fileType = 'unknown';
        }
        return $fileType;
    }
  	//添加附件到数据库
    public function add($data)
    {
        return $this->model->table('upload')->data($data)->insert();
    }
	//获取头像
	public function get_photo($userid, $width = 80, $height = 80,$do=false)
	{
		$dir = array();
		list($path, $filename) = $this->set_photo_path($userid);
		$avatar = "/avatar/". $path . '/'. $filename . ".jpg";
		$file= is_file(__UPDDIR__ . $avatar) ? thumb(__UPDDIR__ . $avatar, $width, $height) : __PUBLIC__ . "/images/nohead.png";
		if ($do==true){
			return $file;
		}
		echo $file;
	}	
	public function set_photo_path($userid)
	{
		$dir = array();
		$userid = sprintf("%09d", $userid);
		$dir[] = substr($userid, 0, 3);
		$dir[] = substr($userid, 3, 2);
		$dir[] = substr($userid, 5, 2);
		$dir = implode("/", $dir);
		$name = substr($userid, -2);
		$return = array($dir, $name);
		return $return;
	}
	//生成地址位置
	public function data_to_area($data){
		if (1>strlen($data)) return '';
		$array= explode(',',$data);
		if (1>count($array) || 1>strlen($array[0])) return '';
		foreach ($array as $vo=>$k){
			$where['cid']=$k;
			$temp=$this->info($where,'area','name');
			$html.=$temp['name'];
		}
		
		
		return $html;
			
	}
	
	
	
}