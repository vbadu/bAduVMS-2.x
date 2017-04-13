<?php
class editor_uploadMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //文件上传
    public function index() {
      $files=$_FILES;
      $ban_ext=array('php','asp','asp','html','htm','js','shtml','txt','aspx');
      if(!empty($files)){
        foreach($files as $file) {
          $name=$file['name'];
          $ext=explode('.', $file['name']);
          $ext=end($ext);
          if(in_array($ext, $ban_ext)){
          $this->error_msg('非法文件上传！');
          return;
          }
        }   
      }else{
        $this->error_msg('上传文件不能为空！');
        return;
      }

    	//文件路径
      $file_path = __ROOTDIR__ . '/upload/';
      //文件URL路径
      $file_url = __ROOTURL__ . '/upload/';
      //文件目录时间
    	$filetime=date('Y-m').'/'.date('d');

      //重命名
      function filename(){
        foreach($_FILES as $file) {
          $name=explode('.', $file['name']);
          $ext=end($name);
          $name=$name[0];
        }
        $pinyin = new Pinyin();
        $pattern = '/[^\x{4e00}-\x{9fa5}\d\w]+/u';
        $name = preg_replace($pattern, '', $name);
        $name = substr($pinyin->output($name, true),0,80);
        if(file_exists(__ROOTDIR__.'/upload/'.date('Y-m').'/'.date('d').'/'.$name.'.'.$ext)){
          $rand='-'.substr(cp_uniqid(),-5);
        }
        return $name.$rand;
      }

      //上传
    	$upload = new UploadFile();
    	$upload->maxSize = 1024 * 1024 * $this->config['ACCESSPRY_SIZE']; //大小
		$upload->allowExts = explode(',', $this->config['ACCESSPRY_TYPE']); //格式
		$upload->savePath = $file_path . $filetime . '/'; //保存路径
        $upload->saveRule = 'md5_file'; //重命名

		if(!$upload->upload())
        {
            $this->error_msg($upload->getErrorMsg()); //输出错误消息
            return;
        }else{
           $info = $upload->getUploadFileInfo();
           $info = $info[0];
           $ext=$info['extension'];

          if($_POST['wateradd']){
            $waterfile=__ROOTDIR__.'/public/watermark/'.$this->config['WATERMARK_IMAGE'];
            if(!isset($_POST['waterpor'])){
              $por=$this->config['WATERMARK_PLACE'];
            }else{
              $por=$_POST['waterpor'];
            }
            if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='gif'){
                Image::water($file_path.$filetime.'/'.$info['savename'],$waterfile,$por);
            }
          }

          if($_POST['thumb']){
              //设置高度和宽度
              $thumbwidth=intval($_POST['thumbwidth']);
              $thumbheight=intval($_POST['thumbheight']);
              if(empty($thumbwidth)||$_POST['thumbsys']==1){
                $thumbwidth=$this->config['THUMBNAIL_MAXWIDTH'];
              }
              if(empty($thumbheight)||$_POST['thumbsys']==1){
                $thumbheight=$this->config['THUMBNAIL_MAXHIGHT'];
              }
              if(isset($_POST['thumb_cutout'])){
                  $thumb_cutout=intval($_POST['thumb_cutout']);
              }else{
                  $thumb_cutout=$this->config['WATERMARK_CUTOUT'];
              }
              //过滤不支持格式进行缩图
              if($ext=='jpg'||$ext=='jpeg'||$ext=='png'||$ext=='gif'){
                $thumb=Image::thumb($file_path.$filetime.'/'.$info['savename'],$file_path.$filetime.'/thumb_'.$info['savename'],'',$thumbwidth,$thumbheight,'',$thumb_cutout);
              }
          }

           //根据缩图返回数据           
           if($thumb){
            $file=$file_url.$filetime.'/thumb_'.$info['savename'];
           }else{
            $file=$file_url . $filetime . '/'.$info['savename'];
           }

           $title=str_replace('.'.$info['extension'],'', $info['name']);
           $json=array('error' => 0, 'url' =>$file, 'original'=>$file_url . $filetime . '/'.$info['savename'], 'file'=>$file,'title'=>$title,'ext'=>$ext,'msg'=>'成功');

           /*hook*/
           $json=$this->plus_hook_replace('upload','index',$json);
           /*hook end*/

           //录入数据库
           $data['file']=$json['file'];
           $data['title']=$json['title'];
           $data['folder']=date('Y-m-d');
           $data['ext']=$ext;
           $data['size']=$info['size'];
           $data['time']=time();
           $id=model('upload')->add($data);
           $json['id']=$id;

           @header("Content-type:text/html");
           echo json_encode($json);
		       return;
        }

      }

    //输出消息
    public function error_msg($msg) {
    @header("Content-type:text/html");
		echo json_encode(array('error' => 1, 'message' => $msg));
		return;
	}

}

?>