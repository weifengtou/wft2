<?php
namespace Home\Controller;
class ProjectController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}
	
	/* 项目列 */
	public function projects(){
		$this->display();
	}
	
	public function add(){
		$projectBase = D('ProjectBase');
		$data[proName] = '测试';
		if($projectBase->create($data)){
			$projectBase -> add();
			echo '成功';
		}else{
			var_dump($projectBase->getErrotr());
		}
	}
}