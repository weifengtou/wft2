<?php
namespace Common\Controller;
class CommonController extends \Think\Controller {

	public function _initialize()
	{
		header("Content-type: text/html; charset=utf-8");
	}
}