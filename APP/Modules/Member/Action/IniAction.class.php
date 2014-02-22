<?php
/*
 * 会员中心初始化控制器
 */
class IniAction extends PublicAction{
	protected  $userInfo; //会员信息
    public function _initialize(){
        $member=D('Member');
		if(!session('uid')){ //cookie 登陆
			if(cookie('uid') && cookie('salt')){
                $userInfo=$member->UserInfo(cookie('uid'));
				if($userInfo){
					if(md5($userInfo['salt'])==cookie('salt')){
						session('uid',cookie('uid'));
						$this->userInfo=$userInfo;
                        define('ASMSUID',$userInfo['asms']['hyid']);//加入asms id
                        D('Member')->updateLogin();//更新登陆
					}else{
                        cookie('uid',null);  cookie('salt',null);
                    }
				}else{
                    cookie('uid',null);  cookie('salt',null);
                }
			}
		}elseif(session('uid')){
            $userInfo=$member->UserInfo(session('uid'));
            if(!$userInfo) session('uid',null);
            $this->userInfo=$userInfo;
            $asmsUid=$userInfo['asms']['hyid']?$userInfo['asms']['hyid']:'-1';
            define('ASMSUID',$asmsUid);//加入asms id
            cookie('uid',session('uid'));
		}
        //登陆后检测是否关联胜意 没有则注册到asms
        if(getUid() && !$userInfo['asms_member_id']){
            D('AsmsMember')->relationReg($userInfo);
        }

       $this->restrict();//未登录不能访问

        //当没有对应客服,跳到客服选择页
        if($this->userInfo && !$this->userInfo['user_id'] && MODULE_NAME!='Index'){
            if(MODULE_NAME!='Adviser' && ACTION_NAME!='set_kf'){
                D('User')->assignUserid();
            }
        }
        $this->widget();
     //   print_r($this->userInfo);
		$this->assign('userInfo',$this->userInfo);//用户信息输出到模板
        $this->assign('userinfo',$this->userInfo);//用户信息输出到模板
	}

    //未登录不能访问
	function restrict(){
		$array=array('register','login','invite','pay');//不需要登陆可访问的
			$module_name=strtolower(MODULE_NAME);
            if(!in_array($module_name,$array)){
				if(!session('uid')){
                    $u=isset($_GET["u"])?$_GET["u"]:get_cur_url(1);
				//	$this->redirect('member/login',"u='$cururl'");
                    redirect(U('/Member/login')."/?u=$u");
					exit;
				}
			}else{
				if(session('uid')){
					$this->success("你已经登陆",U('/Member/index'));
					exit;
				}
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

    //测边栏
    function widget(){
        if(getUid()){
            //我有订单
            $orderDB=D("AsmsOrder");
            $orderDB->orderFind(ASMSUID,I("update")); //查找出我的订单
            $where['hyid']=ASMSUID;

            $where['ddzt']=array('in',array('7','8'));
            $common['cancel_count']=$orderDB->where($where)->count();
            $pagesize=10;//
            $where['ddzt']=array('not in',array('7','8'));
            $where['zf_fkf']=0;//未支付
            $common['pending_count']=$orderDB->where($where)->count();
            $where['zf_fkf']=1; //已支付
            $common['process_count']=$orderDB->where($where)->limit($pagesize)->count();

            //站内信
            $messageDB=D('Message');          
            $wh['to_id']=getUid();			
			$wh['id_read']=0;//0表示未读
            $common['message_count']=$messageDB->where($wh)->count();
            $this->common=$common;//公共数据
        }
    }



}	
?>