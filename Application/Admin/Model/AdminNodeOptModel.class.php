<?php
namespace Admin\Model;
use Think\Model;
class AdminNodeOptModel extends Model{
	
	// 不包含表前缀的数据表名称，一般情况下默认和模型名称相同，只有当你的表名和当前的模型类的名称不同的时候才需要定义
	protected $tableName = 'admin_node';
	
	/* 自动完成
	 * array(完成字段，完成规则，[完成条件，附加规则])
	*  */
	protected $_auto = [
		['fasten',0,1]
	];
	
	/* 自动验证  array(验证字段1，验证规则，错误提示，[验证条件，附加规则，验证时间])*/
	protected $_validate = [
		['name','require','节点名必填',0],
		['name','','节点名不能重复',0,'unique',1],
		['name','check_name2','节点名必须由字母、数字、下划线、中文组成',0,'function'],
		['title','require','节点标题必须填',0],
		['title','check_name2','节点标题必须由字母、数字、下划线、中文组成',0,'function'],
	];
}