<?php
namespace Admin\Widget;
class AdminWidget extends WidgetWidget{

	public function _initialize()
	{
		parent::_initialize();
	}
	
	public function showAdminLeft($menusTree){
		$this->assign('menusTree',$menusTree);
		$this->display("Widget/Admin:adminLeft");
	}
	
}