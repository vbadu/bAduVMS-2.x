<?php
//插件管理器数据处理
class pluginModel extends commonModel {

	public function __construct()
    {
        parent::__construct();
    }

    //获取列表
    public function plugin_list()
    {
        $config_lists = glob(__ROOTDIR__ . '/system/plugins/*/config.xml');

        if(!empty($config_lists)){
            foreach($config_lists as $key=>$value){
                $config_ini=Xml::decode(file_get_contents($value));
                $config_ini=$config_ini['config'];
                $db_config =$this->model->table('plugin')->where('file="'.$config_ini['file'].'"')->find(); 
                if (!empty($db_config))
                {
                    $db_config['update_ver']=$config_ini['ver'];
                    $config_list[] = $db_config;
                }
                else
                {
                    $config_ini['update_ver']=$config_ini['ver'];
                    $config_list[] = $config_ini;
                }
            }

        }
        return $config_list;
    }

    //插件数量
    public function plugin_count()
    {
        return $this->model->table('plugin')->count();

    }

    //获取配置
    public function info($name)
    {
        $url = __ROOTDIR__ . '/system/plugins/' . $name. '/config.xml';
        $config = @Xml::decode(file_get_contents($url));
        return $config['config']; 
    }

    //获取插件信息
    public function info_data($id)
    {
        return $this->model->table('plugin')->where('id='.$id)->find(); 
    }

    public function info_data_count($file)
    {
        return $this->model->table('plugin')->where('file="'.$file.'"')->find(); 
    }


    //获取插件列表
    public function list_table()
    {
        return $this->model->table('plugin')->select();
    }

    //查找插件
    public function info_table($name)
    {
        return $this->model->table('plugin')->where('file="'.$name.'"')->find(); 
    }

    //删除权限
    public function del_purview($id){
        return $this->model->table('admin_purview')->where('id='.$id)->delete();
    }

    //添加菜单
    public function add_menu($info,$pid=null){
        $data=array();
        $data['pid']=5;
        $data['name']=$info['name'];
        $data['module']=$info['file'];
        return $this->model->table('admin_menu')->data($data)->insert();
    }

    //删除菜单
    public function del_menu($id){
        return $this->model->table('admin_menu')->where('id='.$id)->delete();
    }

    //禁用菜单
    public function status_menu($id,$status){
        $data['status']=$status;
        $this->model->table('admin_menu')->data($data)->where('id='.$id)->update(); 
    }

    //添加插件
    public function add($info,$mid){
        $data=array();
        $data['name']=$info['name'];
        $data['file']=$info['file'];
        $data['status']=1;
        $data['mid']=$mid;
        $data['ver']=$info['ver'];
        $data['author']=$info['author'];
        return $this->model->table('plugin')->data($data)->insert();
    }
    //升级插件
    public function upgrade($id,$ver){
        $data['ver']=$ver;
        $this->model->table('plugin')->data($data)->where('id='.$id)->update(); 
    }

    //删除插件
    public function del($id){
        return $this->model->table('plugin')->where('id='.$id)->delete();
    }

    //删除插件表
    public function del_table($table){
        $sql=" DROP TABLE `{$this->model->pre}{$table}` ";
        @$this->model->query($sql);
    }

    //状态
    public function status($id,$status){
        $data['status']=$status;
        $this->model->table('plugin')->data($data)->where('id='.$id)->update(); 
    }

    //执行数据库操作
    public function runSql($sql_array = "")
    {
        if (is_string($sql_array))
        {
            $this->model->query($sql_array);
            return true;
        }
        if (is_array($sql_array))
        {
            foreach ($sql_array as $sql)
            {
                $this->model->query($sql);
            }
            return true;
        }
        return false;
    }

  

}