<?php
// 首页控制器
class IndexAction extends IniAction {

    function index(){
        if($_GET['test']){
            $data['name']=D('Member')->username;
            D('Message')->message_action('reg_success',$data);//发送信息
        }

        if(session('uid')){
            $this->title="会员中心";
            $where['member_id']=getUid();
            //会员订单状况
            $order_status=D("BookingView")->field("order_status,count(order_status) count")->where($where)->group("order_status")->select();
            foreach($order_status as $k=>$v){
                $status[$v['order_status']]=$v['count'];
            }
            $this->order_count=array_sum($status);
            $this->order_status=$status;

            //客服好评数
            $server=D("Member")->pjCount();
            $this->server=$server['server'];
            $this->sysMessageList=D('Message')->sysMessageList();

            $this->display();
        }else{
        //    $this->redirect('/member/login');
        }

    }



	
	
}