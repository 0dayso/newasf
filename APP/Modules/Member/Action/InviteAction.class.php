<?php
// 首页控制器
class InviteAction extends IniAction {

    function index(){
        $this->title="邀请好友注册";
       // http://www.52meifan.com/invite/
        if(I("id")){
            $memberDB=D("Member");
            $arr=$memberDB->UserInfo(I("id"));
            if(empty($arr)){
                $this->error('你访问的链接已失效');
            }
        }
    //    $this->display();

    }

    function reg(){
        $this->title="邀请注册";
        if(I("id")){
            $memberDB=D("Member");
            $arr=$memberDB->find(I("id"));
            if(empty($arr)){
                $this->error('你访问的链接已失效');
            }
            if($arr['asms_member_id']){
                //同步asms 订单
                $orderDB=D("AsmsOrder");
                $orderDB->orderFind($arr['asms_member_id']); //查找出我的订单
            }
        }
        //邀请注册人id保存到cookie
        if(I('id')) cookie('referee_id',I('id'),3600*24*7);
        redirect(U('/Member/register'));
    }
    
	
	
}