<?php
class GetpasswordAction extends PublicAction{
    function step1(){
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
                   $this->display("Getpassword/step2");
                }else{
                    $this->error('输入的用户名或手机号码有误');
                }
            }
            elseif($_POST['act']=='step2' &&  session('auth_mobile')){
               if( I('post.auth_str')==session('auth_str')){
                  $this->display("Getpassword/step3");
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
                     $this->display("Getpassword/step4");
               }else{
                    $this->error('操作失败');
               }
            }
        }else{
            $this->display();
        }
    }
}