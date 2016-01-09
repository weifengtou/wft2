<?php
namespace Home\Controller;
class HomeController extends \Common\Controller\CommonController {

	public function _initialize()
	{
		parent::_initialize();
		//注入userDetail
		if (session('userDetail')) {
			$this->assign('userDetail',session('userDetail'));
		}
		$Base = M('userBase');
		$map[user_id] = session('userDetail')[0][user_id];
		$userBase = $Base -> where($map) -> find();
		$this->userBase = $userBase;
		$this->assign('userBase',$userBase);
		
	}

	//验证码
	public function verify()
	{
		$Verify = new \Think\Verify($_GET);
		$Verify->entry(1);
	}
	//校验验证码
	public function checkVerify()
	{
		if (IS_POST) {
			$code = I('post.code');
			$Verify = new \Think\Verify();
			if ($Verify->check($code, 1)) {
				echo 1;
			}else{
				echo 0;
			}
		}
	}

	//获取邮箱对应登录页
	public function getEmailUrls($email)
	{
		$db2=C('BASIC_LIB_TABLES');
    	$urls = M('EmailLoginUrls','lib_',$db2)->getField('case_sender,return_url');
    	$email_end = preg_replace('/^(\w)+(\.\w+)*@/','',$email);
    	return $urls[$email_end]?$urls[$email_end]:"http://hao.360.cn/mianfeiziyuan.html";
	}
	
	//获取ip地域
	public function ip(){
		$ip = get_client_ip();
		print_r($ip);
		$Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
		$area = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
		print_r($area);
	}

	//异常返回
	public function errorBack()
	{
		$this->error("异常");
	}
	
	/**
	 * 获取用户昵称
	 **/
	public function getUserBase(){
		
	}

	/**
	 * 根据地区上级获取地区下级列表(ajax)
	 * @param post $area_pid 上级ID
	 * @return html 下级数据
	 * @author wodrow <1173957281@qq.com>
	 */
	public function getAreas()
	{
		if (IS_POST) {
			$citys = M('BaseArea')->where("area_pid='".$_POST['area_pid']."'")->select();
			echo "<option value='0'>未选择</option>";
			foreach ($citys as $k => $v) {
				echo "<option value='".$v['area_id']."'>".$v['area_name']."</option>";
			}
		}
	}

	/**
	 * 根据地区上级获取行业下级列表(ajax)
	 * @param post $area_pid 上级ID
	 * @return html 下级数据
	 * @author wodrow <1173957281@qq.com>
	 */
	public function getTrades()
	{
		if (IS_POST) {
			$citys = M('BaseTrade')->where("trade_pid='".$_POST['trade_pid']."'")->select();
			echo "<option value='0'>未选择</option>";
			foreach ($citys as $k => $v) {
				echo "<option value='".$v['trade_id']."'>".$v['trade_name']."</option>";
			}
		}
	}
}