<?php 
namespace Home\Widget;
class UserCenterWidget extends WidgetWidget {

	public function _initialize()
	{
		parent::_initialize();
	}

	//项目团队模板输出控制
	public function projectMemberTpl($projectDetail,$selId,$memInfo)
	{
		$this->assign('projectDetail',$projectDetail);
		$this->assign('selId',$selId);
		$this->assign('memInfo',$memInfo);
		$this->display("Widget/UserCenter:projectMemberTpl");
	}
}