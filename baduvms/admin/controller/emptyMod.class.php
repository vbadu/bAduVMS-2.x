<?php
//插件模块，用于显示插件
class emptyMod extends commonMod
{

    public function _empty()
    {
        $plugin_name = $_GET['_module'];
        $action_name = $_GET['_action'];

        $info=model('plugin')->info_table($plugin_name);
        if(empty($info)){
        	Error::show($_GET['_module'] . '模块或插件不存在');
        }else{
        	if($info['status']==1){
        		if (Plugin::run($plugin_name, $action_name) == false) {
            		Error::show($_GET['_module'] . '模块或插件不存在');
        		}
        	}else{
        		Error::show($_GET['_module'] . '模块或插件不存在');
        	}

        }

        
    }

}
?>