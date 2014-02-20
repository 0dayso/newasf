<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * To change this template use File | Settings | File Templates.
 */

class MemberAction extends IniAction{
    private $uid;
    //会员中心
    function index(){
        if($_GET['test']){
            $data['name']=D('Member')->username;
            D('Message')->message_action('reg_success',$data);//发送信息
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
            $this->sysMessageList=D('Message')->sysMessageList();

			$this->display();
		}else{
			$this->redirect('/member/login');
		}
		
    }

	//注册
	function register(){
		//邀请注册人id保存到cookie 
		if(I('referee_id')) cookie('referee_id',I('referee_id'),3600*24*7);		
		//提交数据处理
		if($_POST['act']=='register'){
			$rs=D('Member')->register();
            if($rs===true){
                $data['name']=D('Member')->username;
                D('Message')->message_action('reg_success',$data);//发送信息
               $this->redirect('/index');
			}else{
				$this->error($rs,U('/member/register'));
			}
		}else{
            $this->title="会员注册";
			$this->display();		
		}
	}

    //注册后为会员设置客服
    function set_kf(){
        if(getUid()){
            $uid=D('Member')->where(array('id'=>getUid()))->getField('user_id');
            if($uid){ //如果已设置了客服 返回false
                  return false;
            }
            $rs=D('User')->assignUserid();
            if($rs){
               $this->success('完成',U('/index'));
            }
        }
    }

	//登陆
	function login(){
		if($_POST['act']=='login'){
			$member=D('Member');
            $rs=$member->login();
            if($rs===true){
                if($_POST['check']) $this->updateCookie(); // 用户信息写入Cookie
                $url=isset($_GET['u'])?$_GET['u']:U("Member/index");
                $url=urldecode($url);
                redirect($url);
            }else{
                $this->error($rs);
            }
		}else{
			$this->display();
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
        cookie("uid",null,array('domain'=>''));
		cookie('salt',null);
	//	$this->redirect('/member/login');
		$this->success("成功退出",U('/member/login'));
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

    //我的积分
    function points(){
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
        $this->points_sy=($this->points_hj)-($this->points_xf);

        $count      = $points->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出

        $this->mypoints=$points->where($where)->order('create_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->title="我的积分";
        $this->display();
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

    //修改密码
    function editPwd(){
        if($_POST){
            $rs=D('Member')->editPwd();
            if($rs===true){
                D('Message')->message_action('edit_pwd');//发送信息
                $this->success("修改成功");
            }else{
                $this->error($rs);
            }
        }else{
             $this->title="修改密码";
             $this->display();
        }
    }

    //手机找回密码
    function getPassword(){
        $this->title="密码找回";
        if($_POST){
            if($_POST['act']=='step1'){
                $username=I('username');
                $mobile= I('mobile');
                if(I('verify_code','','md5') != session('verify')){
                    $this->error('验证码错误！');
                }
                if(!$username || !$mobile){
                    $this->error('请输入用户名和手机号码');
                }
                $member= D('Member');
                $rs= $member->where("username='$username' and mobile='$mobile'")->count();
                if($rs){
                   $this->assign('mobile',$mobile);
                    import('ORG.Util.String');
                    $auth_str=String::randString(6,1);  //生成6位数的认证码
                    session('auth_username',$username);
                    session('auth_mobile', $mobile);
                    if(!session('auth_str'))  session('auth_str', $auth_str);
                   $this->display("member/getPassword1");
                }else{
                    $this->error('输入的用户名或手机号码有误');
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

    //我的订单
    function booking(){
        $booking=D("BookingView");
        if(I("details")){
            $where['id']=I("details");
            $this->info=$booking->bookingInfo($where);
          //  print_r($this->info);//travelerinfo
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

        $count      = $booking->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->list=$booking->bookingList($where,"$Page->firstRow,$Page->listRows");
        $this->title="我的订单";
        $this->display();
    }

    //订单支付列表
    function onlinePay(){
        $booking=D("BookingView");
        $where["order_status"]=1;
        $where["member_id"]=getUid();
        import('ORG.Util.Page');// 导入分页类
        $count      = $booking->where($where)->count();// 查询满足要求的总记录数
        $Page       = new Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出

        $this->list=$booking->bookingList($where,"$Page->firstRow,$Page->listRows");
       // print_r($this->list);
        $this->title="在线支付";
        $this->display();
    }

    //订单支付
    function orderPay(){
        if(IS_POST){
            $booking=D("BookingView");
            $id=implode(",",I("id"));
            $where['id']=array("in",$id);
            $this->list=$booking->orderList(I("id"));

            $this->total_price=$booking->total_price .'.' .'00';
            foreach($this->list as $v){
                $order_id_arr[]= $v['order_id'];
            }

            $this->order_id_arr=implode(',',$order_id_arr);
            $randNum = rand(0000, 9999);  //4位随机数
            $this->out_trade_no = date("YmdHis") . $randNum;//订单号，此处用时间加随机数生成
        $this->display();
        }
    }
    function transRecode(){
        $this->display();
    }

    //收货地地址管理
    function address(){
        if($_GET['act']=="add"){
            if(IS_POST){
                if(D("DeliverAddress")->addressAdd()){
                    $url=isset($_GET['u'])?$_GET['u']:U("address");
                    $this->success("成功",$url);
                }else
                   $this->error("失败");
            }else
                     $this->display("confirmAdd");
        }elseif($_GET['act']=="edit"){
            if(IS_POST){
                if(D("DeliverAddress")->addressEdit())
                    $this->success("成功",U('Common/close'));
                else
                    $this->error("失败");
            }else{
            $where["member_id"]=getUid();
            $this->info=D("DeliverAddress")->where($where)->find(I("id"));
            if( !$this->info)
                $this->error("操作 有误");
            $this->title="地址管理";
            $this->display("Member/lipin/address_iframe");
            }
        }elseif($_GET['act']=="del"){
            if(I("id")){
                $where["id"]=I("id");
                $where["member_id"]=getUid();
                if(D("DeliverAddress")->where($where)->delete())
                    $this->success("成功",U("address"));
                else
                    $this->error("失败");
            }
        }else{
          $this->list=D("DeliverAddress")->addressList();
            if(IS_AJAX){
                echo $this->ajaxReturn($this->list);
                exit;
            }
        $this->title="地址管理";
        $this->display();
        }
    }
    //礼品
     function liPin(){
        $act=$_GET['_URL_'][2];
        switch($act){
            case 'cart'://购物车
                $cart=D("MallCart");
                if($_GET['act']=='add'){
                    if(isset($_GET['num']) && I('num')<1){
                        $this->error("添加失败,商品数量不能小于1");
                    }
                    $cart->addCart()?$this->success("添加成功",U('/member/lipin/cart'),0):$this->error("添加失败");
                }elseif($_GET['act']=="del"){
                    $cart->delCart()?$this->success("成功删除",'',0):$this->error("删除失败");
                }
                $where['member_id']=getUid();
                $this->list=$cart->relation(true)->where($where)->group('mall_id')->select();
                $total=0;
                foreach($this->list as $v){
                    $total+=$v['jifen']*$v['num'];
                }

                $this->total=$total;
                $this->title="我的购物车";
                $this->display("Member/lipin/cart");
                break;

            case 'exchange':
                $this->title="已兑换的礼品";
                $where['member_id']=getUid();
                $rs=D('MallExchange')->where($where)->select();
                foreach($rs as $val){
                    $arr=json_decode($val['info']);
                    foreach($arr as  $k=>$v){
                        $list[][$k]=(array)$v;
                    }
                }
             //   print_r($list);
                $this->list=$list;
                $this->display("Member/lipin/exchange");
                break;

            case 'confirm';
                if(IS_AJAX){
                    $member=M("member");
                    $uid=getUid();
                    $points=$member->where("id=$uid")->getField('points');
                    $array=array();
                    $arr=session('mall_cart_confirm');
                    foreach($arr as $val){
                        $cart_id[]=$val['id'];
                        $array['total']+=$val['jifen'];
                    }
                    if($array['total']>$points){
                        $this->error("您的积分不足，不能完成本次兑换");
                    }
                    $data['info']=json_encode($arr);
                    $data['member_id']=getUid();
                    $data['create_time']=time();
                    $data['address']=json_encode(D("DeliverAddress")->find(I('address')));
                    $rs=D('Mall_exchange')->add($data);
                    if($rs){
                        D("MallCart")->delCart($cart_id);
                        D('Points')->cutPoints(getUid(),$array['total'],"兑换礼品 ");
                        session('mall_cart_confirm','');
                        $this->success("兑换成功",U('Member/liPin/exchange'));
                    }else{
                        $this->error("兑换失败");
                    }

                }
                if(!D("DeliverAddress")->addressList()){
                    $u=isset($_GET["u"])?$_GET["u"]:get_cur_url(1);
                   redirect(U('/member/address','act=add')."/?u=$u");
                }
                $this->list=D("MallCart")->confirm();
                $this->address_list=D("DeliverAddress")->addressList();

                $this->title="礼品兑换确认";
                $this->display("Member/lipin/confirm");
                break;
            default:
                $collect=D('MallCollect');
                if($_GET['act']=='add'){
                    if($_GET['del'])
                        D("MallCart")->delCart($_GET['del']);
                    $collect->addCollect()?$this->success("添加成功",U('/member/lipin/collect')):$this->error("添加失败");
                }elseif($_GET['act']=='del'){
                    $collect->delCollect()?$this->success("成功删除",'',0):$this->error("删除失败");
                }
                $where['member_id']=getUid();
                $this->list=$collect->relation(true)->where($where)->select();
                $this->title="我的收藏";
                $this->display("Member/lipin/collect");
        }
     }

    //消息
    function message(){
        if($_GET['act']='read' && I('id')){
            $data['to_id']=getUid();
            $data['id']=I('id');
            $data['is_read']=1;
            $rs=D('Message')->save($data);
            if($rs)
                $this->success($rs);
            else
                $this->error($rs);;
        }else{
            $this->list=D('Message')->Messagelist();
        }
    }
}