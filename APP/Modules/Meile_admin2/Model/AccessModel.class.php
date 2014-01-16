<?php

class AccessModel extends Model {

    public function nodeList() {
        import('@.ORG.Category');
        $cat = new Category('Node', array('id', 'pid', 'title', 'fullname'));
        $temp = $cat->getList();               //获取分类结构
        $level = array("1" => "项目（GROUP_NAME）", "2" => "模块(MODEL_NAME)", "3" => "操作（ACTION_NAME）");
        foreach ($temp as $k => $v) {
            $temp[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            $temp[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
            $temp[$k]['level'] = $level[$v['level']];
            $list[$v['id']] = $temp[$k];
        }
        unset($temp);
        return $list;
    }

    public function roleList() {
        $M = M("Role");
        $list = $M->select();
        foreach ($list as $k => $v) {
            $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
            $list[$k]['chStatusTxt'] = $v['status'] == 0 ? "启用" : "禁用";
        }
        return $list;
    }

    public function opStatus($op = 'Node') {
        $M = M("$op");
        $datas['id'] = (int) $_GET["id"];
        $datas['status'] = $_GET["status"] == 1 ? 0 : 1;
        if ($M->save($datas)) {
            return array('status' => 1, 'info' => "处理成功", 'data' => array("status" => $datas['status'], "txt" => $datas['status'] == 1 ? "禁用" : "启动"));
        } else {
            return array('status' => 0, 'info' => "处理失败");
        }
    }

    public function editNode() {
        $M = M("Node");
//        $map['level']=$_POST['level'];
//        $map['pid']=$_POST['pid'];
//        $map['name']=$_POST['name'];
        return $M->save($_POST) ? array('status' => 1, info => '更新节点信息成功', 'url' => U('Access/nodeList')) : array('status' => 0, info => '更新节点信息失败');
    }

    public function addNode() {
        $M = M("Node");
        return $M->add($_POST) ? array('status' => 1, info => '添加节点信息成功', 'url' => U('Access/nodeList')) : array('status' => 0, info => '添加节点信息失败');
    }

    /**
      +----------------------------------------------------------
     * 管理员列表
      +----------------------------------------------------------
     */
    public function adminList() {
        import('ORG.Util.Page');// 导入分页类
        $user=M('user');
        $where="";

        $count      = $user->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出

        $list=$user->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['statusTxt'] = $v['status'] == 1 ? "启用" : "禁用";
        }
        return $list;
    }

    /**
      +----------------------------------------------------------
     * 添加管理员
      +----------------------------------------------------------
     */
    public function addAdmin() {
        if (!is_email($_POST['email'])) {
            return array('status' => 0, 'info' => "邮件地址错误");
        }
        $datas = array();
        $M = M("user");
        $datas['email'] = trim($_POST['email']);
        if ($M->where("`email`='" . $datas['email'] . "'")->count() > 0) {
            return array('status' => 0, 'info' => "已经存在该账号");
        }
        $datas['pwd'] = encrypt(trim($_POST['pwd']));
        $datas['time'] = time();
        if ($M->add($datas)) {
         //   $body = "你的账号已开通，登录地址：" . C('WEB_ROOT') . U("Public/index") . "<br/>登录账号是：" . $datas["email"] . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;登录密码是：" . $_POST['pwd'];
         //   $info = send_mail($datas["email"], "", "开通账号", $body) ? "添加新账号成功并已发送账号开通通知邮件" : "添加新账号成功但发送账号开通通知邮件失败";
            return array('status' => 1, 'info' => $info, 'url' => U("Access/index"));
        } else {
            return array('status' => 0, 'info' => "添加新账号失败，请重试");
        }
    }

    /**
      +----------------------------------------------------------
     * 添加管理员
      +----------------------------------------------------------
     */
    public function editAdmin() {
        $user = D("User");
        $user_id = (int) $_POST['id'];
        if (!empty($_POST['password'])) {
            $_POST['password'] = encrypt(trim($_POST['password']));
        } else {
            unset($_POST['password']);
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
            $avatar='/avatar/'.$info[0]['savename'];
        }
        $data=$_POST;
        $data['avatar']=$avatar;
        $data['entry_time']=strtotime(I('entry_time'));
        if(!$user->create()){
            return  array('status' => 0, 'info' =>$user->getError());
        }
        if ($user->save($data)) {
            return  array('status' => 1, 'info' => "成功更新");
        } else {
            return  array('status' => 0, 'info' => "更新失败，请重试");
        }
    }

    /**
      +----------------------------------------------------------
     * 添加管理员
      +----------------------------------------------------------
     */
    public function editRole() {
        $M = M("Role");
        if ($M->save($_POST)) {
            return array('status' => 1, 'info' => "成功更新", 'url' => U("Access/roleList"));
        } else {
            return array('status' => 0, 'info' => "更新失败，请重试");
        }
    }

    /**
      +----------------------------------------------------------
     * 添加管理员
      +----------------------------------------------------------
     */
    public function addRole() {
        $M = M("Role");
        if ($M->add($_POST)) {
            return array('status' => 1, 'info' => "成功添加", 'url' => U("Access/roleList"));
        } else {
            return array('status' => 0, 'info' => "添加失败，请重试");
        }
    }

    public function changeRole() {
        $M = M("Access");
        $role_id = (int) $_POST['id'];
        $M->where("role_id=" . $role_id)->delete();
        $data = $_POST['data'];
        if (count($data) == 0) {
            return array('status' => 1, 'info' => "清除所有权限成功", 'url' => U("Access/roleList"));
        }
        $datas = array();
        foreach ($data as $k => $v) {
            $tem = explode(":", $v);
            $datas[$k]['role_id'] = $role_id;
            $datas[$k]['node_id'] = $tem[0];
            $datas[$k]['level'] = $tem[1];
            $datas[$k]['pid'] = $tem[2];
        }
        if ($M->addAll($datas)) {
            return array('status' => 1, 'info' => "设置成功", 'url' => U("Access/roleList"));
        } else {
            return array('status' => 0, 'info' => "设置失败，请重试");
        }
    }

     function getOption($model,$info = array()) {
         import('@.ORG.Category');
        $cat = new Category($model, array('id', 'name', 'remark'));
        $list = $cat->getList();
             //     dump($list);//获取分类结构
        $info['Option'] = "<option value='' >----</option>";
        foreach ($list as $v) {
            $disabled = $v['id'] == 0 ? ' disabled="disabled"' : "";
            $selected = $v['id'] == $info['id'] ? ' selected="selected"' : "";
            $info['option'].='<option value="' . $v['id'] . '"' . $selected . $disabled . '>' . $v['name'] . '</option>';
        }
        return $info;
    }

}

?>
