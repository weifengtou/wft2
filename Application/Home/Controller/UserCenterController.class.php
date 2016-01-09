<?php

namespace Home\Controller;

class UserCenterController extends HomeController {

    // 用户id(int)
    private $user_id;
    // 用户详细(array)
    private $userDetail;
    // 项目id(int)
    private $project_id;
    // 项目详情(array)
    private $projectDetail;

    public function _initialize() {
        parent::_initialize();
        if (!session('userDetail')) {
            $this->error("非法访问!", U('Index/index'));
        } else {
            $this->userDetail = $userDetail = session('userDetail');
            $this->user_id = $userDetail[0]['user_id'];
        }
        //输出用户基本信息
        $this->assign("userBase",$this->userBase);
        //根据当前时间，显示问候语
        date_default_timezone_set('Asia/Shanghai');
        $h=date("H");
        if($h<11){
        	$hello = "早上好！";
        } else if($h<13){
        	$hello = "中午好！";
        } else if($h<17){
        	$hello = "下午好！";
        } else {
        	$hello = "晚上好！";
        }
        $this->assign("hello",$hello);
        //判断用户名长度，对用户名进行截取
        $length_nickname = strlen($this->userBase[nick_name]);
        if($length_nickname > 6){
        	$nickname = substr($this->userBase[nick_name],0,6)."···";
        	$this->assign("nickname",$nickname);
        }else{
        	$this->assign("nickname",$this->userBase[nick_name]);
        }
        //我的项目数
        $Project = M("ProjectBase");
        $pro_count = $Project->where("user_id = '$this->user_id'")->count();
        $this->assign("pro_count",$pro_count);
       	
    }

    //获取临时项目数据
    private function getTmpProject() {
        $tmp_project = M('ProjectBase')->where(['pro_name' => '', 'user_id' => $this->user_id])->select();
        if (count($tmp_project) == 1) {
            $this->project_id = $tmp_project[0]['project_id'];
            $this->projectDetail = get_pro_detail($this->project_id);
            if (!$this->__isset('projectDetail')) {
                $this->assign('projectDetail', $this->projectDetail);
            }
        }
        if (count($tmp_project) > 1) {
            exit('error2');
        }
    }

    //根据所传的数据project_id获取操作的项目
    private function getProject($bind = ['Base']) {
        if (I("get.project_id")) {
            $this->project_id = I("get.project_id");
            if (!M('ProjectBase')->where(['user_id'=>$this->user_id,'project_id'=>$this->project_id])->find()) {
                $this->error("非法操作");
            }else{
                $this->projectDetail = get_pro_detail($this->project_id, "project_id", $bind);
                if (!$this->__isset('projectDetail')) {
                    $this->assign('projectDetail', $this->projectDetail);
                }
            }
        } else {
            $this->error("非法操作");
        }
    }

    //个人中心首页
    public function index() {
    	//登录信息
    	$user_log = M("UserLog");
    	$res = $user_log -> where("user_id = '$this->user_id' && action_id ='2'")->order('log_id desc')->limit(2)->select();
    	//根据登录记录判断是否是首次登录
    	if($res[1]){
    		$this->assign("fors","上次登录：");
    		$this->assign("city",$res[1][log_ip]);
    		$this->assign("year",date('Y',$res[1][log_time]));
    		$this->assign("month",date('m',$res[1][log_time]));
    		$this->assign("day",date('d',$res[1][log_time]));
    		$this->assign("hour",date('H',$res[1][log_time]));
    		$this->assign("min",date('i',$res[1][log_time]));
    		$this->assign("sec",date('s',$res[1][log_time]));
    	}else{
    		$this->assign("fors","首次登录：");
    		$this->assign("city",$res[0][log_ip]);
    		$this->assign("year",date('Y',$res[0][log_time]));
    		$this->assign("month",date('m',$res[0][log_time]));
    		$this->assign("day",date('d',$res[0][log_time]));
    		$this->assign("hour",date('H',$res[0][log_time]));
    		$this->assign("min",date('i',$res[0][log_time]));
    		$this->assign("sec",date('s',$res[0][log_time]));
    	}
    	//今天签到积分显示
    	$score_log = M("ScoreLog");
    	$res = $score_log->where("user_id = '$this->user_id' && type = '1'")->order("id desc")->select();
		$num = $res[0][num];
		echo $num."<br>";
		echo strtotime(date("Y-m-d"))-$res[0][time];
    	if(!$res||$num=0){//用户注册后第一次签到或是未连续签到
    		$score = 5;
    	}else if($num=1||$num=2){//连续签到第2、3天
    		$score=5+3*$num;
    	}else if($num=3||$num=4){//连续签到第3、4天
    		$score=5+3*2+4*($num-2);
    	}else if($num=5||$num=6){//连续签到第5、6天
    		$score=5+3*2+4*2+5*($num-4);
    	}else if($num>6){//连续签到7天或以上
    		$score=5+3*2+4*2+5*2;
    	}
    	$this->assign("score",$score);
    	
    	//所有项目
    	$Project = M("ProjectBase");
    	$myProjects = $Project->where("user_id = '$this->user_id'")->limit(2)->order("project_id desc")->select();
    	$this->assign("myProjects",$myProjects); 
        $this->display();
    }
    
    //签到积分
    function signIn(){
    	$score=$_POST;
    	$score_log=M("ScoreLog");
    	$res = $score_log->where("user_id = '$this->user_id' && type = '1'")->order("id desc")->select();
		$num = $res[0][num];
		if($strtotime(date("Y-m-d"))-$res[0][time]=86400&&$score>5){
			$num = $num+1;
		}else{
			$num=0;
		}
    	$data["score"]=$score;
    	$data["user_id"]=$this->user_id;
    	$data["title"]="签到";
    	$data["type"]="1";
    	$data[num]=$num;
    	$data["time"]=strtotime(date("Y-m-d"));//当天时间转化为时间戳
    	$res = $score_log->add($data);
    	if($res){
    		echo "1";
    	};
    }

    //快速创建项目
    public function projectCreate() {
        $this->getTmpProject();
        if (IS_POST) {
            if (!$this->project_id) {
                $this->error("非法操作");
                exit;
            }
            $projectBaseModel = D('ProjectBase');
            if ($data = $projectBaseModel->create()) {
                if ($projectBaseModel->where(['project_id' => $this->project_id])->save($data) !== false) {
                    echo $this->project_id;
                } else {
                    dump($projectBaseModel->getError());
                }
            } else {
                dump($projectBaseModel->getError());
            }
            exit();
        }
        $this->display();
    }

    //我的项目
    public function myProjects() {
        /* $myProjects = M("ProjectBase")->where("user_id='" . $this->user_id . "' AND pro_name <> ''")->select();
        $this->assign('myProjects', $myProjects); */
 		
    	//所有项目
    	$Project = M("ProjectBase");
    	$myProjects = $Project->where("user_id = '$this->user_id'")->select();
    	$this->assign("myProjects",$myProjects); 
        $this->display();
    }
    //我的项目
    public function user_projects(){
    	$rate = $_POST["rate"];
    	$status = $_POST["status"];
    	$project = M("ProjectBase");
    	if($rate == 0 && $status == 1){
    		//echo "11";审核中
    		$res = $project->table("wft_project_base base,wft_project_advanced advanced")->where("base.user_id = '$this->user_id' AND base.status = '$status' AND advanced.pro_rate = '$rate' AND base.project_id = advanced.project_id ")->field("base.project_id,base.pro_name,base.pro_banner,base.total_fin,base.status,advanced.pro_rate")->select();
    		echo json_encode($res);
    		//echo $project->getLastSql();
    	}else if($rate == 1){
    		//echo "22";发布成功
    		$res = $project->table("wft_project_base base,wft_project_advanced advanced")->where("base.user_id = '$this->user_id' AND advanced.pro_rate > '$rate' AND base.project_id = advanced.project_id ")->field("base.project_id,base.pro_name,base.pro_banner,base.total_fin,base.status,advanced.pro_rate")->select();
    		echo json_encode($res);
    	}else if($rate == -1){//审核失败
    		$res = $project->table("wft_project_base base,wft_project_advanced advanced")->where("base.user_id = '$this->user_id' AND advanced.pro_rate = '$rate' AND base.project_id = advanced.project_id ")->field("base.project_id,base.pro_name,base.pro_banner,base.total_fin,base.status,advanced.pro_rate")->select();
    		echo json_encode($res);
    	}else if($status == 0){//未提交
    		$res = $project->table("wft_project_base base")->where("base.user_id = '$this->user_id' AND base.status = '$status' ")->field("base.project_id,base.pro_name,base.pro_banner,base.total_fin,base.status")->select();
    		echo json_encode($res);
    	}
    }

    //上传项目banner图片
    public function proBannerUpload() {
        if (IS_POST) {
            $this->getTmpProject();
            $config = [
                'rootPath' => RUNTIME_PATH,
                'savePath' => C('UPLOAD2DIR') . '/user_' . $this->user_id . '/banner/', //设置附件上传（子）目录
                'maxSize' => 1024 * 1024 * 2,
                'exts' => ['jpg', 'gif', 'png', 'jpeg'], //上传文件的后缀类型
                'autoSub' => false,
                'replace' => true,
            ];
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $x['status'] = 'error';
                $x['msg'] = $upload->getError();
            } else {// 上传成功 获取上传文件信息
                foreach ($info as $file) {
                    $pro_banner = RUNTIME_PATH . $file['savepath'] . $file['savename'];
                    $data['pro_banner'] = $pro_banner;
                    $x['src'] = $pro_banner;
                }
            }
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }
    //裁剪项目banner图并保存
    public function cutProBanner()
    {
        if (IS_POST) {
            $this->getTmpProject();
            if (!$this->project_id) {
                M()->startTrans();
                $project_id = D('ProjectBase')->data(['user_id' => $this->user_id])->add();
                $x2 = D("ProjectPapers")->data(['project_id'=>$project_id])->add();
                if ($project_id&&$x2) {
                    M()->commit();    
                    $this->project_id = $project_id;
                } else {
                    M()->rollback();
                    $x['status'] = 'error';
                    $x['msg'] = 'add error';
                }
            }
            $data['pro_banner'] = cutImage(I('post.banner'), UPLOAD_ROOT . C('UPLOAD2DIR') . '/user_' . $this->user_id . '/project/' . $this->project_id . '/banner/', time() . ".jpg", ['x1' => round(I("post.x1")), 'y1' => round(I("post.y1")), 'w' => round(I("post.w")), 'h' => round(I("post.h"))], ['w' => 190, 'h' => 105]);
            if (D('ProjectBase')->where(['project_id'=>$this->project_id])->save($data)) {
                $x['status'] = 'success';
                $x['src'] = $data['pro_banner'];
            }else{
                $x['status'] = 'error';
                $x['msg'] = 'save error';
            }
        }else{
            $x['status'] = 'error';
            $x['msg'] = 'save error';
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }

    //项目创建成功跳转
    public function proCreateSuccess() {
        $this->getProject();
        $this->display();
    }

    //完善项目
    public function completeProject() {
        $this->getProject(['Base', 'Member','Papers']);
        $projectDetail = $this->projectDetail;
        $this->assign('project_members', $projectDetail[0]['project_members']);
        $this->display();
    }

    //添加编辑项目详情
    public function optProjectDes($value='')
    {
        if (IS_POST) {
            if (D("projectBase")->where(["project_id"=>$_POST["project_id"]])->data(["pro_des"=>$_POST["pro_des"]])->save()!==false) {
                $x["status"] = "success";
            }else{
                $x["status"] = "error";
                $x["msg"] = "异常";
            }
        }else{
            $x["status"] = "error";
            $x["msg"] = "非法操作";
        }
        $x['sql'] = M()->_sql();
        $x = json_encode($x);
        echo $x;
        exit;
    }

    //添加编辑项目成员
    public function optProjectMember() {
        $projectMemberModel = D('ProjectMember');
        if (I("post.project_id") && I("post.mem_name")) {
            if (I('post.mem_id')) {
                $mem_id = I("post.mem_id");
                $data = $projectMemberModel->create();
                if (I("post.mem_picture") !== I("post.old_img_url")) {
                    $data['mem_picture'] = cutImage(I('post.mem_picture'), UPLOAD_ROOT . C('UPLOAD2DIR') . '/user_' . $this->user_id . '/project/' . I("post.project_id") . '/mem_pictures/', time() . ".jpg", ['x1' => round(I("post.x1")), 'y1' => round(I("post.y1")), 'w' => round(I("post.w")), 'h' => round(I("post.h"))], ['w' => 200, 'h' => 200]);
                }
                if ($projectMemberModel->where(['mem_id' => $mem_id])->save($data) === FALSE) {
                    echo 'error';
                    exit;
                }
            } else {
                $data = $projectMemberModel->create();
                $data['mem_picture'] = cutImage(I('post.mem_picture'), UPLOAD_ROOT . C('UPLOAD2DIR') . '/user_' . $this->user_id . '/project/' . I("post.project_id") . '/mem_pictures/', time() . ".jpg", ['x1' => round(I("post.x1")), 'y1' => round(I("post.y1")), 'w' => round(I("post.w")), 'h' => round(I("post.h"))], ['w' => 200, 'h' => 200]);
                if ($mem_id = $projectMemberModel->data($data)->add()) {
                    // dump($data);
                }else{
                    echo 'error';
                    exit;
                }
            }
        } else {
            echo "非法访问";
        }
        $this->getProject(['Base', 'Member']);
        $projectDetail = $this->projectDetail;
        $this->assign("projectDetail", $projectDetail);
        $project_members = $projectDetail[0]['project_members'];
        foreach ($project_members as $k => $v) {
            if ($v['mem_id'] == $mem_id) {
                $x[0] = $v;
            }
        }
        $project_members = $x;
        $this->assign("project_members", $project_members);
        $this->display("Template:projectMemberList");
        exit;
    }

    //删除项目成员
    public function deleteProjectMember() {
        if ($mem_id = I('post.mem_id')) {
            if (M('ProjectMember')->delete($mem_id)) {
                echo "删除成功";
            } else {
                echo("异常");
            }
        } else {
            echo("非法访问");
        }
    }

    //上传项目成员图片
    public function addMemPicture() {
        $this->getProject();
        if (IS_POST) {
            $config = [
                'rootPath' => RUNTIME_PATH,
                'savePath' => C('UPLOAD2DIR') . '/user_' . $this->user_id . '/project/' . $this->project_id . '/mem_pictures/', //设置附件上传（子）目录
                'maxSize' => 1024 * 1024 * 2,
                'exts' => ['jpg', 'gif', 'png', 'jpeg'], //上传文件的后缀类型
                'autoSub' => false,
                'replace' => true,
            ];
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $x['status'] = 'error';
                $x['msg'] = $upload->getError();
            } else {// 上传成功 获取上传文件信息
                foreach ($info as $file) {
                    $mem_picture = RUNTIME_PATH . $file['savepath'] . $file['savename'];
                }
                $x['status'] = 'success';
                $x['src'] = $mem_picture;
            }
        } else {
            $this->error("非法访问");
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }

    //审核项目资料
    public function reviewProPapers()
    {
        $projectPapersModel = D("ProjectPapers");
        if (IS_POST) {
            if (count($projectPapersModel->where(['project_id'=>I("post.project_id")])->select())<1) {
                $projectPapersModel->where(['project_id'=>I("post.project_id")])->add();
            }
            $config = [
                'rootPath' => UPLOAD_ROOT,
                'savePath' => C('UPLOAD2DIR') . '/user_' . $this->user_id . '/project/' . I("post.project_id") . '/papers/', //设置附件上传（子）目录
                'maxSize' => 1024 * 1024 * 2,
                'exts' => ['jpg', 'gif', 'png', 'jpeg'], //上传文件的后缀类型
                'autoSub' => false,
                'replace' => true,
            ];
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $x['status'] = 'error';
                $x['msg'] = $upload->getError();
            } else {// 上传成功 获取上传文件信息
                foreach ($info as $file) {
                    $mem_picture = UPLOAD_ROOT . $file['savepath'] . $file['savename'];
                }
                $data = $projectPapersModel->create();
                foreach (array_keys($_POST) as $k => $v) {
                    if ($v!=='project_id') {
                        $data[$v] = $mem_picture;
                    }
                }
                if ($projectPapersModel->data($data)->save()) {
                    //提交数据
                    $x = $_POST;
                    $x['status'] = 'success';
                    $x['src'] = $mem_picture;
                }else{
                    $x['status'] = 'error';
                    $x['msg'] = $projectPapersModel->getDbError();
                }
            }
        }else{
            echo "error";
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }
    //项目审核
    public function proReview()
    {
        if (IS_POST) {
            $is_save = D("ProjectBase")->where(["project_id"=>I("post.project_id")])->data(["status"=>1])->save();
            if ($is_save!==false) {
                $x["status"] = "success";
                $x["msg"] = "提交成功，请等待审核结果，成功后能享受我们的服务";
            }else{
                $x['status'] = "error";
                $x["msg"] = M()->getDbError();
            }
        }else{
            $x['status'] = "error";
            $x["msg"] = "提交失败，请检查提交信息";
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }

/* ---------------------------------投资人--------------------------------------------- */
    
    //成为投资人
    public function beInvestor() {
    	$this->isInvestor();
    	$this->display();
    }

    //判断用户是否已申请成为投资人
    public function isInvestor(){
    	$investor = M(InvestorBase);
    	$map['user_id'] = $this->user_id;
    	$result = $investor -> where($map)->find();
    	if($result){
    		$this->redirect("completeInvestor");
    	}else{
    		$this->fatherTrade();
    		$this->inv_quality();
    	}
    }
    
    //获取一级行业
    public function fatherTrade() {
        $trade = M('BaseTrade');
        $fathertrade = $trade->where('trade_pid=0')->select();
        $this->assign('alltrade', $fathertrade);
    }
    
    //获取公司所有性质
    public function inv_quality(){
    	$quality = M("BaseQuality");
    	$allQuality = $quality->select();
    	$this->assign('allQuality',$allQuality);
    }

    //个体投资人
    public function inv_individual() {
        if (IS_POST) {
        	$data['user_id'] = $this->user_id;
            $data['inv_name'] = I('post.inv_name', '', 'htmlspecialchars');
            $data['inv_sex'] = I('post.sex', '', 'htmlspecialchars');
            $data['inv_link'] = I('post.inv_link', '', 'htmlspecialchars');
            $data['province'] = I('post.province', '', 'htmlspecialchars');
            $data['city'] = I('post.city', '', 'htmlspecialchars');
            $data['county'] = I('post.county', '', 'htmlspecialchars');
            $data['max_amount'] = I('post.max_amount', '', 'htmlspecialchars');
            $data['min_amount'] = I('post.min_amount', '', 'htmlspecialchars');
            $data['focus_trade'] = I('post.focus_trade', '', 'htmlspecialchars');
            $data['privacy'] = I('post.privacy', '', 'htmlspecialchars');
            $data['concept'] = I('post.concept', '', 'htmlspecialchars');
            $data['casus'] = I('post.casus', '', 'htmlspecialchars');
            $data['type'] = 1; 
            $data['save_time'] = NOW_TIME;
            $data['save_ip'] = get_client_ip(0);
            $investor = D("InvestorBase");
            if (!$investor->create($data)) {
                echo $investor->getError();
            } else {
             	$investor->add();
             	echo '1';
            }
        }
    }

    //机构投资人
    public function inv_institutional() {
    	if (IS_POST) {
    		$data['user_id'] = $this->user_id;
    		$data['inv_name'] = I('post.inv_name', '', 'htmlspecialchars');
    		$data['inv_quality'] = I('post.quality', '', 'htmlspecialchars');
    		$data['inv_link'] = I('post.inv_link', '', 'htmlspecialchars');
    		$data['province'] = I('post.province', '', 'htmlspecialchars');
    		$data['city'] = I('post.city', '', 'htmlspecialchars');
    		$data['county'] = I('post.county', '', 'htmlspecialchars');
    		$data['max_amount'] = I('post.max_amount', '', 'htmlspecialchars');
    		$data['min_amount'] = I('post.min_amount', '', 'htmlspecialchars');
    		$data['focus_trade'] = I('post.focus_trade', '', 'htmlspecialchars');
    		$data['privacy'] = I('post.privacy', '', 'htmlspecialchars');
    		$data['concept'] = I('post.concept', '', 'htmlspecialchars');
    		$data['casus'] = I('post.casus', '', 'htmlspecialchars');
    		$data['type'] = 2;
    		$data['save_time'] = NOW_TIME;
    		$data['save_ip'] = get_client_ip(0);
    		$investor = D("InvestorBase");
    		if (!$investor->create($data)) {
    			echo $investor->getError();
    		} else {
    			$investor->add();
    			echo '1';
    		}
    	}
    }

    //完善投资人
    public function completeInvestor() {
        $this->display();
    }

    //投资人审核
    public function invReview(){
        $this->display();
    }
    
    //提交投资人认证信息
    public function inv_approve(){
    	if(IS_POST){
    		$data['user_id'] = $this->user_id;
    		$data['true_name'] = I('post.true_name','','htmlspecialchars');
    		//$data['paper_type'] = I('post.paper_type','','htmlspecialchars');
    		$data['paper_num'] = I('post.paper_num','','htmlspecialchars');
    		$data['paper'] = I('post.paper','','htmlspecialchars');
    		$data['status'] = 0;
    		$data['save_time'] = NOW_TIME;
    		$approve = D("InvestorApprove");
    		if(!$approve->create($data)){
    			echo $approve -> getError();
    		}else{
    			$approve -> add();
    			echo '1';
    		}
    	}
    	exit;
    }
    
    //上传投资人身份证正面图
    public function upInvFrontImg(){
        if (IS_POST) {
            $config = [
                'rootPath' => UPLOAD_ROOT,
                'savePath' => C('UPLOAD2DIR') . '/user_' . $this->user_id . '/invFront/', //设置附件上传（子）目录
                'maxSize' => 1024 * 1024 * 2,
                'exts' => ['jpg', 'gif', 'png', 'jpeg'], //上传文件的后缀类型
                'autoSub' => false,
                'replace' => true,
            ];
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $x['status'] = 'error';
                $x['msg'] = $upload->getError();
            } else {
                $x['status'] = 'success';
                foreach ($info as $file) {
                    $y = UPLOAD_ROOT . $file['savepath'] . $file['savename'];
                    $x['src'] = $y;
                } 
            }
        } else {
            $x['status'] = 'error';
            $x['msg'] = "非法访问";
        }
        $x = json_encode($x);
        echo $x;
        exit;
    }
}
