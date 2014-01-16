<?php

class ComplaintAction extends IniAction{
    function index(){
        if($_POST){
            $rs=D('Complaint')->addNews();
            if($rs===true){  //成功
                $type=I('type')==1?'投诉':'建议';
                $mailsubject = "来自".C('WEBNAME')."的  $type- ".I('title');//邮件主题
                $mailbody = $type."内容 <br/>".I('contents')."<br/> <br/> 电话：".I('mobile')."  email:".I('email');//邮件内容
                $smtpemailto = C('SMTP_EMAILTO');//发送给谁
                $rs=sendMail($smtpemailto,$mailsubject,$mailbody);
                $this->success("提交成功");exit;
            }else{
                $this->error($rs);
            }

        }
        $this->title="投诉建议";
        $this->display();
    }
}
?>