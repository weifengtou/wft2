<?php
namespace Admin\Controller;
class TestController extends AdminController {

	private $navName = Test;

	public function _initialize() {
		parent::_initialize();
		$this->getThisNavMenu($this->navName);
	}
	
	public function index(){
		$this->display();
	}

	public function ajaxPostTest()
	{
		if (IS_POST) {
			dump($_POST);
		}
		exit;
	}
}
