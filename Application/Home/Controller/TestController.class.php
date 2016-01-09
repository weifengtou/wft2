<?php
namespace Home\Controller;
class TestController extends HomeController {

	public function _initialize()
	{
		parent::_initialize();
	}

	public function index()
	{
		W('Test/index');
	}

	//phpmail
	public function test1()
	{
		$to = "1173957281@qq.com";
		$username = "test";
		$subject = "qqq";
		$content = "asdfsd";
		// sendEmail($to, $subject, $content);
	}

	//php动态引用html代码
	public function test2()
	{
		$user_id = 50;
		session(md5("user_id".$user_id),md5("user_id".$user_id),24*3600);
		ob_start();
		W('User/sendActiveEmail',[get_user_detail(50)]);
		$content = ob_get_contents();
		ob_end_clean();
		// dump(get_user_detail(50));
		echo $content;
	}

	//widget 数组传参
	public function test3()
	{
		W('Test/test1',[[1,2,3]]);
	}

	//图片上传
	public function test4()
	{
		$this->display();
	}
	public function test5()
	{
		// if (IS_POST) {
			# code...
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     3145728 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath  =      './Uploads/'; // 设置附件上传根目录
			$upload->savePath  =      ''; // 设置附件上传（子）目录
			// 上传文件 
			$info   =   $upload->upload();
			if(!$info) {// 上传错误提示错误信息
			    $this->error($upload->getError());
			}else{// 上传成功 获取上传文件信息
			    foreach($info as $file){
			        echo $file['savepath'].$file['savename'];
			    }
			}
		// }
	}
	
	//图片裁剪
	public function test6()
	{
		$this->display();
	}
	public function test6upimg()
	{
		if (IS_POST) {
			$config = [
                'rootPath' => RUNTIME_PATH,
                'savePath' => C('UPLOAD2DIR').'/test/', //设置附件上传（子）目录
                'maxSize' => 1024 * 1024 * 2,
                'exts' => ['jpg', 'gif', 'png', 'jpeg'], //上传文件的后缀类型
                'autoSub' => false,
                'replace' => true,
            ];
            $upload = new \Think\Upload($config); // 实例化上传类
            // 上传文件 
            $info = $upload->upload();
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功 获取上传文件信息
                foreach ($info as $file) {
                    $imgurl = RUNTIME_PATH . $file['savepath'] . $file['savename'];
                    echo $imgurl;
                }
            }
		}
		exit;
	}
	public function test6data()
	{
		if (IS_POST) {
			echo cutImage(I("post.img_url"),UPLOAD_ROOT.C('UPLOAD2DIR')."/test/",time().".jpg",['x1'=>round(I("post.x1")),'y1'=>round(I("post.y1")),'w'=>round(I("post.w")),'h'=>round(I("post.h"))],['w'=>200,'h'=>200]);
			// $img_url = I("post.img_url");
			// $image = new \Think\Image(\Think\Image::IMAGE_GD,$img_url); // GD库
			// $save_path = UPLOAD_ROOT.C('UPLOAD2DIR')."/test/";
			// createDir($save_path);
			// $save_name = time().".jpg";
			// $save_url = $save_path.$save_name;
			// $image->crop(round(I("post.h")),round(I("post.w")),round(I("post.x1")),round(I("post.y1")))->thumb(200,200)->save($save_url);
			// echo $save_url;
		}
	}

	//文件夹操作
	public function test7()
	{
		createDir("./Uploads/test/test/");
	}

	public function test8()
	{
		echo "string";
		dump(M('BaseArea')->field('name')->table('BasePrivacy')->group('id')->select());
	}
}