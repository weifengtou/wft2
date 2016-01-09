<?php
namespace Home\Controller;
class UserController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}

	//判断是否已经登陆
	private function is_login()
	{
		if(session('userDetail')){
			$this->error("已登录不能访问!");
		}
	}

	//注册
	public function register()
	{
		$this->is_login();
		if (IS_POST) {
			$userModel = D('User');
			if ($userModel->create()) {
				$user_id = $userModel->add();
				if ($user_id) {
					$this->sendRegisterEmailFun($user_id);
					$this->redirect("registerInfo",['user_id'=>$user_id]);
				}
			}else{
				$this->error(dump($userModel->getError()));
			}
		}
		$this->display();
	}

	//注册后跳转页面
	public function registerInfo()
	{
		$this->is_login();
		if ($user_id = I("get.user_id")) {
			$userDetail = get_user_detail($user_id);
			if ($userDetail[0]["status"]==0) {
				$this->assign("userDetail",$userDetail);
				$this->display();
			}else{
				// session('userDetail',get_user_detail($user_id));
				// $this->success("你已激活成功!",U('Index/index'));
				$this->success("你已激活成功!",U('User/login'));
			}
		}else{
			$this->error('非法访问');
		}
	}

	//发送注册邮件
	private function sendRegisterEmailFun($user_id)
	{
		$email_code = time().md5($user_id.time());
		M('User')->where("user_id='$user_id'")->save(['email_code'=>$email_code]);
		$userDetail = get_user_detail($user_id);
		ob_start();
		W('User/sendActiveEmail',[$userDetail]);
		$content = ob_get_contents();
		ob_end_clean();
		sendEmail($userDetail[0]['email'],"账户激活",$content);
	}

	//重新发送注册码
	public function reSendEmailCode()
	{
		if (!$user_id=I("get.user_id")) {
			$this->error("非法访问!");
		}else{
			$this->sendRegisterEmailFun($user_id);
			$this->success("发送成功",U("registerInfo",['user_id'=>$user_id]));
		}
	}

	//激活邮箱
	public function activeEmail()
	{
		if (I('get.user_id')&&I('get.activeCode')) {
			$userDetail = get_user_detail(I('get.user_id'));
			if (I('get.activeCode')==$userDetail[0]['email_code']) {
				$update_time = substr($userDetail[0]['email_code'], 0,10);
				if ((time()-$update_time)>24*3600) {
					echo "验证码过期!";
				}else{
					M('User')->where("user_id=".I('get.user_id'))->save(['email_code'=>'','status'=>1]);
					session('userDetail',get_user_detail(I('get.user_id')));
					$this->success("激活成功",U('Index/index'));
					$this->nickName();
				}
			}else{
				echo "验证码失效!";
			}
		}else{
			echo "非法访问!";
		}
	}
	
	//自动分配注册用户昵称
	public function nickName(){
		if(I('get.user_id')){
			$nickName = ID.NOW_TIME.I('get.user_id');
			$userBase = M('userBase');
			$data[user_id]=I('get.user_id');
			$data[nick_name]=$nickName;
			$userBase->add($data);
		}
	}

	//验证邮箱是否重复
	public function checkEmail()
	{
		if (IS_POST) {
			$has_email = M('User')->where("email='".I("post.email")."'")->limit("1")->select();
			if ($has_email) {
				echo 1;
			}else{
				echo 0;
			}
		}
	}

	/**
	 * 登录
	 * 错误提示：
	 * 1：登录成功
	 * -1：账号和密码必须填写
	 * -2：邮箱或密码错误
	 * -3： 手机号或密码错误
	 * -4：用户名或密码错误
	 * -5：邮箱未激活
	 * -6：请输入正确格式账号
	 * */	
	public function login()
	{
		$this->is_login();
		if(IS_POST){
			$name	  = I('POST.name');
			$password = I('POST.password');
			$remember = I('POST.remember');
			$this->rememberUserLog($name,$password,$remember);
			if($name!=NULL && $password !=Null){
				$user = D('User');
				$map['password'] = md5($password);
				if(checkLegalEmail($name)){           //邮箱登录
					$map['email'] = $name;
					$userDetail = $user->where($map)->limit(1)->select();
					session('userDetail',$userDetail);
					if($userDetail!=Null){
						if($userDetail[0][status] == 1){
							$user_log = M("user_log");
							$data["user_id"] = $userDetail[0][user_id];
							$data["action_id"] = 2;
							$data["log_time"] = time(); 
							$data["log_ip"] = get_client_ip();
							$user_log -> add($data);
							echo "1";
						}else{
							echo '-5';//请前往注册邮箱激活账号
						}
					}else{
						echo '-2';//邮箱或是密码错误
					}
				}else if(checkLegalTel($name)){       //手机登录
					$map['telphone'] = $name;
					$userDetail = $user->where($map)->limit(1)->select();
					session('userDetail',$userDetail);
					if($userDetail!=Null){
						echo "1";
					}else{
						echo "-3";//手机或是密码错误
					}
				}else if(checkLegalUserName($name)){  //用户名登录
					$map['user_name'] = $name;
					$userDetail = $user->where($map)->limit(1)->select();
					session('userDetail',$userDetail);
					if($userDetail!=Null){
						echo "1";
					}else{
						echo "-4";//用户名或是密码错误
					}
				}else{
					echo "-6";//请输入正确格式的账号
				}
			}else{
				echo '-1';//账号或密码必需填写
			}
			//echo json_encode($_SESSION);
		}else{
			$this->display();
		}
	}
	
	/* 登录时记住用户名密码 */
	public function rememberUserLog($name,$password,$remember){
		if($remember == 1){
			setcookie('name',$name,time()+3600);
			setcookie('password',$password,time()+3600);
			setcookie('remember',$remember,time()+3600);
		}else{
			setcookie('name',$name,time()-3600);
			setcookie('password',$password,time()-3600);
			setcookie('remember',$remember,time()-3600);
		}	
	}

	//退出
	public function logout()
	{
		session(null);
		$this->redirect("User/login");
	}
	
	/* cc模型测试 */
	public function add(){
		$user = D('User');
		// $data['userName'] = '我是s打发ss';
		$data['email'] = 'huixiao_deyun@163.com';
		$data['password'] = '123456';
		$data['repassword'] = '';
		if($user->create($data)){
			$user->add();
			echo "所有字段验证成功";
			//echo $data['userName'];
			//$user->where('user_id = 11')->save($data);
		}else{
			var_dump($user->getError());
		}
	}
}
