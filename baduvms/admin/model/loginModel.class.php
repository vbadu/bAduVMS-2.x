<?php
class loginModel extends commonModel
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取用户信息
    public function user_info($user) {
        return $this->model->table('admin')->where('user="'.$user.'"')->find();
    }

    //获取用户信息ID
    public function user_info_id($uid) {
        return $this->model->table('admin')->where('id='.$uid)->find();
    }

    //更新用户信息
    public function edit($data,$id) {
    	$condition['id']=$id;
        $this->model->table('admin')->data($data)->where($condition)->update();
    }

	//时间比较函数，返回两个日期相差几秒、几分钟、几小时或几天
	public function DateDiff($date1, $date2, $unit = "") { 
		switch ($unit) {
			case 's':
			$dividend = 1;
			break;
		case 'i':
			$dividend = 60;
			break;
		case 'h':
			$dividend = 3600;
			break;
		case 'd':
			$dividend = 86400;
			break;
			default:
			$dividend = 86400;
		}
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		if ($time1 && $time2)
			return (float)($time1 - $time2) / $dividend;
		return false;
	}


}

?>