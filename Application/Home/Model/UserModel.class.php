<?php

/* 用户模型 */
namespace Home\Model;
use Think\Model;

/* 
 * 验证条件
 * 	self::EXISTS_VALIDATE或者0存在字段就验证（默认）
 * 	self::MUST_VALIDATE或者1必需验证
 * 	self::VALUE_VALIDATE或者2值不为空的时候验证
 * 
 * 验证时间
 *  self::MODEL_INSERT或者1新增数据时候验证
 *  self::MODEL_UPDATE或者2编辑数据时候验证
 *  self::MODEL_BOTH或者3全部情况下验证（默认）
 *  自定义4代表登录
 *  
 *  */

class UserModel extends Model {
	/* 多错误显示 */
	protected $patchValidate = true;
	
	/* 字段定义 */
	protected $fields = array('user_id','user_name','email','password','telphone','role','status','reg_time','reg_ip','_pk'=>'user_id');
	
	/* 字段映射 */
	protected $_map = array();

	/* 自动完成
	 * array(完成字段，完成规则，[完成条件，附加规则])
	 *  */
	protected $_auto = array(
		array('password','md5',self::MODEL_BOTH,'function'),//对password字段在新增和编辑的时候使md5函数处理
		//array('user_name','getName',self::MODEL_INSERT,'callback'),//对user_name字段在新增时回调getName方法      
		array('reg_ip','get_client_ip',self::MODEL_INSERT,'callback'),//对regIP字段在新增的时候回调get_client_ip方法
		array('reg_time',NOW_TIME,self::MODEL_INSERT),//对regTime字段在新增的时候写入当前时间戳
		array('status',0,self::MODEL_INSERT),//新增的时候把status字段设置为0
		array('role',0,self::MODEL_INSERT),//对role字段在新增的时候设置为0
	);

	/* 新增时调用getName方法 */
	public function getName(){
		$chars = array(	//定义一个数组
			"a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r",
			"s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9"
		);
		
		$charsLen = count($chars)-1;
		shuffle($chars);//把数组中的元素按随机顺序重新排列
		$output = "";
		for($i=0;$i<9;$i++){
			$output .= $chars[mt_rand(0,$charsLen)];
		}
		return 'wft用户'.$output;
	}
	
	/* 自动验证  array(验证字段1，验证规则，错误提示，[验证条件，附加规则，验证时间])*/
	protected $_validate = array(
    	
		//验证用户名
		//array('user_name','','用户名已存在！',0,'unique',3),//在新增和编辑的时候验证user_name字段是否唯一性
		//array('user_name','/^[a-zA-Z0-9_\x7f-\xff]{3,20}$/','用户名不合法',0,'regex',2),//在编辑的时候验证用户名合法性
		//array('user_name','checkLength','用户名长度不合法',0,'function',2), //用户名长度不合法
	
		/* 验证邮箱 */
		array('email','email','邮箱格式不正确',self::EXISTS_VALIDATE),//邮箱格式不正确	
		array('email','1,32','邮箱长度不合法',self::EXISTS_VALIDATE,'length'),//邮箱长度不合法
		array('email','checkDenyEmail','邮箱禁止注册',self::EXISTS_VALIDATE,'callback'),//邮箱禁止注册
		array('email','','邮箱被占用',self::EXISTS_VALIDATE,'unique'),//邮箱被占用   

		//验证手机号
		array('telphone','/^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$/','手机格式不正确',self::EXISTS_VALIDATE,'regex',3),//在新增和编辑时验证手机
		
		array('password','/^[a-zA-Z0-9]{6,20}$/','密码格式不正确',0,'regex',3),//密码格式不正确
		array('repassword','password','确认密码不正确',0,'confirm',1),//确认密码不一致
		
		array('verify','require','验证码必须'),//验证码必须	
		array('verify','checkVerify','验证码错误',self::EXISTS_VALIDATE,'callback',1),//验证码错误
		
		//登录验证，登录操作专门指定验证时间为4
		array('name','log_checkName','账号错误',1,'function',4),//只在登录时候验证
		array('password','log_checkPwd','密码错误',1,'function',4),//只在登录时候验证
	);
	
	/** 
	 * 验证码错误
	 * */
	public function checkVerify($verify){
		return true;
		// return check_verify($verify,1);
	}	
	
	/* 邮箱禁止注册 */
	protected function checkDenyEmail(){}
	
	public function getIP(){
		// 		echo getenv('HTTP_CLIENT_IP');
		// 		var_dump($_SERVER);
		// 		exit();
		if(getenv("HTTP_CLIENT_TP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR") && strcasecmp("REMOTE_ADDR", "unknown"))
			$ip = getenv("REMOTE_ADDR");//www.jbxue.com
		else if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
			$ip = $_SERVER['REMOTE_ADDR'];
		else
			$ip = "unknown";
		return $ip;
		//echo $ip;
	}
	
	function get_client_ip($type = 0) {
		$type       =  $type ? 1 : 0;
		static $ip  =   NULL;
		if ($ip !== NULL) return $ip[$type];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$pos    =   array_search('unknown',$arr);
			if(false !== $pos) unset($arr[$pos]);
			$ip     =   trim($arr[0]);
		}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip     =   $_SERVER['HTTP_CLIENT_IP'];
		}elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$ip     =   $_SERVER['REMOTE_ADDR'];
		}
		// IP地址合法验证
		$long = ip2long($ip);
		$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
		return $ip[$type];
	}
		
}