<?php

// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = ''){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 验证手机号和座机号
 * /(^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$)|(^0\d{2,3}-?\d{7,8})/
 *   */
function checkLegalLink($link){
	$regx = '/(^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$)|(^0\d{2,3}-?\d{7,8})/';
	return preg_match($regx,$link)?true:false;
}

/**
 * 验证金额 格式
 * ^[1-9]\d{0,5}(\.[0-9]{1,3})$  验证有1-3位小数的非零正实数 
 *  */
function checkMoney($money){
	$regx = '/(^[1-9]\d{0,5}(\.[0-9]{1,3})$)|(^[1-9]\d{0,5}$)/';
	return preg_match($regx,$money)?true:false;
}

/**
 * 验证证件号码格式
 *  '/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/'验证数字和字母组成
 *   */
function checkPaperNum($num){
	$regx = '/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/';
	return preg_match($regx,$num)?true:false;
}

