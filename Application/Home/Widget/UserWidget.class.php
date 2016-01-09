<?php 
namespace Home\Widget;
class UserWidget extends WidgetWidget {

	public function _initialize()
	{
		parent::_initialize();
	}

	public function sendActiveEmail($data)
	{
		$this->assign('data',$data);
		$this->display("Widget/User:sendActiveEmail");
	}
}