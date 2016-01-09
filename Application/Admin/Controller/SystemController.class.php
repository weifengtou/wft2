<?php
namespace Admin\Controller;
class SystemController extends AdminController {
	
	private $navName=System; //所在导航name
	
	
	public function _initialize() {
		parent::_initialize();
		$this->getThisNavMenu($this->navName);
	}
	
	public function index(){
		$this->display();
	}
	
	// 导航管理
	public function navConf(){
		$all_navs = M("AdminNavigation")->select();
		$this->assign('all_navs',$all_navs);
		$this->display();
	}
	
	// 菜单管理
	public function menuConf(){
		$nav_menu_tree = get_nav_menu_tree();
		$this->assign('nav_menu_tree',$nav_menu_tree);
		$this->display();
	}
	
	//添加菜单
	public function addMenu(){
		if (I('post.submit')=='submit'){
			$x['status']='error';
			$x['msg'] = 'error:';
			$adminMenuOptModel = D('AdminMenuOpt');
			if($data = $adminMenuOptModel->create()){
				if ($id = $adminMenuOptModel->data($data)->add()){
					$x['status'] = "success";
					$x['url'] = U("menuConf",['menu_id'=>3])."#nav".I("get.nav_id")."menu".$id;
				}else{
					$x['msg'] .= $adminMenuOptModel->getDbError();
				}
			}else{
				$x['msg'] .= $adminMenuOptModel->getError();
			}
			echo json_encode($x);
			exit();
		}
		$this->assign('nav_menu_tree',get_nav_menu_tree());
		$this->display("optMenu");
	}

	//编辑菜单
	public function editMenu(){
		if (I('post.submit')=='submit'){
			$x['status']='error';
			$x['msg'] = 'error:';
			$adminMenuOptModel = D('AdminMenuOpt');
			if ($data = $adminMenuOptModel->create($_POST,2)) {
				if ($adminMenuOptModel->where(['id'=>I('get.optmenu')])->data($data)->save()!==false){
					$x['status'] = "success";
					$x['url'] = U("menuConf",['menu_id'=>3])."#nav".I("get.nav_id")."menu".I('get.optmenu');
				}else{
					$x['msg'] .= $adminMenuOptModel->getDbError();
				}
			}else{
				$x['msg'] .= $adminMenuOptModel->getError();
			}
			echo json_encode($x);
			exit();
		}
		$this->assign('menuInfo',M('AdminMenu')->where(['id'=>I('get.optmenu')])->select());	
		$this->assign('nav_menu_tree',get_nav_menu_tree());
		$this->display("optMenu");
	}
	
	// 删除菜单
	public function delMenu(){
		$x['status'] = 'error';
		$x['msg'] = "异常";
		if (IS_POST) {
			$x['del_data'] = M("AdminMenu")->where(['id'=>I('post.menu_id')])->find();
			if (M("AdminMenu")->where(['id'=>I('post.menu_id')])->delete()!==false) {
				$x['status'] = "success";
				$x['del_id'] = I("post.menu_id");
			}
		}
		echo json_encode($x);
		exit();
	}
	
	//根据导航选择菜单列
	public function selectMenus(){
		if (IS_POST){
			$menus = M('AdminMenu')->where(['nav_id'=>I('post.nav_id')])->select();
			echo "<option value='0'>0</option>";
			foreach ($menus as $k => $v){
				echo "<option value='".$v['id']."'>".$v['title']."</option>";
			}
		}
		exit;
	}
}