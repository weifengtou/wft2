<?php
namespace Home\Model;
use Think\Model;

class InvestorBaseModel extends Model{
	/* 自动完成 
	 * 注意：由于在数据表中主键user_id 不是自增 ，所以自动完成无法进行
	 * */
	protected $_auto = array(
		array('status','0',self::MODEL_INSERT),//新增的时候把status字段设置为0
		array('save_time',NOW_TIME,self::MODEL_INSERT),//新增的时候把save_time字段设置为当前时间
		array('save_ip','get_client_ip',self::MODEL_INSERT,'function'),//新增的时候把save_ip字段设置为当前IP
	);
	
	/* 自动验证  */
	protected $_validate = array(
		array('inv_name','checkTrueName','-1',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证输入名字汉字
		array('inv_sex','1,2','-2',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写	
		array('inv_quality','1,2','-13',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写
		array('inv_link','checkLegalLink','-3',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证联系方式样式
		array('province','2,10','-4',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写
		array('city','2,10','-5',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写
		array('county','2,10','-6',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证时候填写
		array('max_amount','checkMoney','-7',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证数据格式
		array('min_amount','checkMoney','-7',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证数据格式
		array('max_amount,min_amount','checkCompare','-8',self::MUST_VALIDATE,'callback',self::MODEL_BOTH),//新增和修改时候验证max_amount>min_amount
		array('focus_trade','2,200','-9',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证非空
		array('privacy','1,2','-10',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证非空
		array('concept','5,500','-11',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证输入长度5-500个汉字
	);
	/* 比较最大金额与最小金额 */
	public function checkCompare($amount){
		return $amount['max_amount']>$amount['min_amount']?true:false;
	}
}

