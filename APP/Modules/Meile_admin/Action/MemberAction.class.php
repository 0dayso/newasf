<?php
// 后台用户模块
class MemberAction extends CommonAction {
	function index(){
        if(!I('user_id')){
            $map=D("UserAdmin")->userLevelWhere();
        }
        if(I('so')){
            $where['name'] = array('like',"%".I('so')."%");
            $where['username']  = array('like',"%".I('so')."%");
            $where['mobile']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        if(I('so_date1')&& I('so_date2')){
            $map['create_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }
        $this->map=$map;
        $this->order='id desc';
        $this->relation = true;
        parent::index(D("Member"));

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

	function points(){

		//数据提交处理
		$pointsDB=D('Points');

		$uid=I('id');
		$points=I('points');
		$description=I('description');
		$type2=I('type2');

		if(!empty($_POST)){
			if($points == ""){
				$this->error("请输入数字");
			}
			if($description == ""){
				$this->error("请输入理由");
			}
			if(I('type') == 0){
				$rs=$pointsDB->addPoints($uid,$points,$description,$type2);
				if($rs===true){
					$this->success('操作成功！');
				}
			}else {
				$rs=$pointsDB->cutPoints($uid,$points,$description,$type2);
				if($rs===true){
					$this->success('操作成功！');
				}
			}
			$this->error('操作失败！'.$pointsDB->getError());
		}

		//查询
		//从member里查询username
		$id=I('id');
		$member=D('Member');
		$condition['id']=$id;
		$this->mem=$member->field('username')->where($condition)->find();

		$points=D('Points');

		//从points取5条描述
		$where['member_id']=$id;
		$this->description=$points->field('description,create_time')->where($where)->order('create_time DESC')->limit(5)->select();

		//积分操作
		$where1['member_id']= $id;
		$where1['type2']=0;

		$where1['type']=0;
		$this->addjifen=$points->where($where1)->sum('points');//增加积分总数
        $this->addjifen=$this->addjifen?$this->addjifen:0;
		$where1['type']=1;
		$this->cutjifen=$points->where($where1)->sum('points');//消费积分总数
        $this->cutjifen=$this->cutjifen?$this->cutjifen:0;

        $this->totle_jifen=$this->addjifen+($this->cutjifen); //积分返还总数


		//爱钻操作
		$where2['member_id']= $id;
		$where2['type2']=1;
		$where2['type']=0;
		$this->addaizuan=$points->where($where2)->sum('points');//增加爱钻总数
		$where2['type']=1;
		$this->cutaizuan=$points->where($where2)->sum('points');//消费爱钻总数
		$this->totel_aizuan=$this->addaizuan+($this->cutaizuan);//总爱钻

		$this->display();
	}


	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z0-9]\w{4,}$/i',$_POST['username'])) {
            $this->error( '用户名必须是字母数字，且5位以上！');
        }
		$User = M("Member");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['username'];
        $result  =  $User->getByAccount($name);
        if($result){
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }

	// 插入数据
	public function insert2() {
		// 创建数据对象
		$User	 =	 D("Member");
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


    function edit(){
        $M = D("Member");
        $id = (int) $_GET['id'];
        $info = $M->where("id=".$id)->relation(true)->find();
        if (empty($info['id'])) {
            $this->error("不存在ID");
        }
        $this->vo=$info;
        $this->display();


    }

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = M('Member');
	//	$User->password	=	md5($password);
        $salt=generateSalt();

        $User->salt=$salt;  // 设置salt字段值
        $User->password = hashPassword($password,$salt);
		$User->id			=	$id;
		$result	=	$User->save();
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }

    public function insert() {
        if (IS_POST){
            $user=D('Member');
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
            $id=$user->add(); //插入数据库
            if($id){
                $this->addRole($id);
                $this->success("提交成功");exit;
            }else{
                $this->error("提交失败");
            }
        }
    }

    /*
     * 会员邀请  统计
     */
    function memberInvite(){
        $year=I('year')?I('year'):$_REQUEST['year']=date("Y");
        $month=I('month')?I('month'):$_REQUEST['month']=date("m");
        $date1=$year."-".$month."-"."01";
        $_REQUEST['so_date1']=$date1;
        $date2 = $month==12?($year+1)."-1-01":$year."-".($month+1)."-01";
        $_REQUEST['so_date2']=$date2;
        $date1=strtotime($date1);
        $date2 = strtotime($date2);
        $regDate=" m.create_time>$date1 and m.create_time<$date2 and";

        //搜索条件
        $userName=I('so')?"(u.name='".I('so')."' or u.username='" . I('so')."' ) and ":'';
        $where=$userName.$regDate;
        $M=M();
        //所有的
        $sql="select u.id,u.name,u.department_id,d.name department,u.status,count(m.id) count from asf_user u left join asf_department d on u.department_id=d.id  left join asf_member m on m.user_id=u.id where $where m.user_id>0  group by m.user_id ";
      //  是通过邀请注册的
        $sql2="select u.id,u.name,count(m.id) invite_count from asf_user u left join asf_member m on m.user_id=u.id where $where m.user_id>0 and m.invite_id>1 group by m.user_id ";
        //邀请注册后有订票的
        $sql3="select u.id,u.name,count(m.id) effective from asf_user u left join asf_member m on m.user_id=u.id where $where m.user_id>0 and m.invite_id>1 and (select o.zf_fkf from asf_asms_order o where o.hyid=m.asms_member_id and o.zf_fkf=1 limit 1) is not null group by m.user_id ";
        //  直接订票会员数
        $sql4="select u.id,u.name,count(m.id) direct from asf_user u left join asf_member m on m.user_id=u.id where $where m.user_id>0 and m.invite_id=0 and (select o.zf_fkf from asf_asms_order o where o.hyid=m.asms_member_id and o.zf_fkf=1 limit 1) is not null group by m.user_id ";

        $arr=array();
        //执行sql
        $arr=$M->query($sql);
        $arr2=$M->query($sql2);
        $arr3=$M->query($sql3);
        $arr4=$M->query($sql4);
        //组和查询结果
     //  print_r($arr);
        foreach($arr as $key=>$val){
            $arr[$key]['effective']= 0;
            $arr[$key]['reward']=0;
            $arr[$key]['invite_count']=0;
            $arr[$key]['direct']=0;
            foreach($arr2 as $k=>$v){
                if($val['id']==$v['id']){
                    $array[$key]['invite_count']=$v['invite_count'];
                }
            }

            foreach($arr3 as $k=>$v){
                if($val['id']==$v['id']){
                    $arr[$key]['effective']=$v['effective'];
                    $arr[$key]['reward']=$v['effective']?$v['effective']*20:0;
                }
            }
            foreach($arr4 as $k=>$v){
                if($val['id']==$v['id']){
                $arr[$key]['direct']=$v['direct'];
                }
            }

        }

        foreach($arr as $val){
            $arrs[$val['id']]=$val;
        }
      //  print_r($arr);
        $this->arr=$arrs;
        //创建临时表
        $sql="CREATE TEMPORARY TABLE asf_tmp_table ( id int(10) ,status tinyint(1) DEFAULT 0,department_id int(10)  DEFAULT 0,`count` int(10) DEFAULT 0,direct int(10) DEFAULT 0, invite_count int(10) DEFAULT 0,effective int(10) DEFAULT 0,reward float(10,2))";
        $M->query($sql);
        $tmp_table= M('tmp_table');
        $tmp_table->addall($arr);//写入临时表
     //   dump($tmp_table->getDbError());
      //  print_r(M('tmp_table')->select());
        $access=D('Access');
        $info['departmentOption']=$access->getOption('department',array('id'=>I('department_id')));
        $this->info=$info;

        parent::index(M('tmp_table'));
        $this->display();

    }

}
?>