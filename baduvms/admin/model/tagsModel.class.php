<?php
class tagsModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取tag列表
    public function tag_list($where,$limit,$order)
    {
        $sql="
        SELECT A.*,B.name as cname 
        FROM {$this->model->pre}tags A 
        LEFT JOIN {$this->model->pre}tags_category B ON A.cid=B.cid 
        WHERE 1=1 {$where} 
        ORDER BY {$order}
        LIMIT {$limit} 
        ";
        return $this->model->query($sql); 
    }

    //修改tag分类
    public function edit($data)
    {
        $condition['id']=intval($data['id']);
        return $this->model->table('tags')->data($data)->where($condition)->update(); 
    }

    //获取tag总数
    public function count($where=null)
    {
        return $this->model->table('tags','A')->where('1=1'.$where)->count();
    }

    //获取tag分类
    public function tag_category()
    {
        return $this->model->table('tags_category')->order('cid asc')->select();
    }

    //获取tag分类
    public function category_info($cid)
    {
        return $this->model->table('tags_category')->where('cid='.$cid)->find();
    }

    //添加tag分类
    public function category_add($data)
    {
        return $this->model->table('tags_category')->data($data)->insert();
    }

    //编辑tag分类
    public function category_edit($data)
    {
        $condition['cid']=intval($data['cid']);
        return $this->model->table('tags_category')->data($data)->where($condition)->update(); 
    }

    //删除tag内容
    public function category_del($cid)
    {
        $data['cid']=0;
        $this->model->table('tags')->data($data)->where('cid='.$cid)->update(); 
        return $this->model->table('tags_category')->where('cid='.$cid)->delete(); 
    }

    //删除tag内容
    public function del($id)
    {
        return $this->model->table('tags')->where('id='.$id)->delete(); 
    }

    //添加tag
    public function content_save($keywords,$aid)
    {
        if(empty($keywords)){
            return false;
        }
        $str = $keywords;
        $str = str_replace('，', ',', $str);
        $str = str_replace(' ', ',', $str);
        $strArray = explode(",", $str);
        $this->model->table('tags_relation')->where('aid='.$aid)->delete();
        foreach ($strArray as $list)
        {
            if(!empty($list)){
            $condition['name']=$list;
            $info=$this->model->table('tags')->where($condition)->find();
            if (empty($info))
            {
                //添加tag
                $data2 = array();
                $data2['name'] = $list;
                $data2['aid'] = $aid;
                $data2['aid'] = $aid;
                $tid=$this->model->table('tags')->data($data2)->insert();
                $data_relation['aid']=$aid;
                $data_relation['tid']=$tid;
                $this->model->table('tags_relation')->data($data_relation)->insert();
            }
            else
            {
                $condition2['aid']=$aid;
                $condition2['tid']=$info['id'];
                $info_relation=$this->model->table('tags_relation')->where($condition2)->find();

                if(empty($info_relation)){
                    $data_relation['aid']=$aid;
                    $data_relation['tid']=$info['id'];
                    $this->model->table('tags_relation')->data($data_relation)->insert();
                }
            }

            }

        }
        return true;
    }

    //删除tag
    public function del_content($aid)
    {

        $list=$this->model->table('tags_relation')->where('aid='.$aid)->select();
        if(empty($list)){
            return;
        }
        //删除该内容的TAG关系
        $this->model->table('tags_relation')->where('aid='.$aid)->delete();
        //查找其他TAG关系
        foreach ($list as $value) {
            $info=$this->model->table('tags_relation')->where('tid='.$value['tid'])->find();
            if(empty($info)){
                $this->model->table('tags')->where('id='.$value['tid'])->delete();
            }

        }
        
    }

}

?>