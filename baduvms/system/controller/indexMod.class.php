<?php
class indexMod extends commonMod {

	public function __construct()
    {
        parent::__construct();
    }

	public function index() {
		/*hook*/
        $this->plus_hook('index','index');
        /*hook end*/
        $nav['home']=" class='aon'";
        $this->navs = $nav;
        //MEDIA信息
        $this->common=model('pageinfo')->media();
		$this->display($this->config['TPL_INDEX']);
	}
    //页面不存在
    public function noteie()
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->common=model('pageinfo')->media('请升级您的浏览器');
        $this->display('noteie.html');
        exit;
    }

}