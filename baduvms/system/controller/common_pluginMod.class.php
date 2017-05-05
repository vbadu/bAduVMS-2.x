<?php
//插件公共类
class common_pluginMod extends commonMod
{
    private $_data = array();

    public function __construct()
    {
        parent::__construct();
        define('__PLUGINDIR__', __ROOTDIR__. '/system/plugins/'.$_GET['_module']);
        define('__PLUGINTPL__', __ROOT__. '/system/plugins/'.$_GET['_module'].'/template');
        
    }

    public function __get($name){
        return isset( $this->_data[$name] ) ? $this->_data[$name] : NULL;
    }

    public function __set($name, $value){
        $this->_data[$name] = $value;
    }

    //包含内模板显示
    protected function show($tpl = ''){
        module('common')->view()->assign( $this->_data );
        $content=$this->display($tpl.'.html',true,true);
        $body=$this->display($this->config['TPL_COMMON'],false,true);
        $html=str_replace('<!--body-->', $content, $body);
        echo $html;
    }

    //模板输出定义
    protected function display($tpl = '',$dir=true,$return = false,$is_dir=true){
        module('common')->view()->assign( $this->_data );
        if($dir){
            $tpl= __ROOTDIR__. '/system/plugins/'.$_GET['_module'].'/template/'.$tpl;
        }else{
            $tpl=__ROOTDIR__.'/'.$this->config['TPL_TEMPLATE_PATH'].$tpl;
        }
        $this->assign('sys', $this->config);
        $this->assign('config', $this->config);
        $this->view()->assign( $this->_data);
        return $this->view()->display($tpl.'.html', $return, true,true);
    }

    //获取插件信息
    protected function plugin_config(){
        $url = __ROOTDIR__ . '/system/plugins/' . $_GET['_module']. '/config.xml';
        $config = Xml::decode(file_get_contents($url));
        return $config;
    }



}
?>