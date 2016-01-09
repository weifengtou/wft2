<?php
namespace Admin\Widget;
class UserWidget extends WidgetWidget{

	public function _initialize()
	{
		parent::_initialize();
	}
	
	public function listNodeTree($nodeTree)
	{
		$this->assign('nodeTree',$nodeTree);
		$this->display("Widget/User:listNodeTree");
	}
}