<?php
class searchModel extends commonMod {

	public function __construct()
    {
        parent::__construct();
    }

    //获取搜索列表
    public function search_list($where,$limit,$model)
    {
        if($model==2){
            $list=$this->model->field('A.*,B.name as cname,B.subname as csubname,B.mid,C.content')
                ->table('content','A')
                ->add_table('category','B','B.cid = A.cid')
                ->add_table('content_data','C','C.aid = A.aid')
                ->where($where)
                ->limit($limit)
                ->select();
        }else{
            $list=$this->model->field('A.*,B.name as cname,B.subname as csubname,B.mid')
                ->table('content','A')
                ->add_table('category','B','B.cid = A.cid')
                ->where($where)
                ->limit($limit)
                ->select();
        }
        return $list;

    }

    //总数统计
    public function search_count($where,$model)
    {
        if($model==2){
            $count=$this->model->field('A.*,B.name as cname,B.subname as csubname,B.mid')
                ->table('content','A')
                ->add_table('category','B','B.cid = A.cid')
                ->add_table('content_data','C','C.aid = A.aid')
                ->where($where)
                ->count();
        }else{
            $count=$this->model->field('A.*,B.name as cname,B.subname as csubname,B.mid')
                ->table('content','A')
                ->add_table('category','B','B.cid = A.cid')
                ->where($where)
                ->count();
        }
        return $count;
        

    }


}