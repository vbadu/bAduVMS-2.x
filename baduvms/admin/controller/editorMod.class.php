<?php
class editorMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取编辑器
    public function get_editor($id,$ajax=false) {
        $ext_str  = 'cssPath : \'' . __PUBLICURL__ . '/kindeditor/plugins/code/prettify.css\',';
        $ext_str .= 'fileManagerJson:\'' . __APP__ . '/editor_file_manage/index.html'. '\',';
        $ext_str .= 'uploadJson:\'' . __APP__ . '/editor_upload/index.html?key='.urlencode($this->config['SPOT'].$this->config['DB_NAME']) . '\',';

        $html="<script type=\"text/javascript\">";
        if($ajax){
	        $html.="$(function() {";
        }else{
	        $html.="KindEditor.ready(function() {";
        }
        $html.="editor_".$id." = KindEditor.create(";
        $html.=" '#".$id."', ";
        $html.="		{
                           ".$ext_str."";
        $html.=" 
                items:[
				'source', '|', 'undo', 'redo', '|', 'plainpaste', 'wordpaste', '|', 
				'justifyleft', 'justifycenter', 'justifyright','justifyfull', 
				'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
				'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'preview','fullscreen','/',
				'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
				'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
				'flash', 'media', 'insertfile', 'table', 'hr',  'link', 'unlink'],
            			";	
	
        $html.="		
                            allowFileManager : true,
                            allowImageUpload : false,
                            allowFileUpload:false,
                            allowMediaUpload:false,
                            filterMode : false,
                            allowFlashUpload :false,
                            afterUpload : function(url,data) {
                                	$('#file_id').val($('#file_id').val()+data.id+',');
	                            },
		                            config : {
	                                THUMBNAIL_SWIHCH : ".intval($this->config['THUMBNAIL_SWIHCH']).",
	                                THUMBNAIL_MAXWIDTH : ".intval($this->config['THUMBNAIL_MAXWIDTH']).",
	                                THUMBNAIL_MAXHIGHT : ".intval($this->config['THUMBNAIL_MAXHIGHT']).",
	                                WATERMARK_SWITCH : ".intval($this->config['WATERMARK_SWITCH']).",
	                                WATERMARK_PLACE : ".intval($this->config['WATERMARK_PLACE']).",
	                                WATERMARK_CUTOUT : ".intval($this->config['WATERMARK_CUTOUT'])."
	                            	}
                            });
                });";
        $html.="</script>\r\n";
        return $html;
    }

    //远程存图
    public function get_remote_image(){
        $content=$_POST['content'];
        if(empty($content)){
            $this->msg('没有获取到内容！',0);
        }
        //文件路径
        $file_path = __ROOTDIR__ . '/upload/'.date('Y-m').'/'.date('d').'/';
        //文件URL路径
        $file_url = __ROOTURL__ . '/upload/'.date('Y-m').'/'.date('d').'/';
        $body=html_out($content);
        $img_array = array();
        preg_match_all("/(src|SRC)=[\"|'| ]{0,}(http:\/\/(.*)\.(gif|jpg|jpeg|bmp|png))/isU",$body,$img_array);
        $img_array = array_unique($img_array[2]);
        set_time_limit(0);
        $milliSecond = date("dHis") . '_';
        if(!is_dir($file_path)) @mkdir($file_path,0777,true);
        foreach($img_array as $key =>$value)
        {
            $value = trim($value);
            $ext=explode('.', $value);
            $ext=end($ext);
            $get_file = @Http::doGet($value,5);
            $rndFileName = $file_path.$milliSecond.$key.'.'.$ext;
            $fileurl = $file_url.$milliSecond.$key.'.'.$ext;
            if($get_file)
            {
                $status=@file_put_contents($rndFileName, $get_file);
                if($status){
                    $body = str_replace($value,$fileurl,$body);
                }
            }
            
        }
        $this->msg($body,1);
    }

    public function upload_type() {
        $type=$this->config['ACCESSPRY_TYPE'];
        $type=explode(',',$type);
        if(!empty($type)){
            foreach ($type as $value) {
                $str.='*.'.$value.';';
            }
            $str=substr($str,0,-1);
        }
        return $str;
    }

    //编辑器上传
    public function editor_upload() {
        $this->editor=in($_GET['editor']);
        $this->type=$this->upload_type();
        $this->limit=intval($this->config['ACCESSPRY_NUM']);
        $this->size=intval($this->config['ACCESSPRY_SIZE']);
        $this->display();
    }

    public function get_editor_upload($button,$editor) {

        $html="<script>
        $(document).ready(function() {
            $('#".$button."').click(function(){
                urldialog({
                    title:'上传文件到编辑器',
                    url:'".__APP__."/editor/editor_upload?editor=".$editor."',
                    height:480
                });
            });
        });
        </script>
        ";
        return $html;
    }

    //图片上传文件
    public function image_upload() {
        $this->id=in($_GET['id']);
        $this->editor=in($_GET['editor']);
        $this->type=$this->upload_type();
        $this->limit=intval($this->config['ACCESSPRY_NUM']);
        $this->size=intval($this->config['ACCESSPRY_SIZE']);
        $this->display();
    }

    public function get_image_upload($button,$id,$ajax=false,$editor='') {
        $html="<script>
        $(document).ready(function() {
            $('#".$button."').click(function(){
                urldialog({
                    title:'图片上传',
                    url:'".__APP__."/editor/image_upload?id=".$id."&editor=".$editor."',
                    width:620,
                    height:240
                });
            });
        });
        </script>
        ";
        return $html;
    }

    //文件上传
    public function file_upload() {
        $this->id=in($_GET['id']);
        $this->type=$this->upload_type();
        $this->limit=intval($this->config['ACCESSPRY_NUM']);
        $this->size=intval($this->config['ACCESSPRY_SIZE']);
        $this->display();
    }
    
    public function get_file_upload($button,$id,$ajax=false) {
        $html="<script>
        $(document).ready(function() {
            $('#".$button."').click(function(){
                urldialog({
                    title:'单文件上传',
                    url:'".__APP__."/editor/file_upload?id=".$id."',
                    width:620,
                    height:180
                });
            });
        });
        </script>
        ";
        return $html;
    }

    //组图上传
    public function images_upload() {
        $this->id=in($_GET['id']);
        $this->editor=in($_GET['editor']);
        $this->type=$this->upload_type();
        $this->limit=intval($this->config['ACCESSPRY_NUM']);
        $this->size=intval($this->config['ACCESSPRY_SIZE']);
        $this->display();
    }

    
    public function get_images_upload($id,$ajax=false) {
        $html="<script>
        $(document).ready(function() {
            $('#".$id."_button').click(function(){
                urldialog({
                    title:'组图上传',
                    url:'".__APP__."/editor/images_upload?id=".$id."',
                    height:480
                });
            });
        });
        </script>
        ";
        return $html;
    }

    public function upload_data(){
        ob_start();
        module('editor_upload')->index();
        $data=ob_get_contents();
        ob_end_clean();
        $json=json_decode($data,true);
        if($json['error']){
            header("HTTP/1.1 500 File Upload Error");
        }else{
            echo $data;
        }

    }

    //获取文件选择
    public function get_file_manage($id,$url,$ajax=false) {
        $ext_str .= 'fileManagerJson:\'' . __APP__ . '/editor_file_manage/index.html'. '\'';
        $html="<script type=\"text/javascript\">";
        if($ajax){
        $html.="$(function() {";
        }else{
        $html.="KindEditor.ready(function() {";
        }
        $html.="
                var editor = KindEditor.editor({
                    ".$ext_str."
                });
                KindEditor('#".$id."').click(function() {
                    editor.loadPlugin('filemanager', function() {
                        editor.plugin.filemanagerDialog({
                            viewType : 'VIEW',
                            dirName : '../themes',
                            clickFn : function(url, title) {
                                KindEditor('#".$url."').val(url);
                                editor.hideDialog();
                            }
                        });
                    });
                });
            });
            </script>\r\n";
        return $html;
    }


}

?>