<?php
class IniAction extends PublicAction{
	Public $userinfo;
	Public $uid=1;
	
    public function _initialize(){
		if(!session('uid')){
			if(cookie('uid') && cookie('salt')){			
				$userinfo=D('Member')->UserInfo(cookie('uid'));				
				if($userinfo){
					if(md5($userinfo['salt'])==cookie('salt')){	
						session('uid',cookie('uid'));
						$this->userinfo=$userinfo;
					}else{
                        cookie('uid',null);  cookie('salt',null);
                    }
				}
			}
		}else{
			$this->userinfo=D('Member')->UserInfo(session('uid'));
            cookie('uid',session('uid'));
		}

		$this->assign('userinfo',$this->userinfo);//用户信息输出到模板

	}

    function aaa(){
      //  return $this->userinfo;
    }

}	
?>