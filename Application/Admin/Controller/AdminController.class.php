<?php
namespace Admin\Controller;
class AdminController extends \Common\Controller\CommonController {

	protected $uid; //用户id
	protected $userInfo; //用户信息
	
	protected $navId; //所在导航id
	protected $menus; //包含的菜单
	protected $menusTree; //菜单树

	public function _initialize()
	{
		parent::_initialize();
		// login 忽略
		/*if (CONTROLLER_NAME=="Public"&&in_array(ACTION_NAME,['login','checkAdminUser','rememberUserLog'])){}else {
			$userInfo = $this->userInfo = session('adminUserInfo');
			$uid = $this->uid = $userInfo[0]['id'];
			if (!$uid) {
				$this->redirect("Public/login");
			}
			$this->assign('uid',$uid);
			$this->assign('userInfo',$userInfo);
		}*/
		if (session('adminUserInfo')) {
			# code...
			$userInfo = $this->userInfo = session('adminUserInfo');
			$uid = $this->uid = $userInfo[0]['id'];
			$this->assign('uid',$uid);
			$this->assign('userInfo',$userInfo);
			// rbac
			if (session(C('ADMIN_AUTH_KEY'))==true) {
				# code...
			}else{
				$rbac=new \Org\Util\Rbac();  
				//检测是否登录，没有登录就打回设置的网关  
				$rbac::checkLogin();  
				//检测是否有权限没有权限就做相应的处理  
				if(!$rbac::AccessDecision()){  
				    // echo '<script type="text/javascript">alert("没有权限");</script>';  
				    $this->error("no",U('Public/testPublic'));
				    die();  
				}else{
					
				}
			}
		}
	}
	
	protected function getThisNavMenu($navName){
		$x = M('AdminNavigation')->where(['name'=>$navName])->find();
		$this->navId = $x['id'];
		$this->menus = M('AdminMenu')->where(['nav_id'=>$this->navId])->select();
		$this->menusTree = get_tree_node_sort(list_to_tree($this->menus));
		$this->assign('navId',$this->navId);
		$this->assign('menus',$this->menus);
		$this->assign('menusTree',$this->menusTree);
	}
}