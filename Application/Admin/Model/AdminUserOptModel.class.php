<?php
namespace Admin\Model;
use Think\Model;
class AdminUserOptModel extends Model{
	
	// 不包含表前缀的数据表名称，一般情况下默认和模型名称相同，只有当你的表名和当前的模型类的名称不同的时候才需要定义
	protected $tableName = 'admin_user';
	
	/* 自动完成
	 * array(完成字段，完成规则，[完成条件，附加规则])
	 **/
	protected $_auto = [
		['password','md5',1,'function'],
		['create_time',NOW_TIME,1],
	];
	/**/
	
	/* 自动验证  array(验证字段1，验证规则，错误提示，[验证条件，附加规则，验证时间])*/
	protected $_validate = [
		['username','require','成员名必填',0],
		['username','','成员名已存在',0,'unique',1],
		['username','check_name1','成员名必须字母开头，只能由字母、数字、下划线及“/”组成',0,'function'],
		['password','require','密码必填',0],
		['password','6,18','密码必须6-18位',0,'length'],
		['password','check_password1','密码只能由字母、数字、下划线组成',0,'function'],
		['email','/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/','邮箱格式不正确',2],
		['phone','/(^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$)|(^0\d{2,3}-?\d{7,8})/','手机格式不正确',2],
	];
	/**/
}