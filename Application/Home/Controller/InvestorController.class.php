<?php
namespace Home\Controller;
class InvestorController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}
	
	/* 投资人列 */
	public function investors(){
		$this->display();
	}
}