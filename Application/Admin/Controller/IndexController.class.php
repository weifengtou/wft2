<?php
namespace Admin\Controller;
class IndexController extends AdminController {

	public function _initialize()
	{
		parent::_initialize();
	}

    public function index(){
    	$this->display();
    	_vp(session());
    }
}