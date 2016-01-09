<?php
namespace Home\Controller;
class IndexController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}

	// 首页
    public function index(){
        $this->display();
    }
}