<?php
//  投诉建议求的控制器
class ComplaintAction extends Action {
    function index(){
        if($_POST){
            C('VERIFY_CODE',0);
            $rs=D('Complaint')->addNews();
            if($rs===true){  //成功
                $type=I('type')==1?'投诉':'建议';
                $mailsubject = "来自".C('WEBNAME')."的  $type- ".I('title');//邮件主题
                $mailbody = $type."内容 <br/>".I('contents')."<br/> <br/> 电话：".I('mobile')."  email:".I('email');//邮件内容
                $smtpemailto = C('SMTP_EMAILTO');//发送给谁
                $rs=sendMail($smtpemailto,$mailsubject,$mailbody);
                $this->success("提交成功",U('/'));exit;
            }else{
                $this->error($rs);
            }

        }else{
        $this->title="投诉建议";
        $this->display();
        }
    }
	
	
}