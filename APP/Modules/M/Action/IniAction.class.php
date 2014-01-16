<?php
class IniAction extends PublicAction{
	private $userinfo;
	
    public function _initialize(){
        $member=D('Member');
		if(!session('uid')){ //cookie 登陆
			if(cookie('uid') && cookie('salt')){			
				$userinfo=$member->UserInfo(cookie('uid'));
				if($userinfo){
					if(md5($userinfo['salt'])==cookie('salt')){
						session('uid',cookie('uid'));
						$this->userinfo=$userinfo;
                        D('Member')->updateLogin();//更新登陆
					}else{
                        cookie('uid',null);  cookie('salt',null);
                    }
				}
			}
		}elseif(session('uid')){
            $this->userinfo=$member->UserInfo(session('uid'));
            cookie('uid',session('uid'));
		}

        $this->restrict();//未登录不能访问

        //当没有对应客服,跳到客服选择页
        if($this->userinfo && !$this->userinfo['user_id'] && MODULE_NAME!='Index'){
            if(MODULE_NAME!='Adviser' && ACTION_NAME!='set_kf'){
                D('User')->assignUserid();
            }
        }
    //    dump($this->userinfo);
		$this->assign('userinfo',$this->userinfo);//用户信息输出到模板

	}

    //未登录不能访问
	function restrict(){
		$array=array('register','login','phoneverify','getpassword','getpassword1','getpassword2','getpassword3');//不需要登陆可访问的function
		if(MODULE_NAME == "Member"){
			if(!in_array(ACTION_NAME,$array)){
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
	}



}	
?>