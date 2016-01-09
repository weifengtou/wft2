<?php
namespace Home\Model;
use Think\Model;

class InvestorApproveModel extends Model{
	/* 自动完成 */
	protected $_auto = array(
		array('status','0',self::MODEL_INSERT),//新增的时候把status字段设置为0
		array('save_time',NOW_TIME,self::MODEL_INSERT),//新增的时候把save_time字段设置为当前时间		
	);
	
	/* 自动验证 */
	protected $_validate = array(
		array('true_name','checkTrueName','-1',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证输入的真实姓名
		//array('paper_type','1,2','-2',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写
		array('paper_num','checkPaperNum','-2',self::EXISTS_VALIDATE,'function',self::MODEL_BOTH),//新增和修改时候验证格式是否正确
		array('paper','10,100','-3',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和修改时候验证是否填写
	);
}