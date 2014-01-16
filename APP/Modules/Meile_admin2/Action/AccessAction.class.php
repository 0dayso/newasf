<?php

class AccessAction extends IniAction {
    /**
    +----------------------------------------------------------
     * 管理员列表
    +----------------------------------------------------------
     */
    public function index() {
        import('ORG.Util.Page');// 导入分页类
        $user=M('user');
        $where="";
        if(I('where')){
            foreach(I('where') as $k=>$v){
                if($v!=''){
                    $where[$k]=$v;
                }
            }
        }
        if(I('search')!=''){
            $search=I('search');
            $where['_string'] = "(username like '%{$search}%')  OR ( name like '%{$search}%') OR ( public_mobile like '%{$search}%') OR ( private_mobile like '%{$search}%') OR ( qq like '%{$search}%')";
        }

        $count      = $user->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,30,'','',1);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出

        $list=$user->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
        }
        $access=D('Access');
        $info['companyOption']=$access->getOption('company',array('id'=>0));
        $info['departmentOption']=$access->getOption('department',array('id'=>0));
        $info['positionOption']=$access->getOption('position',array('id'=>0));
        $this->info=$info;

        $this->list=$list;

        if(I('ss')==1){
            $datalis['list']=$list;
            $datalis['page']=$show;
            $this->ajaxReturn($datalis);exit;
        }
        $this->assign('page',$show);// 赋值分页输出
        $this->display();
    }


    public function nodeList() {
        $this->assign("list", D("Access")->nodeList());
        $this->display();
    }

    public function roleList() {
        $this->assign("list", D("Access")->roleList());
        $this->display();
    }

    public function addRole() {
        if (IS_POST) {
            $this->checkToken();
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(D("Access")->addRole());
        }else{
            $this->assign("info", $this->getRole());
            $this->display("editRole");
        }
    }

    public function editRole() {
        if (IS_POST) {
            $this->checkToken();
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(D("Access")->editRole());
        }else{
            $M = M("Role");
            $info = $M->where("id=" . (int) $_GET['id'])->find();
            if (empty($info['id'])){
                $this->error("不存在该角色", U('Access/roleList'));
            }
            $this->assign("info", $this->getRole($info));
            $this->display();
        }
    }

    public function opNodeStatus() {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(D("Access")->opStatus("Node"));
    }

    public function opRoleStatus() {
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(D("Access")->opStatus("Role"));
    }

    public function opSort(){
        $M = M("Node");
        $datas['id'] = (int) $this->_post("id");
        $datas['sort'] = (int) $this->_post("sort");
        header('Content-Type:application/json; charset=utf-8');
        if ($M->save($datas)) {
            echo json_encode(array('status' => 1, 'info' => "处理成功"));
        } else {
            echo json_encode(array('status' => 0, 'info' => "处理失败"));
        }
    }

    public function editNode() {
        if (IS_POST){
            $this->checkToken();
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(D("Access")->editNode());
        }else{
            $M = M("Node");
            $info = $M->where("id=" . (int) $_GET['id'])->find();
            if (empty($info['id'])) {
                $this->error("不存在该节点", U('Access/nodeList'));
            }
            $this->assign("info", $this->getPid($info));
            $this->display();
        }
    }

    public function addNode() {
        if (IS_POST) {
            $this->checkToken();
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(D("Access")->addNode());
        } else {
            $this->assign("info", $this->getPid(array('level' => 1)));
            $this->display("editNode");
        }
    }

    /**
    +----------------------------------------------------------
     * 添加管理员
    +----------------------------------------------------------
     */
    public function addAdmin() {
        if (IS_POST) {
            $user=D('User');
           if(!$user->create()){
              $this->error($user->getError());
           }

            if($_FILES['avatar']['name']){
                import('ORG.Net.UploadFile');
                $upload = new UploadFile();// 实例化上传类
                $upload->maxSize  = 3145728 ;// 设置附件上传大小
                $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath =  './Public/uploads/avatar/';// 设置附件上传目录
                //设置需要生成缩略图，仅对图像文件有效
                $upload->thumb = true;
                // 设置引用图片类库包路径
                $upload->imageClassPath = 'ORG.Util.Image';
                //设置需要生成缩略图的文件后缀
                $upload->thumbPrefix = 'm_,s_';  //生产2张缩略图
                $upload->thumbMaxWidth = '180,90';
                //设置缩略图最大高度
                $upload->thumbMaxHeight = '180,90';
                //设置上传文件规则
                $upload->saveRule = uniqid;
                //删除原图
                //   $upload->thumbRemoveOrigin = true;
                if(!$upload->upload()) {// 上传错误提示错误信息
                    echo $upload->getErrorMsg();
                }else{// 上传成功 获取上传文件信息
                    $info =  $upload->getUploadFileInfo();
                }
                $user->avatar='/avatar/'.$info[0]['savename'];
            }

            $password=I('post.password');
            $salt=generateSalt();

            $user->salt=$salt;  // 设置salt字段值
            $user->password=hashPassword($password,$salt); //# 对密码进行md5 混合加密
            $user->entry_time=strtotime(I('entry_time'));
            $id=$user->add(); //插入数据库
            if($id){
                $this->success("提交成功");exit;
            }else{
                $this->error("提交失败");

            }
        } else {
            $access=D('Access');
            $info=$this->getRoleListOption(array('role_id' => 0));
            $info['companyOption']=$access->getOption('company',array('id'=>1));
            $info['departmentOption']=$access->getOption('department',array('id'=>5));
            $info['positionOption']=$access->getOption('position',array('id'=>14));

            $this->assign("info", $info);


            $this->display();
        }
    }


    public function changeRole() {
        header('Content-Type:application/json; charset=utf-8');
        if (IS_POST) {
            $this->checkToken();
            echo json_encode(D("Access")->changeRole());
        } else {
            $M = M("Node");
            $info = M("Role")->where("id=" . (int) $_GET['id'])->find();
            if (empty($info['id'])) {
                $this->error("不存在该用户组", U('Access/roleList'));
            }
            $access = M("Access")->field("CONCAT(`node_id`,':',`level`,':',`pid`) as val")->where(C("DB_PREFIX") . "`role_id`=" . $info['id'])->select();
            $info['access'] = count($access) > 0 ? json_encode($access) : json_encode(array());
            $this->assign("info", $info);
            $datas = $M->where("level=1")->select();
            foreach ($datas as $k => $v) {
                $map['level'] = 2;
                $map['pid'] = $v['id'];
                $datas[$k]['data'] = $M->where($map)->select();
                foreach ($datas[$k]['data'] as $k1 => $v1) {
                    $map['level'] = 3;
                    $map['pid'] = $v1['id'];
                    $datas[$k]['data'][$k1]['data'] = $M->where($map)->select();
                }
            }
            $this->assign("nodeList", $datas);
            $this->display();
        }
    }

    /**
    +----------------------------------------------------------
     * 添加管理员
    +----------------------------------------------------------
     */
    public function editAdmin() {
        if (IS_POST) {
         //   $this->checkToken();
            header('Content-Type:application/json; charset=utf-8');
            echo json_encode(D("Access")->editAdmin());
        } else {
            $M = M("user");
            $id = (int) $_GET['id'];
            $pre = C("DB_PREFIX");
            $info = $M->where("`id`=" . $id)->join($pre . "role_user ON " . $pre . "user.id = " . $pre . "role_user.user_id")->find();
            if (empty($info['id'])) {
                $this->error("不存在该管理员ID", U('Access/index'));
            }
            if ($info['email'] == C('ADMIN_AUTH_KEY')) {
                $this->error("超级管理员信息不允许操作", U("Access/index"));
                exit;
            }

            $access=D('Access');
            $this->assign("info", $this->getRoleListOption($info));
            $info['companyOption']=$access->getOption('company',array('id'=>$info['company_id']));
            $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
            $info['positionOption']=$access->getOption('position',array('id'=>$info['position_id']));
            $this->info=$info;
            $this->display("addAdmin");
        }
    }

    private function getRole($info = array()) {
        import('@.ORG.Category');
        $cat = new Category('Role', array('id', 'pid', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        foreach ($list as $k => $v) {
            $disabled = $v['id'] == $info['id'] ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['pid'] ? ' selected="selected"' : "";
            $info['pidOption'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }

    private function getRoleListOption($info = array()) {
        import('@.ORG.Category');
        $cat = new Category('Role', array('id', 'pid', 'name', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        $info['roleOption'] = "";
        foreach ($list as $v) {
            $disabled = $v['id'] == 1 ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['role_id'] ? ' selected="selected"' : "";
            $info['roleOption'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['fullname'] . '</option>';
        }
        return $info;
    }

    private function getPid($info) {
        $arr = array("请选择", "项目", "模块", "操作");
        for ($i = 1; $i < 4; $i++) {
            $selected = $info['level'] == $i ? " selected='selected'" : "";
            $info['levelOption'].='<option value="' . $i . '" ' . $selected . '>' . $arr[$i] . '</option>';
        }
        $level = $info['level'] - 1;
        import('@.ORG.Category');
        $cat = new Category('Node', array('id', 'pid', 'title', 'fullname'));
        $list = $cat->getList();               //获取分类结构
        $option = $level == 0 ? '<option value="0" level="-1">根节点</option>' : '<option value="0" disabled="disabled">根节点</option>';
        foreach ($list as $k => $v) {
            $disabled = $v['level'] == $level ? "" : ' disabled="disabled"';
            $selected = $v['id'] != $info['pid'] ? "" : ' selected="selected"';
            $option.='<option value="' . $v['id'] . '"' . $disabled . $selected . '  level="' . $v['level'] . '">' . $v['fullname'] . '</option>';
        }
        $info['pidOption'] = $option;
        return $info;
    }

}