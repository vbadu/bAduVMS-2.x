<?php
class settingMod extends commonMod
{
    public function __construct()
    {
        parent::__construct();
        if(!model('user_group')->menu_power('system',true)){
        	$this->msg('对不起，您没有该模块的操作权限！',0);
        }
		$this->check_app_power('setting',true);
	}
    // 显示系统设置页面
    public function index()
    {
        require __CONFIG__;
        $this->tpl_list=model('category')->tpl_list();
        $this->themes_list=model('setting')->themes_list();
        $this->config_array = $config;
        $this->show();
    }

    // 修改系统设置
    public function save(){
        if(!model('user_group')->model_power($this->model_url,'config')){
        	$this->msg('对不起，您没有该模块('.$this->model_url.')的操作权限！',0);
        }
        $config = $_POST; //接收表单数据
        //对密码进行加密处理
        $pwdtype=in($config['sms_pwd_type']);
        if ($pwdtype==1){
        	$config['sms_pwd']=$config['sms_pwd'];
        }elseif($pwdtype==2){
        	$config['sms_pwd']=md5($config['sms_pwd']);
        }elseif($pwdtype==3){
        	$config['sms_pwd']=md5($config['sms_pwd'].$config['sms_usr']);
        }elseif($pwdtype==4){
        	$config['sms_pwd']=md5($config['sms_usr'].$config['sms_pwd']);
        }
        unset($config['sms_pwd_type']);
        $config_array = array();
        foreach ($config as $key => $value) {
            if(!strpos($key,'|')){
                $config_array["config['" . $key . "']"] = $value;
            }else{
                $strarray=explode('|', $key);
                $str="config['" . $strarray[0] . "']";
                foreach ($strarray as $keys=>$values) {
                    if($keys<>0){
                    $str.="['".$values."']";
                    }
                }
                unset($strarrays);
                $config_array[$str] = $value;
            }
        }
		if (strlen(str_replace("id","",str_replace("}","",str_replace("{","","{$config['VIP_CARD_TPL']}"))).$config['VIP_CARD'])>19){
			$this->msg('对不起,会员编号规则生成的编号长度超过19位，请重新调整会员编号规则。',0);
		}
		
		
        $config_file= __CONFIG__;
        $status=model('setting')->save($config_array,$config_file);
        if($status){
            $this->msg('网站配置成功！',1);
        }else{
            $this->msg('网站配置失败，请检查配置文件所在目录是否有写入权限！',0);
        }
    }

}
?>