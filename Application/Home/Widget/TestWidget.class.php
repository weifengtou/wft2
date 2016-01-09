<?php
namespace Home\Widget;
class TestWidget extends WidgetWidget{

	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
		$this->display("User/login");
	}

	//测试参数
	public function test1($arr2)
	{
		dump($arr2);
	}
	
}