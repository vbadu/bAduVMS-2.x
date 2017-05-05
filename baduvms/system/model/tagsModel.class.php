<?php
//tag显示
class tagsModel extends commonMod {

	public function __construct()
    {
        parent::__construct();
    }


    public function tag_index_list($limit) {
        return $this->model->table('tags')->order('id desc')->limit($limit)->select();
    }

    public function tag_index_count() {
        return $this->model->table('tags')->count();
    }

    public function tag_info($tag) {
        $condition['name']=$tag;
        return $this->model->table('tags')->where($condition)->find();        
    }

    public function tags_relation_aid($aid) {
        $condition['aid']=$aid;
        return $this->model->table('tags_relation')->where($condition)->select();         
    }

    public function tags_relation_tid($tid) {
        return $this->model->table('tags_relation')->where('tid in('.$tid.')')->select();       
    }

	public function tag_list($tid,$limit) {
        if(empty($tid)){
        return;
        }
        return $this->model
        ->field('A.*,B.name as cname,B.subname as csubname,B.mid')
        ->table('content','A')
        ->add_table('category','B','A.cid=B.cid')
        ->add_table('tags_relation','C','A.aid=C.aid')
        ->where('C.tid='.$tid.'')
        ->order('A.updatetime desc')
        ->limit($limit)
        ->select();
	}

    public function tag_count($tid) {
        if(empty($tid)){
        return;
        }
        return $this->model
        ->table('content','A')
        ->add_table('category','B','A.cid=B.cid')
        ->add_table('tags_relation','C','A.aid=C.aid')
        ->where('C.tid='.$tid.'')
        ->count();
    }

    //访问计数
    public function views_content($id,$views){
        $data['click'] = $views + 1;
        $condition['id'] = $id;
        $this->model->table('tags')->data($data)->where($condition)->update();
    }
	

}