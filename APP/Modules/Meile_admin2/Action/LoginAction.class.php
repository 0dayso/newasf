<?php
 class   LoginAction extends Action{
    function index(){
        if(IS_AJAX){
            if(I('verify_code','','md5') != session('verify')){
                $this->error('验证码错误!');
            }
            $username=I('post.name');
            $password=I('post.password');
            if(!$username || !$password){
                $this->error('请填写帐号 密码再提交！');
            }
            if($username == 'admin' || $username=='sheyun'){

            }else{
                $this->error('用户名不正确');
            }
            $user=D('User');
            $salt=$user->getsalt($username);
            if($salt===null){
                $this->error('用户名不正确');
            }

            $password=hashPassword($password,$salt);
            $userinfo=$user->where("username='$username' and password='$password'")->find();
            if(!empty($userinfo)){
                $this->name=$userinfo['name'];
                $this->uid=$userinfo['id'];
                $this->username=$userinfo['username'];
                session('name',$userinfo['name']);
                session('uid',$userinfo['id']);
                session('username',$userinfo['username']);
                // 更新用户信息
                $data['last_login_time']=time();
                $data['last_login_ip']=get_client_ip();
                $data['login_count'] = array('exp','login_count+1');// 用户的d登陆次数加1
                $where['id']=$this->uid;
                $user->where($where)->save($data);
                $this->success("登陆成功",U('index/index'));
               // $this->redirect('/member/index');
            }else{
                $this->error('密码不正确');
            }
        }else
         $this->display('index/login');
    }

}