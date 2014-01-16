<?php
// 会员中心控制器
class MemberAction extends IniAction {

    function index(){
        if($_GET['test']){
            $this->error(123);
        }
        if(session('uid')){
            $this->title="会员中心";
            $where['member_id']=getUid();
            //会员订单状况
            $order_status=D("BookingView")->field("order_status,count(order_status) count")->where($where)->group("order_status")->select();
            foreach($order_status as $k=>$v){
                $status[$v['order_status']]=$v['count'];
            }
            $this->order_count=array_sum($status);
            $this->order_status=$status;

            //客服好评数
            $server=D("Member")->pjCount();
            $this->server=$server['server'];
            $this->serverImg=round($this->server*2);
            $this->sysMessageList=D('Message')->sysMessageList();

            $this->display();
        }else{
            $this->redirect('/Member/login');
        }

    }

    //我的信息
    function information(){
        if($_POST){
            $member= D('Member');
            $data['name']=I('post.name');
            $data['sex']=I('post.sex');
            //    $data['mobile']=I('post.mobile');
            $data['province']=I('post.province');
            $data['city']=I('post.city');
            $data['address']=I('post.address');
            $data['email']=I('post.email');
            $data['zip_code']=I('post.zip_code');
            $uid=session('uid');
            $rs=$member->where("id=$uid")->save($data);
            if($rs){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->title="我的信息";
            $this->display();
        }
    }
	
	public function editpwd(){
        if($_POST){
            $rs=D('Member')->editPwd();
            if($rs===true){
                D('Message')->message_action('edit_pwd');//发送信息
                $this->success("修改成功",'/Member/index');
            }else{
                $this->error($rs);
            }
        }else{
            $this->title="修改密码";
            $this->display();
        }
    }

    function login(){
        if($_POST['act']=='login'){
            C('VERIFY_CODE',0);
            $member=D('Member');
            $rs=$member->login();
            if($rs===true){
                if($_POST['check']) $this->updateCookie(); // 用户信息写入Cookie
                $url=isset($_GET['u'])?$_GET['u']:U("/Member/index");
                $this->success('登陆成功',$url);
            }else{
                $this->error($rs);
            }
        }else{
            $this->display();
        }
    }

    // 用户信息写入Cookie
    function updateCookie(){
        if(getUid()){
            $cookie_time=3600*24*14;
            $salt=D('Member')->getsalt(getUid(),1);
            cookie('uid',getUid(),$cookie_time);
            cookie('salt',md5($salt),$cookie_time);
        }
    }

    //退出
    function out(){
        session_unset();
        session_destroy();
        cookie('uid',null);
        $host=$_SERVER['SERVER_NAME'];
        cookie("uid",null,array('domain'=>$host));
        cookie("uid",null,array('domain'=>'www.aishangfei.net'));
        cookie("uid",null,array('domain'=>'/'));
        cookie('salt',null);
        //	$this->redirect('/member/login');
        $this->success("成功退出",U('/index'));
    }
	
	public function register(){
        //邀请注册人id保存到cookie
        if(I('referee_id')) cookie('referee_id',I('referee_id'),3600*24*7);
        //提交数据处理
        if($_POST['act']=='register'){
            $rs=D('Member')->regCheckName(I('name'));
            if($rs!==true){
                $this->error($rs);
            }
            if(D('Member')->checkName(I('phone'))){
                $this->error('手机号已存在');
            }
            session('reg_name',I('name'));
            session('reg_phone',I('phone'));
            session('reg_password',I('password'));
            if(IS_AJAX) $this->success('验证手机',U('/Member/phoneverify'));
            $this->display('phoneverify');
        }else{
            $this->title="会员注册";
            $this->display();
        }
    }

	//注册第二步
	public function phoneverify(){
        if(!session('reg_phone')) return false;
        if($_POST['act']=='phoneverify'){
            if(I('verify')==session('auth_reg_str')){
                session('reg_mobile',session('auth_reg_mobile'));
            }else{
                $this->error('验证码不正确');
            }
            C('VERIFY_CODE',0);
            $_POST['username']= session('reg_name');
            $_POST['mobile']= session('reg_phone');
            $_POST['password']= session('reg_password');
            $_POST['source']='mobile';
            $rs=D('Member')->register();
            if($rs===true){
                $data['name']=D('Member')->username;
                D('Message')->message_action('reg_success',$data);//发送信息
                $rs=D('User')->assignUserid();
                $this->success('完成',U('/member/index'));
            }else{
                $this->error($rs,U('/member/register'));
            }
        }else{
            $this->reg_phone=session('reg_phone');
            $this->title="验证手机";
            $this->display();
        }
    }

    //手机找回密码
    function getPassword(){
        $this->title="密码找回";
        if($_POST){
            if($_POST['act']=='step1'){
                $mobile= I('phone');
                if(!$mobile){
                    $this->error('请输入用户名和手机号码');
                }
                $member= D('Member');
                $rs= $member->where("mobile='$mobile'")->find();
                if($rs){
                    $this->assign('mobile',$mobile);
                    import('ORG.Util.String');
                    $auth_str=String::randString(6,1);  //生成6位数的认证码
                    session('auth_username',$rs['username']);
                    session('auth_mobile', $mobile);
                    session('auth_str', $auth_str);
                    $this->display("member/getPassword1");
                }else{
                    return ;
                  //  $this->error('手机号码有误');
                }
            }
            elseif($_POST['act']=='step2' &&  session('auth_mobile')){
                if( I('post.auth_str')==session('auth_str')){
                    $this->display("/member/getPassword2");
                }else{
                    $this->error('输入的手机验证码有误');
                }
            }
            elseif($_POST['act']=='step3'){
                $member= D('Member');
                if( strlen(I('password')) < 6 || I('password')!=I('re_password')){
                    $this->error('密码长度不能少于6位数 ，或两次密码不一样');
                }
                $password=I('password');
                $salt=generateSalt();
                $data['salt']=$salt;  // 设置salt字段值
                $data['password']=hashPassword($password,$salt); //# 对密码进行md5 混合加密
                $username=session('auth_username');
                $mobile=session('auth_mobile');
                $rs=$member->where("username='$username' and mobile='$mobile'")->save($data);
                if($rs){
                    $data['mobile']=session('auth_mobile');
                    D('Message')->message_action('forgot_pwd_success',$data);//发送信息
                    session('act',null);
                    session('auth_mobile',null);
                    session('auth_username',null);
                    session('auth_str',null);
                    $this->display("/member/getPassword3");
                }else{
                    $this->error('操作失败');
                }
            }
        }else{
            $this->display();
        }
    }

	
	//订单
	public function booking(){
        $booking=D("BookingView");
        if(I("details")){
            $where['id']=I("details");
            $this->info=$booking->bookingInfo($where);
            $this->title="订单详情";
            $this->display("orderDetail");
            exit;
        }elseif(I('act')=='cancel'){
            D("Booking")->orderCancel()?$this->success("已取消"):$this->error("操作失败");
        }
        $where['member_id']=getUid();
        if(I("status")=="pending")
            $where['order_status']=array(array('eq',0),array('eq',1), 'or');
        if(I("status")=="process")
            $where['order_status']=2 ;
        if(I("status")=="cancel")
            $where['order_status']="-1";

        import('ORG.Util.Page');// 导入分页类
        $pageRows=10;
        $count      = $booking->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$pageRows);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->list=$booking->bookingList($where,"$Page->firstRow,$Page->listRows");
        $this->title="我的订单";

        if(IS_AJAX){
            $totalPages= ceil($count/$pageRows);
            if(I('p')>$totalPages && $totalPages!=0){
                $this->error('已经是最后一页了');
            }
            if($count!==false){
                $info['list']= $this->list;
                $info['title']=$this->title;
                $info['showPage']=$this->$show;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }

            $this->AjaxReturn($info);
            exit;
        }
        $this->display();
    }
	
	//积分
	public function points(){
        $points=D('Points');
        import('ORG.Util.Page');// 导入分页类
        $uid=session('uid');
        if(I('type')==1){
            $where="member_id=$uid and type=1"; //消费积分
        }else{
            $where="member_id=$uid";//积分明细
        }

        $this->points_hj=$points->where("member_id=$uid and type=0")->sum('points');
        $this->points_xf=$points->where("member_id=$uid and type=1")->sum('points');
        $this->points_sy=$this->points_hj-$this->points_xf;

        $pageRows=10;
        $count      = $points->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,$pageRows);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出

        $list=$points->where($where)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
       foreach($list as $k=>$v){
            $list[$k]['time']=date("Y-m-d",$v['create_time']);
            $list[$k]['make']=$v['type']==0?'+':'-';
       }
        $this->list=$list;
        if(IS_AJAX){
            $totalPages= ceil($count/$pageRows);
            if(I('p')>$totalPages && $totalPages!=0){
                $this->error('已经是最后一页了');
            }
            if($count!==false){
                $info['list']= $this->list;
                $info['title']=$this->title;
                $info['showPage']=$this->$show;
                $info['status']=1;
                $info['p']=I('p')?intval(I('p')):1;
            }else{
                $info['info']='获取失败';
                $info['status']=0;
            }
            $this->AjaxReturn($info);    exit;
        }
        $this->title="我的积分";
        $this->display();
    }
	public function bonuspoints(){
        $_GET['type']=1;
        $this->points();
    //    $this->display();
    }
}