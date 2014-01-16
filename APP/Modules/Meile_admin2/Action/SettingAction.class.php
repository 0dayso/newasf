<?php

class SettingAction extends IniAction {
    public function  index(){
        $this->display();
    }

    function emailConfig(){
        $this->display();
    }

    function smsConfig(){
        $this->display();
    }

    function safeConfig(){

    }


    function testEmailConfig(){
        if(IS_AJAX){
            $return = sendMail($_POST['SMTP_TEST'],"title:测试配置是否正确", "content:这是一封测试邮件，如果收到了说明配置没有问题");
            if ($return == 1) {
                $this->success("测试邮件已经发往你的邮箱" . $_POST['SMTP_TEST'] . "中，请注意查收");
            } else {
                $this->error($return);
            }
        }
    }

    function testSmsConfig(){
        if(IS_AJAX){
            $return = sendMobileSms($_POST['SMS_TEST'], "content:这是一封测信息，如果收到了说明配置没有问题");
            if ($return == 1) {
                $this->success("测试短信已经发往你的手机" . $_POST['SMS_TEST'] . "中，请注意查收");
            } else {
                $this->error($return);
            }
        }
    }

    function updateConfig(){
        if(IS_AJAX){
            $rs= update_config(WEB_ROOT.APP_NAME."/Conf/web_config.php",$_POST);
            if($rs)
                $this->success("更新成功");
        }
    }

}