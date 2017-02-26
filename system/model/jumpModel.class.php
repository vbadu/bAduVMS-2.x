<?php
//单页
class jumpModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //单页内容
    public function info($cid)
    {
        return $this->model->table('category_jump')->where('cid=' . $cid)->find(); 
    }



}

?>