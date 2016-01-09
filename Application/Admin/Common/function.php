<?php
function get_nav_menu_tree(){
	$navs = M("AdminNavigation")->order(['sort'])->select();
	foreach ($navs as $k => $v){
		foreach ($v as $key => $value){
			$navMenusTree[$k][$key] = $value;
		}
		$navMenusTree[$k]['menus'] = M('AdminMenu')->where(['nav_id'=>$v['id']])->select();
	}
	return $navMenusTree;
}

// 必须字母开头，只能由字母、数字、下划线及“/”组成'
function check_name1($name){
	$reg = "/^[a-zA-Z_]+[a-zA-Z0-9_\/]*[a-zA-Z0-9_]$/";
	if(preg_match($reg,$name)){
		return true;
	}else{
		return false;
	}
}

// 必须字母中文数字、下划线
function check_name2($name)
{
	$reg = "/^[\x40-\xfe_a-zA-Z]+[\x40-\xfe_a-zA-Z0-9]$/";
	if(preg_match($reg,$name)){
		return true;
	}else{
		return false;
	}
}

function check_password1($password)
{
	$reg = "/^[a-zA-Z0-9_]+$/"; 
	if(preg_match($reg,$password)){
		return true;
	}else{
		return false;
	}
	return true;
}

//菜单由标识name获取id
function get_menu_id($menuname)
{
	$x = M('AdminMenu')->where(['name'=>$menuname])->field(['id'])->find();
	return $x['id'];
}