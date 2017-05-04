<?php
//公共类
class commonMod
{
    protected $model = NULL; //数据库模型
    protected $layout = NULL; //布局视图
    protected $config = array();
    private $_data = array();

    protected function init(){}
    
    public function __construct(){
        global $config;
        @session_start();

        if(!file_exists('data/install.lock'))
        {
            $this->redirect(__ROOT__.'/install/');
        }
		if(preg_match('/(?i)msie [1-6]/',$_SERVER['HTTP_USER_AGENT'])==true) {
			if (in($_GET['_action'])!='noteie') {
				$this->redirect(__ROOT__.'/index/noteie');
				die;
			}
		}

        $config['PLUGIN_PATH']=__ROOTDIR__.'/system/plugins/';
		$this->common_headnav=model('nav')->get_list(0);
		$this->common_footnav=model('nav')->get_list(1);
        $this->config = $config;
        $this->model = self::initModel( $this->config);
        $this->init();
        Plugin::init();
        $this->config = (array)$config;
        define('__INDEX__', __APP__);
		$this->user=model('member')->current_member();
        //dump($this->user);
    }

    public function _empty()
    {
        $this->error404();
    }

    //初始化模型
    static public function initModel($config){
        static $model = NULL;
        if( empty($model) ){
            $model = new Model($config);
        }
        return $model;
    }

    public function __get($name){
        return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
    }

    public function __set($name, $value){
        $this->_data[$name] = $value;
    }

    //获取模板对象
    public function view(){
        static $view = NULL;
        if( empty($view) ){
            $view = new Template( $this->config );
        }
        return $view;
    }
    
    //模板赋值
    protected function assign($name, $value){
        return $this->view()->assign($name, $value);
    }

    public function return_tpl($content){
        return $this->display($content,true,false);
    }

    //模板显示
    protected function display($tpl = '', $return = false, $is_tpl = true,$is_dir=true){
        if(is_mobile()&&$this->config['MOBILE_OPEN']){
            $mobile_tpl='mobile'.'/';
        }
        if( $is_tpl){
            $tpl=__ROOTDIR__.'/'.$this->config['TPL_TEMPLATE_PATH'].$mobile_tpl.$tpl;
            if( $is_tpl && $this->layout ){
                $this->__template_file = $tpl;
                $tpl = $this->layout;
            }
        }

        $this->assign('model', $this->model);
        $this->assign('sys', $this->config);
        $this->assign('config', $this->config);
        $this->view()->assign( $this->_data);
        return $this->view()->display($tpl, $return, $is_tpl,$is_dir);
    }

    //页面不存在
    protected function error404($msg="")
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->common=model('pageinfo')->media('您要查找的页面不存在');
        $this->msg=$msg;
		//dump($msg,1);
        $this->display('404.html');
        exit;
    }

    //包含内模板显示
    protected function show($tpl = ''){
        $content=$this->display($tpl,true);
        $body=$this->display($this->config['TPL_COMMON'],true);
        $html=str_replace('<!--body-->', $content, $body);
        echo $html;
    }

    //脚本运行时间
    public function runtime(){
    $GLOBALS['_endTime'] = microtime(true);
        $runTime = number_format($GLOBALS['_endTime'] - $GLOBALS['_startTime'], 4);
        echo $runTime;
    }


    //判断是否是数据提交 
    protected function isPost(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    //直接跳转
    protected function redirect($url)
    {
        header('location:' . $url, false, 301);
        exit;
    }

    //操作成功之后跳转,默认三秒钟跳转
    protected function success($msg, $url = null, $waitSecond = 3)
    {
        if ($url == null)
            $url = __URL__;
        $this->assign('message', $msg);
        $this->assign('url', $url);
        $this->assign('waitSecond', $waitSecond);
        $this->display('msg.html');
        exit;
    }

    //弹出信息
    protected function alert($msg, $url = NULL){
        header("Content-type: text/html; charset=utf-8"); 
        $alert_msg="alert('$msg');";
        if( empty($url) ) {
            $gourl = 'history.go(-1);';
        }else{
            $gourl = "window.location.href = '{$url}'";
        }
        echo "<script>$alert_msg $gourl</script>";
        exit;
    }

    //判断空值
    public function alert_str($srt,$type=0,$json=false)
    {
        switch ($type) {
            case 'int':
                $srt=intval($srt);
                break;
            default:
                $srt=in($srt);
                break;
        }
        if(empty($srt)){
            if($json){
                $this->msg('内部通讯错误！',0);
            }else{
                $this->alert('内部通讯错误！');
            }
        }
    }

    //提示
    public function msg($message,$status=1) {
        if (is_ajax()){
            @header("Content-type:text/html");
            echo json_encode(array('status' => $status, 'message' => $message));
            exit;
        }else{
            $this->success($message);
        }
    }

    //分页 
    protected function page($url, $totalRows, $listRows =20, $rollPage = 5 ,$type=0,$mode=1,$style=array(),$pageecho='li')
    {
        $page = new Page();
        if($type==0){
			$page->pageecho=$pageecho;
			return $page->show($url, $totalRows, $listRows, $rollPage,$mode,$style);
        }else if($type==1){
            $page->show($url, $totalRows, $listRows, $rollPage);
            return $page->prePage('',0);
        }else if($type==2){
            $page->show($url, $totalRows, $listRows, $rollPage);
            return $page->nextPage('',0);
        }
    }

    //获取分页条数
    protected function pagelimit($url,$listRows)
    {
        $page = new Page();
        $cur_page = $page->getCurPage($url);
        $limit_start = ($cur_page - 1) * $listRows;
        return  $limit_start . ',' . $listRows;
    }

    //插件接口
    public function plus_hook($module,$action,$data=NULL)
    {
        $action_name='hook_'.$module.'_'.$action;
        $list=$this->model->table('plugin')->where('status=1')->select();
        $plugin_list=Plugin::get();
        if(!empty($list)){
            foreach ($list as $value) {
                $action_array=$plugin_list[$value['file']];
                if(!empty($action_array)){
                    if(in_array($action_name,$action_array))
                    {
                        if($return){
                            return Plugin::run($value['file'],$action_name,$data,$return);
                        }else{
                            Plugin::run($value['file'],$action_name,$data);
                        }
                    }
                }
            }
        }
    }

    //替换插件接口
    public function plus_hook_replace($module,$action,$data=NULL)
    {
        $hook_replace=$this->plus_hook($module,$action,$data,true);
        if(!empty($hook_replace)){
            return $hook_replace;
        }else{
            return $data;
        }
    }

    //登录检测
    protected function usr($table='') {
    	$uid=$_SESSION[$this->config['SPOT'].'_member'];
    	if (empty($uid)) return 0;
     	return model('member')->checkuser($uid);
	}
    protected function check_login() {
    	$m=strtolower($_GET['_module']);
    	$a=substr(strtolower($_GET['_action']),-8);
		if($m=='member' && $a=='login'|| $a=='logout'||$a==='register'){
            return true;
        }
		if($m=='read'){
            return true;
        }
        
        if(!empty($_GET['key'])){
            $key=urldecode($_GET['key']);
            $syskey=$this->config['SPOT'].$this->config['DB_NAME'];
            if($key==$syskey){
                return true;
            }
        }
        $uid=$_SESSION[$this->config['SPOT'].'_member'];
        //读取登录信息
        if(empty($uid)){
            $this->redirect(__APP__ . '/member/login.html');
        }
        $member=model('login')->member_info_id($uid);
        if(empty($member)){
            $this->redirect(__APP__ . '/member/login.html');
        }
        return true;
    }
	//登录校验
	public function check_id() {
        $member=model('member')->current_member();
		if (is_array($member)){
			return $member;
		}else{
			$this->redirect(__APP__ . '/member/login.html');
		}
	}











}