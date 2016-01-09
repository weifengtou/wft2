<?php
namespace Admin\Controller;
class UserController extends AdminController {

    private $navName=User; //所在导航name

	public function _initialize()
	{
		parent::_initialize();
        $this->getThisNavMenu($this->navName);
    }

    public function index()
    {
        $this->display();
    }

    // 成员列表
    public function listMem()
    {
        $mems = M('AdminUser')->select();
        $this->assign('mems',$mems);
        $this->display();
    }
    // 成员所属角色
    public function belongsToRole()
    {
        if (!$mem_id=I("post.mem_id")) {
            echo "非法访问";
            exit();
        }else{
            $x['mem_id'] = $mem_id;
            $x['role_ids'] = M('AdminRoleUser')->field("role_id")->where(['user_id'=>$mem_id])->find();
            if ($x['role_ids']) {
                $x['to_roles'] = M("AdminRole")->where(['id'=>['in',$x['role_ids']]])->select();
            }else{
                $x['to_roles'] = [];
            }
        }
        $this->assign('info',$x);
        $this->display("User/includes:belongsToRole");
        exit();
    }

    // 添加成员
    public function addMem()
    {
        if (IS_POST) {
            $x['status'] = "error";
            $model = D('AdminUserOpt');
            $data = $model->create();
            if ($data) {
                $id = $model->data($data)->add();
                if ($id) {
                    $x['status'] = "success";
                    $x['id'] = $id;
                }else{
                    $x['msg'] = $model->getError();
                }
            }else{
                $x['msg'] = $model->getError();
            }
            echo json_encode($x);
            exit();
        }
        $this->display();
    }

    // 角色列表
    public function listRole()
    {
        $this->assign("roles",M("AdminRole")->select());
        $this->display();
    }

    // 添加角色
    public function addRole()
    {
        if (IS_POST) {
            $x['status'] = "error";
            $model = D('AdminRoleOpt');
            $data = $model->create();
            if ($data) {
                $id = $model->data($data)->add();
                if ($id) {
                    $x['status'] = "success";
                    $x['id'] = $id;
                }else{
                    $x['msg'] = $model->getError();
                }
            }else{
                $x['msg'] = $model->getError();
            }
            echo json_encode($x);
            exit();
        }
        $this->display();
    }

    // 节点列表
    public function listNode()
    {
        $this->assign("nodes",get_tree_node_sort(list_to_tree(M("AdminNode")->select())));
        $this->display();
    }

    // 添加节点
    public function addNode()
    {
        if (IS_POST) {
            $x['status'] = "error";
            $model = D('AdminNodeOpt');
            $data = $model->create();
            if ($data) {
                $id = $model->data($data)->add();
                if ($id) {
                    $x['status'] = "success";
                    $x['id'] = $id;
                }else{
                    $x['msg'] = $model->getError();
                }
            }else{
                $x['msg'] = $model->getError();
            }
            echo json_encode($x);
            exit();
        }
        $this->display();
    }
}