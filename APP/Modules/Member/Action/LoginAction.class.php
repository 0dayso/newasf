<?php
// 首页控制器
class LoginAction extends IniAction {

    function index(){
        if($_POST['act']=='login'){
            $member=D('AsmsMember');
            $rs=$member->login(I('post.name'),I('post.password'));
            if($rs===true){
                if($_POST['check']) $this->updateCookie(); // 用户信息写入Cookie
                $url=isset($_GET['u'])?$_GET['u']:U("Member/index");
                $url=urldecode($url);
                redirect($url);
            }else{
                $this->error($member->getError());
            }
        }else{
            $this->display();
        }

    }
	
	
}