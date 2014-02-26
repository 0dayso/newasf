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
			$totel_zf=$orderDB->where($where)->select();
			$info[$key]['totel_zf']=count($totel_zf);//待支付订单数

			if($info[$key]['totel_zf'] != 0){
				foreach($totel_zf as $k=>$v){
					$arrId[]=$v['ddbh'];//待支付订单ID号
				}
			}
				
		}
		
		$this->assign('arrId',$arrId);
		$this->assign('info',$info);
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
	
	//查看订单
	function order_view(){
		$orderDB=D('AsmsOrder');
		$where['ddbh']=array('in',I('id'));
		$list=$orderDB->where($where)->find();

		$list['hc']= $orderDB->format($list['hc']);
		$list['hc']=str_split($list['hc'],3);
		$list['hc_a'] = D("City")->getCity($list['hc']);
		$count=count($list['hc_a']);
		if($list['hc_a'][0] == $list['hc_a'][$count-1]){
			$list['hc_d']=2;			
			//去程
			for($i=0;$i<=$count-2;$i++){
				$hc1[]=$list['hc_a'][$i];
			}
			//返程
			$hc2[]=$list['hc_a'][$count-2];
			$hc2[]=$list['hc_a'][$count-1];
			
			$list['hc_n1']=implode('->',$hc1);
			$list['hc_n2']=implode('->',$hc2);
		}else{
			$list['hc_d']=1;
			$list['hc_n']=implode('->',$list['hc_a']);
		}
				
		//航班信息
		$hd_info=json_decode($list['hd_info']);
		$list['hd_info']=$hd_info;

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
		}else{
			$list['zf_status']="[已取消]";
		}
		
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
	    $this->display();
	}
	
	function order_add(){
		$this->ddbh=time().getUid().rand(1000,9999);
		if($_POST){	
			$orderDB=D('AsmsOrder');
			//航程信息
			if($_POST['t'] == 1){//单程
				$hb=array();
				$hb[]=array(
					'hbh'       =>$_POST['hbh1'],
					'cw'        =>$_POST['cw1'],				
					'hd_cfsj'   =>$_POST['date1'],
					'hd_cfsj_p' =>$_POST['time1'],
					'cfsj'      =>$_POST['date1'].'&nbsp;'.$_POST['time1'],
					'hc'        =>$_POST['from1'].'-'.$_POST['to1'],
					'hd_cityname'   =>$_POST['from1'],
					'hd_ddcityname' =>$_POST['to1'],
					'hd_fjjx'       =>$_POST['fjjx1']
				);	
				$hb_info=json_encode($hb);
				$data['lx']=1;
			}
			
			if($_POST['t'] == 2){//往返
				$hb=array();
				$hb=array(
					'hbh'       =>$_POST['hbh2'],
					'cw'        =>$_POST['cw2'],				
					'hd_cfsj'   =>$_POST['date2'],
					'hd_cfsj_p' =>$_POST['time2'],
					'cfsj'      =>$_POST['date2'].'&nbsp;'.$_POST['time2'],
					'hc'        =>$_POST['from2'].'-'.$_POST['to2'],
					'hd_cityname'      =>$_POST['from2'],
					'hd_ddcityname'    =>$_POST['to2'],
					'hd_fjjx'       =>$_POST['fjjx2']
				);
				$hb=array(
					'hbh'       =>$_POST['hbh3'],
					'cw'        =>$_POST['cw3'],				
					'hd_bzbz'   =>$_POST['date3'],
					'hd_bzbz_p' =>$_POST['time3'],
					'ddsj'      =>$_POST['date3'].'&nbsp;'.$_POST['time3'],
					'hc'        =>$_POST['from3'].'-'.$_POST['to3'],
					'hd_cityname'      =>$_POST['from3'],
					'hd_ddcityname'    =>$_POST['to3'],
					'hd_fjjx'       =>$_POST['fjjx3']
				);
				$hb_info=json_encode($hb);
				$data['lx']=2;
			}
			
			if($_POST['t'] == 3){//多程
				$hb=array();
				$hcdata=$_POST['hcdata'];
				foreach($hcdata['hbh'] as $k=>$v){
					$hb=array(
						'hbh'      =>$hcdata['hbh'][$k],
						'cw'       =>$hcdata['cw'][$k],
						'hd_cityname'    =>$hcdata['from'][$k],
						'hd_ddcityname'  =>$hcdata['to'][$k],					
						'hd_cfsj'  =>$hcdata['date'][$k],
						'hd_cfsj_p'=>$hcdata['time'][$k],
						'hd_fjjx'  =>$hcdata['fjjx'][$k],
						'hc'       =>$hcdata['from'][$k].'-'.$hcdata['to'][$k],
						'cfsj'     =>$hcdata['date'][$k].'&nbsp;'.$hcdata['time'][$k]
					);
				}
				$hb_info=json_encode($hb);
				$data['lx']=3;				
			}
			
			//乘机人信息
			$i=0;
			foreach($_POST['info']['cjr_cjrxm'] as $k=>$v){
				$i++;
				$info[$i]=array(
					'cjr_cjrxm'	=>$_POST['info']['cjr_cjrxm'][$k],//姓名
					'cjr_lx'	=>$_POST['info']['cjr_lx'][$k],//乘客类型
					'cjr_clkid'	=>$_POST['info']['cjr_clkid'][$k],//证件号
					'cjr_xsj'	=>$_POST['info']['cjr_xsj'][$k],//票价
					'cjr_jsf'	=>$_POST['info']['cjr_jsf'][$k],//机建
					'cjr_tax'	=>$_POST['info']['cjr_tax'][$k]//税费
				);			
			}
			$obj=json_encode($info);
			
		
			//关联asms
			$userinfo=$this->userInfo;
			$asms_common=array(
				'bxjsj'      =>'',
				'clsx'       =>'',
				'czfrom'     =>'',	
				'close'      =>'',
				'cgmk_gn'    =>'',
				'cgmk_gj'    =>'',
				'cu_ifljjf'  =>'',	
				'clyy'       =>'',
				'ddfrom'     =>'',
				'ddxgtype'   =>'',
				'gngjlx'     =>'',
				'ifxf_dd'    =>'',
				'iftb'       =>'',
				'in_zk_fklx' =>'',
				'mkbh'       =>'',
				'openlx'     =>'',
				'platid'     =>'',
				'sfsgd'      =>'',
				'sfbm'       =>'',
				'tjlx'       =>'',
				'type'       =>'',	
				'td_name'    =>'',
				'wjlpjsfjs'  =>'',
				'xjjlfs'     =>'',
				'xmdh'       =>'',
				'xsmk'       =>'',	
				'zhcs'       =>'',
				'zrs'        =>'',
				'zkfx'       =>'',				
				'ddlx'       =>$data['lx'],//订单类型
				'ddly'       =>'',
				'ddzt'       =>'',
				'ddtzbz'     =>'',
				'ddlx_dd'    =>'',
				'ddtzfs'     =>'',
				'ct_lxrkh'   =>'',//联系人
				'ct_hyxm'    =>'',//会员姓名
				'ct_hyid'    =>$_POST['hyid'],//会员id
				'ct_nxr'     =>$_POST['nklxr'],//联系人
				'ct_nxrdh'   =>$_POST['lxdh'],//联系人电话
				'ct_smsmobilno' =>'',
				'ct_xcd'     =>'',
				'ct_sjr'     =>'',
				'ct_yzbm'    =>'',
				'ct_sjdz'    =>'',
				'dp_compid'  =>$userinfo['company_id'],//订票-公司id
				'dp_deptid'  =>$userinfo['department_id'],//订票-部门id
				'dp_dpyid'   =>$userinfo['id'],//订票-订票员id
				'dp_compmc'  =>'',
				'dp_dpyid'   =>'',	
				'ds_compid_dd'=>'',
				'ds_deptid_dd'=>'',
				'ps_pszt'    =>'',
				'ps_pszt'    =>'',
				'ps_yqrqsj'  =>'',
				'ps_compid_dd'=>'',
				'ps_deptid_dd'=>'',
				'pnrno'      =>'',
				'pnr_no'     =>'',
				'pnr_zt'     =>'',	
				'pnr_lr'     =>'',
				'pnr_nr_b'   =>'',
				'pnr_hcglgj' =>'',
				'version'    =>'',
				'vipxmmc'    =>'',
				'vipusermc'  =>'',
				'vip_jsbmName'=>'',
				'vip_jsbm'   =>'',
				'vip_dp_userid'=>'',
				'vip_bmdh'   =>'',
				'vip_ddbh'   =>'',
				'ct_cpfs'    =>'',//出票方式
				'cplx'       =>'',//出票类型
				'confirmDate'=>'',//确认日期
				'confirmTime'=>'',//确认时间
				'bxlx'       =>'',//保险类型
				'qpbm'       =>'',	
				'ps_lx'      =>'',//配送方式
				'ps_city'    =>'',//配送城市
				'ps_dz'      =>'',//配送地址
				'yqdate'     =>'',//要求日期
				'sj'         =>'',//时间	
				'ps_bz'      =>'',//配送备注
				'ddbz'       =>'',//订单备注
				'cjr_index'  =>'',
				'cjr_bxxjjsj'=>'',
				'cjr_bx'     =>'',
				'cjr_pjxjjsj'=>'',
				'cjr_sjhm'   =>'',//手机号码
				'cjr_sfmp'   =>'',//是否免费
				'cjr_sfzsp'  =>'',//是否赠送票
				'cjr_cgj'    =>'',//
				'cjr_pjsjjsfl'=>'',
				'cjr_pjsjjsj'=>'',
				'cjr_jj'     =>'',//加价
				'cjr_bxfs'   =>'',//保险份数
				'cjr_bxdj'   =>$_POST['taxa'],//保险单价
				'cjr_zsbx'   =>'',//赠送保险份数
				'cjr_bxjl'   =>'',//保险奖励
				'cjr_pjxjjsfl'=>'',//
				'cjr_lwyj'   =>''
			);
			
			foreach($info as $v){//乘机人信息
				$cjr_str .= http_build_query($v)."&";
			}			
			foreach($hb as $v){//航班信息
				$hb_str .= http_build_query($v)."&";
			}
			$common_str= http_build_query($asms_common)."&";			
			$post_str=$common_str.$cjr_str.$hb_str;
			
			$url=C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_ddgl.shtml?cs=5&count=$page_r&start=$page_start&";					
			$rs=curl_post($url,$post_str,COOKIE_FILE);				
			if(!$rs){return -1;}
			if(empty($rs)){
				$this->error="连接失败";
				return false;
			}

			//写入数据库
			$data['ddbh']=$this->ddbh;//订单号
			$data['hyid']=$_POST['hyid'];//会员号
			$data['xsj']=$_POST['xsj'];//票面价格
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['jj']=$_POST['jsf'];//机建费
			$data['xjj']=0;//现金券
			$data['ysje']=$_POST['ysje'];//应付金额
			$data['cpsj']=$_POST['cpsj'];//出票时间
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//联系人手机
			$data['email']=$_POST['email'];//联系人邮箱
			$data['hbh']=$_POST['num1'];//航班号			
			$data['rs']=$_POST['athud']+$_POST['chilren']+$_POST['baby'];//人数
			$data['xm']=$_POST['nklxr'];//姓名			
			$data['hyid']=I('id');//会员id			
			$data['zf_fkf']=0;//支付状态			
			$data['dprq']=data('m-d H:i',time());//订票日期
			$data['update_time']=time();//更新时间
			$data['info_update_time']=time();//详情更新时间			
			$data['hd_info']=$hb_info;//航程信息
			$data['cjr_info']=$obj;//乘机人信息	
			
			//$res=$orderDB->add($data);
			if($res){
				$this->success('添加成功');
			}
		}else{
			 $this->display();	
		}
	}
}
?>