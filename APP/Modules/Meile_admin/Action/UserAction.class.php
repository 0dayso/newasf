<?php
// 后台用户模块
class UserAction extends CommonAction {
	function index(){
        $map=D("UserAdmin")->userLevelWhere('id');
        if(I('so')){
            if(strstr(I('so'),':')){
                $so=explode(':',I('so'));
                $map[$so[0]]=$so[1];
            }else{
                $where['name'] = array('like',"%".I('so')."%");
                $where['username']  = array('like',"%".I('so')."%");
                $where['public_mobile']  = array('like',"%".I('so')."%");
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
            }
        }

        if(!isset($_POST['status']) && !isset($_GET['status'])){
            $map['status'] = 1;
            $_REQUEST['status']=1;
        }
        if(!isset($map['id']))  $map['id'] = array('egt',2);

        $this->map = $map;
        $access=D('Access');
        $info['companyOption']=$access->getOption('company',array('id'=>I('company_id')));
        $info['departmentOption']=$access->getOption('department',array('id'=>I('department_id')));
        $info['positionOption']=$access->getOption('position',array('id'=>I('position_id')));
        $this->info=$info;
        $this->relation=array('department','asms_user','member');
        parent::index(D('UserAdmin'));

     //   print_r($this->list);
        $this->display();


	}

    function add(){
        $access=D('Access');
        $info['companyOption']=$access->getOption('company',array('id'=>I('company_id')));
        $info['departmentOption']=$access->getOption('department',array('id'=>I('department_id')));
        $info['positionOption']=$access->getOption('position',array('id'=>I('position_id')));
        $this->vo=$info;
        $this->display();
    }


	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z0-9]\w{4,}$/i',$_POST['username'])) {
            $this->error( '用户名必须是字母数字，且5位以上！');
        }
		$User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['username'];
        $result  =  $User->getByAccount($name);
        if($result){
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    //设置权限
    function setRole(){
        if($_POST){
            $where['user_id']=I('uid');
            D('RoleUser')->where($where)->delete();
            $user= D('User');
            $data['id']=I('id');
            $data['status']=I('status');
            $data['role_level']=I('role_level');
            $user->create($data);
            $user->save();
            $rs=D('Role')->setGroupsUser(I('gid'),I('uid'));
            if($rs){
                $this->success('成功！');
            }else {
                $this->error('失败！');
            }
        }else{
            $this->vo=D('User')->field('id,username,name,status,role_level')->find(I('id'));
            $role=D('Role')->select();
            $where['user_id']=$_GET['id'];
            $checkRole=D('RoleUser')->where($where)->select();
        //    print_r($checkRole);
            foreach($role as $k=>$rv){
                $roles[$rv['id']]=$rv;
                foreach($checkRole as $uv ){
                 //   print_r($rv);
                    if($rv['id']==$uv['role_id']){
                        $roles[$rv['id']]['checked']=1;
                    }
                }
            }
          //      print_r($roles);
         $this->role=$roles;
            $this->display();
        }

    }
	// 插入数据
	public function insert2() {
		// 创建数据对象
		$User	 =	 D("User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

	protected function addRole($userId){
		//新增用户自动加入相应权限组
		$RoleUser = M("RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入默认组
        $RoleUser->role_id	=1;
		$RoleUser->add();
	}

    function upload(){
        parent::upload('','','avatar');
    }

    /*
     * 禁用
     */
    function forbid(){
        $where['user_id']=I('get.id');
        if(D('Member')->field('id')->where($where)->count()){
            $this->error('还有关联会员 不能禁用');
        }
        parent::forbid();
    }

    /*
     * 编辑
     */
    function edit(){
        $M = M("user");
        $id = (int) $_GET['id'];
        $pre = C("DB_PREFIX");
        $info = $M->where("id=".$id)->find();

        if (empty($info['id'])) {
            $this->error("不存在该管理员ID", U('Access/index'));
        }
        if ($info['email'] == C('ADMIN_AUTH_KEY')) {
            $this->error("超级管理员信息不允许操作", U("Access/index"));
            exit;
        }

        $access=D('Access');
        $info['companyOption']=$access->getOption('company',array('id'=>$info['company_id']));
        $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
        $info['positionOption']=$access->getOption('position',array('id'=>$info['position_id']));
        $this->vo=$info;
     //   print_r($info);
        $this->display();


    }

    //重置密码
    public function resetPwd()
    {
        if(C('VERIFY_CODE') && I('verify','','md5') != $_SESSION['verify'])
        $this->error('验证码错误！');
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = M('User');
	//	$User->password	=	md5($password);
        $salt=generateSalt();

        $User->salt=$salt;  // 设置salt字段值
        $User->password = hashPassword($password,$salt);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            //记录行为
            action_log('admin_resetPwd', 'user', $id, getUid(),$this);
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }

    public function insert() {
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
                $upload->thumbMaxWidth = '100,50';
                //设置缩略图最大高度
                $upload->thumbMaxHeight = '100,50';
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
                $this->addRole($id);
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

    /*
 * 用户数据
 */
    function userData(){
        if(I('so')){
            $where['name'] = array('like',"%".I('so')."%");
            $where['username']  = array('like',"%".I('so')."%");
            $where['email']  = array('like',I('so'));
            $where['public_mobile']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        $this->map=$map;
        $this->map['id'] = array('egt',2);
        $this->map['status'] = 1;
        $access=D('Access');
        $info['companyOption']=$access->getOption('company',array('id'=>I('company_id')));
        $info['departmentOption']=$access->getOption('department',array('id'=>I('department_id')));
        $info['positionOption']=$access->getOption('position',array('id'=>I('position_id')));
        $this->info=$info;
        $this->relation=true;
        parent::index();
    }

    /*
     * 关联会员 转移
     */
    function transfer(){
        $UserAdmin=D('UserAdmin');
        $memberDB=D("Member");
        $userDB=D('User');
        $urs= $userDB->find(I('id'));
        if(IS_POST){
            if(C('VERIFY_CODE') && I('verify','','md5') != $_SESSION['verify'])
            $this->error('验证码错误！');
            //指定业务员
            $where['user_id']=I('id');
           if(!I('auto') && I('to_user_id')){
               $wh['user_id']=I('id');
               $data['user_id']=I('to_user_id');
               $memberDB->where($wh)->save($data);
               $this->success('成功');
           }else{//自动分配
               $arr=$memberDB->field('id,invite_id')->where($where)->select();
                //转
               $array= list_to_tree($arr,0,'id','invite_id');
               $this->log['old']=$array;
               foreach($array as $val){
                    if(is_array($val['_child'])){
                        $da['id']=$val['id'];
                        $da['invite_id']=$val['invite_id'];
                        $a[]=$da;
                        $a[]=tree_to_list($val['_child'], $child = '_child');
                    }else{
                       $a[]=$val;
                    }
                }
                foreach($a as $key=>$val){
                   if(is_array($val) && isset($val['id'])){
                       $arrs[]=$val['id'];
                   }else{
                       $temp=array();
                       foreach($val as $k=>$v){
                           $temp[]=$v['id'];
                       }
                       $arrs[]=implode(',',$temp);
                   }
                }
                foreach($arrs as $key=>$val){
                    $wh['id']=array('in',$val);
                    $data=array();
                    $data['user_id']=$userDB->autoUserid($urs['company_id'],$urs['department_id'],I('id'));
                    $ar[]=$memberDB->where($wh)->save($data);
                }
               if(!empty($ar)) $this->success('成功');
           }
            //记录行为
            action_log('admin_transfer', 'member', $id, getUid(),$this);
            $this->error('失败');
        }else{
            $this->vo=$urs;
            $this->display();
        }
    }


}
?>