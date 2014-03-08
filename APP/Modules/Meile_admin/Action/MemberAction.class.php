<?php
// 后台用户模块
class MemberAction extends CommonAction {
	function index(){
        if(!I('user_id')){
            $map=D("UserAdmin")->userLevelWhere();
        }
        if(I('so')){
            if(strstr(I('so'),':')){
                $so=explode(':',I('so'));
                $map[$so[0]]=$so[1];
            }else{
            $where['name'] = array('like',"%".I('so')."%");
            $where['username']  = array('like',"%".I('so')."%");
            $where['mobile']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            }
        }

        if(I('so_date1')&& I('so_date2')){
            $map['create_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }
        $this->map=$map;
        $this->order='id desc';
        $this->relation = true;
        parent::index(D("Member"));	
		
		$info=$this->list;		
		foreach($info as $key=>$val){
			$points=D('Points'); 
			$wh['member_id']=$val['id'];
			
			//积分总数
			$wh['type2']=0;	
			$jifen=$points->where($wh)->sum('points');
			if(empty($jifen)){
				$info[$key]['jifen']=0;
			}else{
				$info[$key]['jifen']=$jifen;
			}
			//爱钻总数
			$wh['type2']=1;	
			$aizuan=$points->where($wh)->sum('points');
			if(empty($aizuan)){
				$info[$key]['aizuan']=0;
			}else{
				$info[$key]['aizuan']=$jifen;
			}
			
			//待支付订单
			$orderDB=D('AsmsOrder');
			$where['hyid']=$val['id'];
			$where['zf_fkf']=0;
			$where['ddzt']=array('not in','7,8'); 
			$where['user_id']=getUid();
			$totel_zf=$orderDB->where($where)->select();
			$info[$key]['totel_zf']=count($totel_zf);//待支付订单数

			if($info[$key]['totel_zf'] != 0){
				foreach($totel_zf as $k=>$v){
					$arrId[]=$v['ddbh'];//待支付订单ID号
				}
			}				
		}
		
		//国际客服部限制会员编辑
		$userInfo=$this->userInfo;		
		if(substr($userInfo['username'],0,3) == 'can'){
			$this->can_bj=1;
		}
		
		//国际商旅部部分功能限制
		if(substr($userInfo['username'],0,1) == 'L'){
			$this->L_xz=1;
		}		
					
		$this->assign('arrId',$arrId);
		$this->assign('info',$info);
 		$this->display();
	}

    function add(){
//        $access=D('Access');
//        $info['companyOption']=$access->getOption('company',array('id'=>I('company_id')));
//        $info['departmentOption']=$access->getOption('department',array('id'=>I('department_id')));
//        $info['positionOption']=$access->getOption('position',array('id'=>I('position_id')));
//        $this->vo=$info;
		if($_POST){			
			if(empty($_POST['mobile'])){
				$this->error('手机号码不能为空');
			}
			if(empty($_POST['password']) || empty($_POST['password2'])){
				$this->error('密码不能为空');
			}
			if($_POST['password'] !== $_POST['password2']){
				$this->error('两次密码输入不一致');
			}
			if(empty($_POST['name'])){
				$this->error('姓名不能为空');
			}			
			if(empty($_POST['username'])){
				$this->error('用户名不能为空');
			}
			$member=D('Member');
			$member->regCheckName($_POST['username']);//检测用户名			
			
			//对接胜意
			$asmsmember=D('AsmsMember');			
			$memberInfo['mobile']=$_POST['mobile'];
			$memberInfo['xm']=$_POST['name'];
			$memberInfo['username']=$_POST['username'];
			$memberInfo['user_id']=getUId();
			$asmsmember->relationReg($memberInfo);
			$res=$asmsmember->checkName($_POST['username']);
			if($res){
				//写入数据库
				$data['asms_member_id']=$res;
				$member=D('member');
				$data['mobile']=$_POST['mobile'];//手机号码				
				$data['salt']='meile';//salt
				$data['password']=hashPassword($_POST['password'],'meile');//密码
				$data['username']=$_POST['username'];//账号,用户名
				$data['name']=$_POST['name'];//姓名
				$data['nickname']=$_POST['nickname'];//昵称			
				$data['sex']=$_POST['sex'];//性别
				$data['birthday']=$_POST['year'].'-'.$_POST['month'].'-'.$_POST['day'];//生日
				$data['profession']=$_POST['profession'];//职业
				$data['province']=$_POST['province'];//省
				$data['city']=$_POST['city'];//市			
				$data['create_time']=time();//创建时间
				$data['user_id']=getUId();//后台用户id				
				if(!$member->create($data)){
					$this->error($member->getError());
				}else{
					$result=$member->add($data);
					if($result){
						$this->success('添加成功');
					}
				}
			}			
		}else{
        	$this->display();
		}
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
	
	//查看订单
	function order_view(){
		$orderDB=D('AsmsOrder');
		$where['ddbh']= I('id');
		$list=$orderDB->where($where)->find();	
				
		//航班信息
		$hd_info=json_decode($list['hd_info']);
		foreach($hd_info as $k=>$v){			
			$hdinfo[]=array(
				'hc'  =>$v->hd_cityname.'->'.$v->hd_ddcityname,
				'cfsj'=>$v->cfsj,
				'hbh' =>$v->hbh,
				'cw'  =>$v->cw,
				'jx'  =>$v->hd_fjjx
			);
		}

		//乘客信息
		$cjr_info=json_decode($list['cjr_info']);		
		$cjr_info=get_object_vars($cjr_info);
		foreach($cjr_info as $k=>$v){
			if($v->cjr_cjrlx == 1){$v->lx = '成人';$men++;$this->assign('men',$men);}
			if($v->cjr_cjrlx == 2){$v->lx = '儿童';$chl++;$this->assign('chl',$chl);}
			if($v->cjr_cjrlx == 3){$v->lx = '婴儿';$baby++;$this->assign('baby',$baby);}
		}
		
		//支付状态
		if($list['zf_fkf'] == 0){			
			$list['zf_status']="[待支付]";
		}elseif($list['zf_fkf'] == 1){
			$list['zf_status']="[已付款]";
		}elseif($list['zf_fkf'] == 2){
			$list['zf_status']="[已取消]";
		}
		
		$this->assign('hdinfo',$hdinfo);
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
		$this->display();
	}
	
	function order_add(){
		$this->ddbh=time().getUid().rand(1000,9999);
		$this->hyid=I('id');
		
		if($_POST){	
			if($_POST['xsj'] == ""){				
				$this->error('票面总价不能为空,请输入数字');
			}			
			if($_POST['sf'] == ""){				
				$this->error('总税费不能为空,请输入数字，如无请填0');
			}	
			if($_POST['taxa']==""){				
				$this->error('总保险费不能为空,请输入数字，如无请填0');
			}				
			if($_POST['jsf'] == ""){				
				$this->error('总机建费不能为空,请输入数字，如无请填0');
			}
			if($_POST['ysje'] == ""){				
				$this->error('应付金额不能为空,请输入数字，如无请填0');
			}			
			if($_POST['athud']== ""){				
				$this->error('成人数量不能为空,请输入数字，如无请填0');
			}
			if($_POST['chilren']== ""){				
				$this->error('儿童数量不能为空,请输入数字，如无请填0');
			}			
			if($_POST['baby']== ""){				
				$this->error('婴儿数量不能为空,请输入数字，如无请填0');
			}
			if($_POST['nklxr']==""){				
				$this->error('联系人姓名不能为空');
			}				
			if($_POST['lxdh']==""){				
				$this->error('联系人手机号不能为空');
			}
			
			$orderDB=D('AsmsOrder');
			//航程信息
			if($_POST['t'] == 1){//单程
				if(empty($_POST['hbh1'])){				
					$this->error('航班号不能为空');
				}				
				if(empty($_POST['cw1'])){				
					$this->error('舱位不能为空');
				}				
				if(empty($_POST['fjjx1'])){				
					$this->error('机型不能为空');
				}	
				if(empty($_POST['from1'])){				
					$this->error('出发城市不能为空');
				}					
				if(empty($_POST['to1'])){				
					$this->error('到达城市不能为空');
				}	
				if(empty($_POST['date1'])){				
					$this->error('出发日期不能为空');
				}	
				if(empty($_POST['time1'])){				
					$this->error('出发时间不能为空');
				}					
				$from_wz1=stripos($_POST['from1'],'(');
				$cityname1=substr($_POST['from1'],0,$from_wz1);//出发城市名-中文
				$hd_cfcity1=substr($_POST['from1'],-4,-1);//出发城市名-三字码
				
				$to_wz1=stripos($_POST['to1'],'(');
				$ddcityname1=substr($_POST['to1'],0,$to_wz1);//中文
				$hd_ddcity1=substr($_POST['to1'],-4,-1);	//三字码						   
											  
				$hb=array();
				$hb[]=array(
					'hbh'       =>$_POST['hbh1'],
					'cw'        =>$_POST['cw1'],				
					'hd_cfsj'   =>$_POST['date1'],
					'hd_cfsj_p' =>$_POST['time1'],
					'cfsj'      =>$_POST['date1'].''.$_POST['time1'],
					'hc_ddsj'   =>$_POST['ddsj1'],
					'hc_ddsj_p' =>$_POST['ddsj_p1'],
					'ddsj'      =>$_POST['ddsj1'].''.$_POST['ddsj_p1'],
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity1.$hd_ddcity1.''.$cityname1.'-'.$ddcityname1,
					'hd_cfcity' =>$hd_cfcity1,
					'hd_ddcity' =>$hd_ddcity1,
					'hd_cityname'=>$cityname1,					
					'hd_ddcityname'=>$ddcityname1					
				);	
				$hb_info=json_encode($hb);
				$data['lx']=1;//类型-单程
				$data['hc']=$hd_cfcity1.$hd_ddcity1;//航程
				$data['hbh']=$_POST['hbh1'];//航班号
				$data['qfsj']=$_POST['date1'].''.$_POST['time1'];//起飞时间
				$data['cw']=$_POST['cw1'];//舱位
			}
						
			if($_POST['t'] == 2){//往返
				if(empty($_POST['hbh2']) || empty($_POST['hbh3'])){				
					$this->error('航班号不能为空');
				}				
				if(empty($_POST['cw2']) || empty($_POST['cw3'])){				
					$this->error('舱位不能为空');
				}				
				if(empty($_POST['fjjx2']) || empty($_POST['fjjx3'])){				
					$this->error('机型不能为空');
				}	
				if(empty($_POST['from2']) || empty($_POST['from3'])){				
					$this->error('出发城市不能为空');
				}					
				if(empty($_POST['to2']) || empty($_POST['to3'])){				
					$this->error('到达城市不能为空');
				}	
				if(empty($_POST['date2']) || empty($_POST['date3'])){				
					$this->error('出发日期不能为空');
				}	
				if(empty($_POST['time2']) || empty($_POST['time3'])){				
					$this->error('出发时间不能为空');
				}			
				$hb=array();
				//去程
				$from_wz2=stripos($_POST['from2'],'(');
				$cityname2=substr($_POST['from2'],0,$from_wz2);//出发城市名-中文
				$hd_cfcity2=substr($_POST['from2'],-4,-1);//出发城市名-三字码
				
				$to_wz2=stripos($_POST['to2'],'(');
				$ddcityname2=substr($_POST['to2'],0,$to_wz2);//中文
				$hd_ddcity2=substr($_POST['to2'],-4,-1);	//三字码	
				
				$hb[]=array(
					'hbh'       =>$_POST['hbh2'],
					'cw'        =>$_POST['cw2'],				
					'hd_cfsj'   =>$_POST['date2'],
					'hd_cfsj_p' =>$_POST['time2'],
					'cfsj'      =>$_POST['date2'].''.$_POST['time2'],
					'hc_ddsj'   =>$_POST['ddsj2'],
					'hc_ddsj_p' =>$_POST['ddsj_p2'],
					'ddsj'      =>$_POST['ddsj2'].''.$_POST['ddsj_p2'],					
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity2.$hd_ddcity2.''.$cityname2.'-'.$ddcityname2,
					'hd_cfcity' =>$hd_cfcity2,
					'hd_ddcity' =>$hd_ddcity2,
					'hd_cityname'=>$cityname2,					
					'hd_ddcityname'=>$ddcityname2	
				);
				
				//返程
				$from_wz3=stripos($_POST['from3'],'(');
				$cityname3=substr($_POST['from3'],0,$from_wz3);//出发城市名-中文
				$hd_cfcity3=substr($_POST['from3'],-4,-1);//出发城市名-三字码
				
				$to_wz3=stripos($_POST['to3'],'(');
				$ddcityname3=substr($_POST['to3'],0,$to_wz3);//中文
				$hd_ddcity3=substr($_POST['to3'],-4,-1);	//三字码					
				$hb[]=array(
					'hbh'       =>$_POST['hbh3'],
					'cw'        =>$_POST['cw3'],				
					'hd_bzbz'   =>$_POST['date3'],
					'hd_bzbz_p' =>$_POST['time3'],
					'ddsj'      =>$_POST['date3'].''.$_POST['time3'],
					'hc_ddsj'   =>$_POST['ddsj3'],
					'hc_ddsj_p' =>$_POST['ddsj_p3'],
					'ddsj'      =>$_POST['ddsj3'].''.$_POST['ddsj_p3'],					
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity3.$hd_ddcity3.''.$cityname3.'-'.$ddcityname3,
					'hd_cfcity' =>$hd_cfcity3,
					'hd_ddcity' =>$hd_ddcity3,
					'hd_cityname'=>$cityname3,					
					'hd_ddcityname'=>$ddcityname3
				);
				$hb_info=json_encode($hb);
				$data['lx']=2; //类型-往返
				$data['hc']=$hd_cfcity2.$hd_ddcity2.$hd_cfcity3.$hd_ddcity3;//航程
				$data['hbh']=$_POST['hbh2'].'-'.$_POST['hbh3'];//航班号
				$data['qfsj']=$_POST['date2'].''.$_POST['time2'];//起飞时间
				$data['cw']=$_POST['cw2'];//舱位
			}
			
			if($_POST['t'] == 3){//多程			
				$hb=array();
				$hcdata=$_POST['hcdata'];
				foreach($hcdata['hbh'] as $k=>$v){
					if(empty($hcdata['hbh'][$k])){				
						$this->error('航班号不能为空');
					}				
					if(empty($hcdata['cw'][$k])){				
						$this->error('舱位不能为空');
					}				
					if(empty($hcdata['fjjx'][$k])){				
						$this->error('机型不能为空');
					}	
					if(empty($hcdata['from'][$k])){				
						$this->error('出发城市不能为空');
					}					
					if(empty($hcdata['to'][$k])){				
						$this->error('到达城市不能为空');
					}	
					if(empty($hcdata['date'][$k])){				
						$this->error('出发日期不能为空');
					}	
					if(empty($hcdata['time'][$k])){				
						$this->error('出发时间不能为空');
					}					
					
					$from_wz=stripos($hcdata['from'][$k],'(');
					$cityname=substr($hcdata['from'][$k],0,$from_wz);//出发城市名-中文
					$hd_cfcity=substr($hcdata['from'][$k],-4,-1);//出发城市名-三字码
					
					$to_wz=stripos($hcdata['to'][$k],'(');
					$ddcityname=substr($hcdata['to'][$k],0,$to_wz);//中文
					$hd_ddcity=substr($hcdata['to'][$k],-4,-1);	//三字码
					
					$hb[]=array(
						'hbh'       =>$hcdata['hbh'][$k],
						'cw'        =>$hcdata['cw'][$k],
						'hd_cfsj'   =>$hcdata['date'][$k],
						'hd_cfsj_p' =>$hcdata['time'][$k],
						'cfsj'      =>$hcdata['date'][$k].''.$hcdata['time'][$k],
						'hc_ddsj'   =>$hcdata['ddsj'][$k],
						'hc_ddsj_p' =>$hcdata['ddsj_p'][$k],
						'ddsj'      =>$hcdata['ddsj'][$k].''.$hcdata['ddsj_p'][$k],
						'hd_fjjx'   =>$hcdata['fjjx'][$k],
						'hc'        =>$hd_cfcity.$hd_ddcity.''.$cityname.'-'.$ddcityname,
						'hd_cfcity' =>$hd_cfcity,
						'hd_ddcity' =>$hd_ddcity,					
						'hd_cityname'=>$cityname,
						'hd_ddcityname'=>$ddcityname
					);
					$data['hc'].=$hd_cfcity.$hd_ddcity;//航程
					$data['hbh'].=$hcdata['hbh'][$k].'-';//航班号	
				}
				$hb_info=json_encode($hb);
				$data['lx']=3;//类型-多程	
				$data['qfsj']=$_POST['date3'].''.$_POST['time4'];//起飞时间
				$data['cw']=$hcdata['cw'][4];//舱位				
			}

			//乘机人信息
			$i=0;
			foreach($_POST['info']['cjr_cjrxm'] as $k=>$v){
				if($_POST['info']['cjr_cjrxm'][$k]==""){				
					$this->error('乘机人姓名不能为空');
				}			
				if($_POST['info']['cjr_clkid'][$k]==""){				
					$this->error('证件号码不能为空');
				}
				if($_POST['info']['cjr_zjlx'][$k]==""){				
					$this->error('证件类型不能为空');
				}	
				if($_POST['info']['cjr_xsj'][$k]== ""){				
					$this->error('票价不能为空,请输入数字');
				}	
				$i++;
				$info[$i]=array(
					'cjr_cjrxm'	=>$_POST['info']['cjr_cjrxm'][$k],//姓名
					'cjr_lx'	=>$_POST['info']['cjr_lx'][$k],//乘客类型
					'cjr_zjlx'	=>$_POST['info']['cjr_zjlx'][$k],//证件类型
					'cjr_clkid'	=>$_POST['info']['cjr_clkid'][$k],//证件号
					'cjr_xsj'	=>$_POST['info']['cjr_xsj'][$k],//票价
					'cjr_jsf'	=>$_POST['info']['cjr_jsf'][$k],//机建
					'cjr_tax'	=>$_POST['info']['cjr_tax'][$k]//税费
				);
			}
			$obj=json_encode($info);
			
			//写入数据库			
			$data['ddbh']=$this->ddbh;//订单号
			$data['hyid']=$_POST['hyid'];//会员号
			$data['user_id']=getUid();
			$data['xsj']=$_POST['xsj'];//票面价格
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['jj']=$_POST['jsf'];//机建费
			$data['xjj']=0;//现金券
			$data['ysje']=$_POST['ysje'];//应付金额			
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//联系人手机
			$data['email']=$_POST['email'];//联系人邮箱					
			$data['rs']=$_POST['athud']+$_POST['chilren']+$_POST['baby'];//人数
			$data['xm']=$_POST['nklxr'];//姓名
			$data['zf_fkf']=$_POST['zf_fkf'];//支付状态
			$data['ddzt']=0;
			$data['xj']=$_POST['xsj']+$_POST['sf']+$_POST['taxa']+$_POST['jsf'];//小计			
			$data['order_logo']=1;	
			
			$data['cpsj']=substr($_POST['cpsj'],5).''.$_POST['cpsj_p'];//出票时间
			$data['dprq']=date('m-d H:i',time());//订票日期
			$data['update_time']=time();//更新时间
			$data['info_update_time']=time();//详情更新时间
			
			$data['hd_info']=$hb_info;//航程信息
			$data['cjr_info']=$obj;//乘机人信息	
			
			$res=$orderDB->add($data);
			if($res){
				$this->success('添加成功');
			}
		}else{
			 $this->display();	
		}
	}
}
?>