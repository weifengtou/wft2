<?php
namespace Home\Model;
use Think\Model;

class ProjectBaseModel extends Model{
	
	/* 数据表字段 */
	protected $fields = array(
		'user_id','pro_name','pro_resume','pro_stage','pro_video','pro_banner','province','city','county',
		'father_trade','full_trade','total_fin','single_fin','privacy','funds_use','pro_des','link_name','link_tel','status',
		'sub_time','sub_ip','last_update_time','_pk'=>'project_id');
	
	/* 自动完成 */
	protected $_auto = array(
		array('pro_stage','0',self::MODEL_INSERT),//编辑的时候把proStage字段设置为0
		array('status','0',self::MODEL_INSERT),//新增的时候把status字段设置为0
		array('sub_time',NOW_TIME,self::MODEL_INSERT),//新增的时候把subTime字段设置为当前时间
		array('sub_ip','get_client_ip',self::MODEL_INSERT,'function'),//新增的时候把subIP字段设置为当前IP
		array('last_updat_time',NOW_TIME,self::MODEL_UPDATE),//编辑的时候把last_update_time字段设置为当前时间
	);
	
	/* 自动验证 
	 *  array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
	 * */
	protected $_validate = array(
		array('pro_name','2,30','请注意项目名长度',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和编辑的时候验证项目名长度
		array('pro_resume','20,100','请注意项目定位长度',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和编辑的时候验证项目定位长度
        array('funds_use','20,500','请注意资金用途长度',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和编辑的时候验证资金用途长度
		// array('pro_des','200,5000','请注意项目详述长度',self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),//新增和编辑的时候验证项目详述长度
		array('link_tel','/(^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$)|(^0\d{2,3}-?\d{7,8})/','电话格式不正确',self::EXISTS_VALIDATE,'regex',self::MODEL_BOTH),//新增和编辑的时候验证手机号		
	);
}