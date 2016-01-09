<?php
namespace Admin\Widget;
class SystemWidget extends WidgetWidget{

	public function _initialize()
	{
		parent::_initialize();
	}
	
	public function showNavMenusTree($menusTree)
	{
		$this->assign('menusTree',$menusTree);
		$this->display('Widget/System:showNavMenusTree');
	}
	
}