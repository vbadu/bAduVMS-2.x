<?php
class fragmentModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //碎片显示
    public function out($sign)
    {
        $sign=in($sign);
        $info = $this->model->table('fragment')->where('sign="' .$sign . '"')->find();
        return  $this->display(html_out($info['content']),true,false);
    }



}

?>