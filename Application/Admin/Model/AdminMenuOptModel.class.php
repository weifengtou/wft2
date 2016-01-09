<?php
namespace Admin\Model;
use Think\Model;
class AdminMenuOptModel extends Model{
	
	// 不包含表前缀的数据表名称，一般情况下默认和模型名称相同，只有当你的表名和当前的模型类的名称不同的时候才需要定义
	protected $tableName = 'admin_menu';
	
	/* 自动完成
	 * array(完成字段，完成规则，[完成条件，附加规则])
	*  */
	protected $_auto = [
		['static',1,1],
		['fasten',0,1]
	];
	
	/* 自动验证  array(验证字段1，验证规则，错误提示，[验证条件，附加规则，验证时间])*/
	protected $_validate = [
		['name','require','标识必填',0],
		['name','','标识不能重复',0,'unique',1],
		['name','check_name1','标识必须字母开头，只能由字母、数字、下划线及“/”组成',0,'function'],
		['title','require','标题必填',0],
		['icon','require','图标类必须有',0]
	];
}