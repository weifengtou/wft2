<?php
namespace Admin\Controller;
class WorkController extends AdminController {

    private $navName=Work; //所在导航name

	public function _initialize()
	{
		parent::_initialize();
        $this->getThisNavMenu($this->navName);
    }

    public function index()
    {
        $this->display();
    }

    public function testWork()
    {
        $this->display();
    }
}