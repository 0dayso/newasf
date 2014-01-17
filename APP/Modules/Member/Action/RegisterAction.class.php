<?php
// 首页控制器
class RegisterAction extends IniAction {
    //注册
    function index(){
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



	
	
}