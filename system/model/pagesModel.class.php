<?php
//单页
class pagesModel extends commonMod
{
    public function __construct()
    {
        parent::__construct();
    }

    //单页内容
    public function content($cid)
    {
        return $this->model->table('category_page')->where('cid=' . $cid)->find(); 
    }



}

?>