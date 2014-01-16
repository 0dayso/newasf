<?php
// 应用中心
class AppcenterAction extends IniAction {

    //好友fun享
	function fun(){
		$this->title="好友fun享";
        //分享链接
        $this->funUrl=U('/Member/Invite/reg','','','',true)."?id=".$this->userInfo['id'];
        //发送邮件
        if(I('act')=="sendMail" && I('mail')){
            if(C('VERIFY_CODE') && I('verify_code','','md5') != session('verify'))
                $this->error('验证码错误！');
            $content="我注册了爱尚飞国际机票网，感觉很专业，服务很好，价格也很实惠，强烈推荐你注册啊！$this->funUrl";
            $title=$this->userInfo['name']."邀请您注册爱尚飞国际机票网";
            if(sendMail(I('mail'),$title,$content)){
                $this->success('发送成功');
            }
            $this->error('发送失败');

        }

        //推荐商品
        $list=D('Mall')->where('type=1')->order('update_time desc')->limit('12')->select();
        $this->fun_list=$list;
		$this->display();
    }
	
}