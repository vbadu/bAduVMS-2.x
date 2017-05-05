<?php
//公共类
class commonModel
{
    protected $model = NULL; //数据库模型
    protected $config = array();
    private $_data = array();

    protected function init(){}
    
    public function __construct(){
        global $config;
        session_start();
        $this->config = $config;
        $this->model = self::initModel( $this->config);
        $this->cache = self::initCache( $this->config);
        $this->init();
        $this->config = (array)$config;
    }


    //初始化缓存
    static public function initCache($config){
        static $cache = NULL;
        if( empty($cache) ){
            $cache = new Cache($config);
        }
        return $cache;
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

    //插件接口
    public function plus_hook($module,$action,$data=NULL,$return=false)
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

    //提示
    public function msg($message,$status=1) {
        echo json_encode(array('status' => $status, 'message' => $message));
        exit;
    }

}