<?php
namespace Admin\Controller;
class PublicController extends AdminController {
    public function index(){
    	$this->display();
    }

    public function Login()
    {
    	C('SEO_TITLE','微风投-后台管理系统-登录');
    	if (session('adminUserInfo')) {
			$this->error("你已经登录",U("Index/index"));
		}
    	$this->display();
    }

    public function checkAdminUser()
    {
    	$x['status'] = 'error';
    	if (IS_POST) {
    		$username = $_POST['username'];
    		$password = $_POST['password'];
    		$remember = $_POST['remember'];
    		if (count($adminUser = M('AdminUser')->where(['username'=>$username,'password'=>md5($password)])->select())==1) {
    			session('adminUserInfo',$adminUser);
                session(C('USER_AUTH_KEY'),$adminUser[0]['id']);
                session('name',$adminUser[0]['username']);
                //如果用户是超级管理员，则可以进行一切操作  
                if (session('name')==C('RBAC_SUPERADMIN')) {  
                    session(C('ADMIN_AUTH_KEY'),true);  
                }
                $rbac=new \Org\Util\Rbac();  
                //取出用户权限信息  
                $rbac::saveAccessList(); 
    			$x['status'] = 'success';
    			$x['src'] = U("Index/index");
    			$this->rememberUserLog($username,$password,$remember);
    		}else{
    			$x['msg'] = "有户名或密码错误";
    		}
    	}
    	$x = json_encode($x);
    	echo $x;
    	exit;
    }
    
    /* 登录时记住用户名密码 */
    public function rememberUserLog($username,$password,$remember){
    	if($remember == "on"){
    		cookie('username',$username,3600*24);
            cookie('password',$password,3600*24);
            cookie('remember',$remember,3600*24);
    	}else{
            cookie('username',null);
            cookie('password',null);
            cookie('remember',null);
    	}
    }

    public function logout()
    {
    	session(null);
		$this->redirect("Public/login");	
    }

    public function testPublic()
    {
        echo "string";
    }
}