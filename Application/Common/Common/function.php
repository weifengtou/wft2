<?php
/** 
 * 验证用户名
 * /^[a-zA-Z0-9_]{3,16}$/ 字母数字字符下划线，3-20位
 *   */
function checkLegalUserName($userName){
	$regx = '/^[a-zA-Z0-9_]{12,16}$/';
	return preg_match($regx, $userName)?true:false;
}

/**
 * 验证姓名
 * \x7f-\xff 汉字,占3个字节
 * /(?[\x7f-\xff]{1,8}·\x7f-\xff]{1,8})|(^[\x7f-\xff]{6,30}$)/
 *   */
function checkTrueName($trueName){
	$regx = '/(^[\x7f-\xff]{3,30}·[\x7f-\xff]{3,30}$)|(^[\x7f-\xff]{6,60}$)/';
	return preg_match($regx,$trueName)?true:false;
}



/**
 * 验证手机号
 * /^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$/
 *   */
function checkLegalTel($tel){
	$regx = '/^(13[0-9]|15[0|1|2|3|5|6|7|8|9]|17[7|8]|18[0-9])\d{8}$/';
	return preg_match($regx,$tel)?true:false;
}

/**
 * 验证邮箱
 * /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/
 *   */
function checkLegalEmail($email){
	$regx = '/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/';
	return preg_match($regx,$email)?true:false;
}

/** 
 * 验证密码
 * /^[a-zA-Z0-9]{6,30}$/ 字母数字组成6-30位密码
 *  */
function checkPwd(){}

/**
 * 验证登录密码
 *   */
function log_checkPwd(){}

/**
 * 获取用户信息
 * @prarm $info 数据
 * @prarm $field 查询字段
 * @author wodrow <451611cv@gmail.com | 1173957281@qq.com>
 */
function get_user_detail($info,$field='user_id')
{
	$userDetail = M('User')->where($field."='$info'")->limit("1")->select();
	return $userDetail;
}

/**
 * 获取项目信息
 * @prarm $info 数据
 * @prarm $field 查询字段
 * @prarm $bind 关联信息
 * @return array 项目信息
 * @author wodrow <451611cv@gmail.com | 1173957281@qq.com>
 */
function get_pro_detail($info,$field='project_id',$bind=['Base'])
{
    if (in_array('Base', $bind)) {
        $projectDetail = M('ProjectBase')->where($field."='$info'")->limit("1")->select();
    }
    //团队成员
    if (in_array('Member', $bind)) {
        $x = M('ProjectMember')->where(['project_id'=>$projectDetail[0]['project_id']])->select();
        $roleArr = M("BaseProMemRole")->getField("role_id,role_name");
        foreach ($x as $k => $v) {
            $x[$k]['mem_role_name'] = $roleArr[$v['mem_role']];
        }
        $projectDetail[0]['project_members'] = $x;
    }
    //审核信息
    if (in_array('Papers', $bind)) {
        $x = M("ProjectPapers")->where(['project_id'=>$projectDetail[0]['project_id']])->select();
        $projectDetail[0]['project_papers'] = $x;
    }
    return $projectDetail;
}

/**
* 邮件发送函数
* @param string $to 收件邮箱
* @param string $subject 邮件主题
* @param string $content 邮件内容
* @param string $username 收件人姓名
* @author wodrow <451611cv@gmail.com | 1173957281@qq.com>
*/
function sendEmail($to, $subject, $content) 
{
    // require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer5_2_10/PHPMailerAutoload.php");
    // require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer5_2_10/class.phpmailer.php");
    // require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer5_2_10/class.pop3.php");
    // require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer5_2_10/class.smtp.php");
    require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer/class.phpmailer.php");
    require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer/class.pop3.php");
    require_once(C('DOCUMENT_ROOT')."Public/static/PHPMailer/class.smtp.php");
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Port = C('MAIL_PORT');  //邮件发送端口
    $mail->Host=C('MAIL_HOST'); //smtp服务器的名称
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD'); //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to); //收件人和呢称
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet=C('MAIL_CHARSET'); //设置邮件编码
    $mail->Encoding = C('MAIL_ENCODING'); //编码方式
    $mail->Subject =$subject; //邮件主题
    $mail->Body = $content; //邮件内容
    // $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //邮件正文不支持HTML的备用显示
    if($mail->Send()) {
        return 1;
        exit();
    } else {
        return 0;
        exit();
    }
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url){
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url(){
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = [];
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = []){
    if(is_array($tree)) {
        $refer = [];
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 获取节点所有父级元素
 * @param array $list 数据
 * @param int $node_id 节点id
 * @param int $pk 主键
 * @param int $pid 外键
 * @param int $root 根节点
 * @return array 父节点
 * @author wodrow <wodrow451611cv@gmail.com | 1173957281@qq.com>
 */
function get_list_parents($list,$node_id,$pk='id',$pid='pid',$root=0)
{
    $i = $root;
    while($node_id!=$root){
        foreach ($list as $k => $v){
            if ($v[$pk]==$node_id){
                $node_to_root[$i++] = $k;
                $node_id = $v[$pid];
            }
        }
    } 
    $i = $i-1;
    for($i;$i>=$root;$i--){
        $root_to_node[] = $node_to_root[$i];
        $parent_list[] = $list[$node_to_root[$i]];
    }
    return $parent_list;
}

/**
 * 获取节点排序
 * @param array $tree 数据
 * @param int $node_id 节点id
 * @param int $start 起始值
 * @param string $sort_name 排序字段下标
 * @param array $p 
 * @return array 带节点排序的数据
 * @author wodrow <wodrow451611cv@gmail.com | 1173957281@qq.com>
 */
function get_tree_node_sort($tree,$start=0,$sort_name='_node_sort',&$p=[]){
    $start++;
    foreach ($tree as $k => $v) {
        $p[$k] = $v;
        $p[$k][$sort_name] = $start-1;
        if ($v['_child']) {
            get_tree_node_sort($v['_child'],$start,$sort_name,$p[$k]['_child']);
        }
    }
	return $p;
}

/**
 * 获取list数组单个字段值
 * @param array $list 数组
 * @param int $key 索引键
 * @param int $search 索引值
 * @param int $field 查询键
 * @return int|string 查询值
 * @author wodrow <wodrow451611cv@gmail.com | 1173957281@qq.com>
 */
function get_list_field($list,$key,$search,$field){
	foreach ($list as $k => $v){
		if($v[$key]==$search){
			return $v[$field];
		}
	}
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 生成目录
 * @access public
 * @param string $path 目录位置与名称
 * @return void
 * @author wodrow <451611cv@gmail.com | 1173957281@qq.com>
 */
function createDir($path)
{
    return is_dir($path) or (createDir(dirname($path)) and mkdir($path, 0777)); 
}

/**
 * 图片裁剪处理函数
 * @param  string $img_url  图片位置 must
 * @param  string $save_path 处理完毕的保存路径 must
 * @param  string $save_name 处理完毕的保存名称 must
 * @param  array $crop 裁剪参数
 * @param  array $thumb 缩略参数
 * @return array
 * @author wodrow <451611cv@gmail.com | 1173957281@qq.com>
 */
function cutImage($img_url,$save_path,$save_name,$crop=['x1'=>0,'y1'=>0,'w'=>200,'h'=>200],$thumb=['w'=>200,'h'=>200])
{
    $image = new \Think\Image(\Think\Image::IMAGE_GD,$img_url); // GD库
    createDir($save_path);
    $save_url = $save_path.$save_name;
    $image->crop($crop['w'],$crop['h'],$crop['x1'],$crop['y1'])->thumb($thumb['w'],$thumb['h'])->save($save_url);
    return $save_url;
}

/**
 * 获取当前页面完整URL地址
 */
 function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
 }

 /**
 * 调试输出|调试模式下
 * @param  mixed $test 调试变量
 * @param  int $style 模式
 * @param  int $stop 是否停止
 * @return void       浏览器输出
 * @author wodrow <wodrow451611cv@gmail.com | 1173957281@qq.com>
 */
function _vp($test,$style=0,$stop=0)
{
    switch ($style) {
    	case 0:
    		echo "<pre>";
    		var_dump($test);
    		echo "</pre>";
    		break;
    		
        case 1:
            echo "<pre>";
            var_dump($test);
            echo "<hr/>";
            for($i=0;$i<100;$i++){
            	echo $i."<hr/>";
            }
            echo "</pre>";
            break;

        case 2:
            file_put_contents(C("DOCUMENT_ROOT").RUNTIME_PATH.'/_vpFile.php', "<? \r".var_export($test, true));
            break;

        case 3:
            file_put_contents(C("DOCUMENT_ROOT").RUNTIME_PATH.'/_vpFile.php', "\r\r".var_export($test, true),FILE_APPEND);
            break; 
    }
    if ($stop!=0) {
    	exit("<hr/>");
    }
}