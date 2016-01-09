<?php
namespace Home\Controller;
class CommunityController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
		$this->display();
	}
}