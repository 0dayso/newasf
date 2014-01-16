<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-9-5
 * Time: 下午5:43
 * To change this template use File | Settings | File Templates.
 */

class ClaimSystemSAction extends CommonAction{
    //帐本列表
    function _before_index(){
        $this->relation=true;
        $this->actionName='ClaimBooks';
        $where['company_id']=array(array('eq',$this->userInfo['company_id']),array('eq',0),'or');
        $where['view']=1;
        $where['_logic'] = 'or';
        $map['_complex']=$where;
        if(!auth_check()){
            $this->map=$map;
        }
        $this->order="id desc";
        $this->map['type']=0;
       $this->index(D('ClaimBooks'));
    }
    //帐本详情列表
    function claimList(){
        $book_id=isset($_GET['book_id'])?$_GET['book_id']:$_POST['book_id'];
        if(!auth_check()){ //权限
            $wheres['company_id']=array(array('eq',$this->userInfo['company_id']),array('eq',0),'or');
            $wheres['view']=1;
            $wheres['_logic'] = 'or';
            $wheres2['_complex']=$wheres;
            unset($wheres);
            $wheres=$wheres2;
        }
        $wheres['id']=$book_id;
        $this->bookInfo=D('ClaimBooks')->where($wheres)->find();
        if(!$this->bookInfo && !auth_check()){
            $this->error('没有权限');
        }

        $this->book_id=$book_id;
        //     $this->relation=true;
        $this->actionName='ClaimData';
        if(I('so')){ //搜索
            $where['ClaimData.id']  = array('like',"%".$_POST['so']."%");
            $where['ClaimData.arrival_amount']  = array('like',"%".I('so')."%");
            $where['ClaimData.bank']  = array('like',"%".I('so')."%");
            $where['ClaimData.claim_name']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
        }

        if($_POST['so_date1'] && $_POST['so_date2']){ //日期搜索
            $map['arrival_date']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }

        $this->order='id desc';//默认倒序

        if(!empty($map))
            $this->map = $map;
        $this->index(D('ClaimDataView'));
        $list=$this->list;
        foreach($list as $k=>$v){
            if(!$v['create_username']){
                $list[$k]['create_username']=$v['create_username2'];
            }
            if(!$v['claim_username']){
                $list[$k]['claim_username']=$v['claim_username2'];
            }
            if(!$v['update_username']){
                $list[$k]['update_username']=$v['update_username2'];
            }
            if(!$v['audit_username']){
                $list[$k]['audit_username']=$v['audit_username2'];
            }
            if($v['edit_order_id']){
                $list[$k]['order_id']=$v['edit_order_id'];
            }
            if($v['edit_ticket_date']){
                $list[$k]['ticket_date']=$v['edit_ticket_date'];
            }
            if($v['edit_claim_remark']){
                $list[$k]['claim_remark']=$v['edit_claim_remark'];
            }
            if($v['edit_claim_remitter']){
                $list[$k]['claim_remitter']=$v['edit_claim_remitter'];
            }
        }
        $this->list=$list;
        $this->display();
    }

    // 查看详情信息
    function _before_read(){
        $this->relation=true;
        $this->actionName='ClaimData';
    }

    //编辑认帐信息
    function editClaim(){
        if($_POST){
            $ClaimData=D('ClaimData');
            $where['id']=I('id');
            $uid=$ClaimData->field('id,claim_uid,status')->find(I('id'));
            if($uid['status']==0){
                $data['id']=I('id');
                $data['order_id']=I('order_id');
                $data['ticket_date']=strtotime(I('ticket_date'));
                $data['claim_remitter']=I('claim_remitter');
                $data['claim_name']=I('claim_name');
                $data['claim_remark']=I('claim_remark');
                $data['department_id']=I('department_id');
                $data['claim_time']=time();
                $data['claim_uid']=getUid();
                $data['status']=1;
                if(!$ClaimData->create($data)){
                    $this->error($ClaimData->getError());
                }
                $rs=$ClaimData->save();
                if($rs)
                    $this->success('成功');
                else
                    $this->error('失败');
            }else{
                $this->error('非法操作');
            }
        }else{
            $ClaimData=D('ClaimData');
            $where['id']=I('id');
            $userinfo = D('User')->where('id='.getUid())->find();

            $info=$ClaimData->where($where)->find();

            $wheres['id']=$info['book_id'];
            if(!auth_check()){ //权限
             //   $wheres['company_id']=array(array('eq',$userinfo['company_id']),array('eq',0),'or');
            }
            $bookInfo=D('ClaimBooks')->where($wheres)->find();
            if(!$bookInfo){
                $this->error('没有权限');
            }

            $access=D('Access');
            $info['departmentOption']=$access->getOption('department',array('id'=>$userinfo['department_id']));
            if(!$info['claim_name'])
                 $info['claim_name']=$userinfo['name'];
            $this->vo=$info;
            $this->display();
        }
    }

    //申请支付帐本列表
    function applyBook(){
        $this->actionName='ClaimBooks';
        $this->relation=true;
        $this->map['type']=1;
        $this->index(D('ClaimBooks'));
        $this->display();
    }

    //申请付款信息列表
    function applyPay(){
        $this->relation=true;
        if(I('so')){ //搜索
            $where['id']  = array('like',"%".$_POST['so']."%");
            $where['apply_amount']  = array('like',"%".I('so')."%");
            $where['apply_name']  = array('like',"%".I('so')."%");
            $where['account_name']  = array('like',"%".I('so')."%");
            $where['apply_remark']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
        }

        if($_POST['so_date1'] && $_POST['so_date2']){ //日期搜索
            $map['create_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }

     //   $map['department_id']=$this->userInfo['department']['id'];
     //   $map['create_uid']=getUid();
     //   $map['_logic'] = 'or';
        $department_id=$this->userInfo['department']['id'];
        $department_where=$department_id?"department_id = $department_id":"department_id =''";

        $this->map = $map;
        $this->map['_string'] = "$department_where or create_uid = ".getUid();
        if(I('book_id')){
            $this->map['book_id'] = I('book_id');
        }else{
            $this->error('禁止访问');
        }
    //   dump( D('ClaimPaymentView')->find(21));
        $this->bookInfo=D('ClaimBooks')->find(I('book_id'));
        $this->order='id desc';//默认倒序
        $this->index(D('ClaimPayment'));
    //    print_r($this->list);
        $amount=0;
        foreach($this->list as $v){
            $amount+=$v['apply_amount']; //计算当前页金额
        }
        $this->amount=$amount;
        $this->list=D('ClaimPayment')->format($this->list);
        $this->totalAmount=D('ClaimPayment')->where($this->map)->sum('apply_amount');//计算总额
        $this->display();
    }

    //添加支付申请
    function addPay(){
        if(IS_POST){
            $this->actionName='ClaimPayment';
            $this->insert();
        }else{
            $access=D('Access');
            $info=D('User')->where(array('id'=>$this->userInfo['department_id']))->find();
            $info['departmentOption']=$access->getOption('department',array('id'=>$this->userInfo['department_id']));
            $info['apply_name']=$this->userInfo['name'];
            $this->vo=$info;
            $this->display();
        }
    }

    //编辑支付申请
    function editPay(){
        $ClaimPayment=D('ClaimPayment');
        $where['id']=I('id');
        $info=$ClaimPayment->find(I('id'));
        if($info['create_uid']!=getUid() && !auth_check()){
            $this->error('没有权限');
        }
        if($info['status']!=0 or $info['status']!=4){
            $this->error('当前状态不可操作');
        }

        if($_POST){
                $data=$_POST;
                $data['id']=I('id');
                $data['status']=0;
                if(!$ClaimPayment->create($data)){
                    $this->error($ClaimPayment->getError());
                }
                $rs=$ClaimPayment->save();
                if($rs)
                    $this->success('成功');
                else
                    $this->error('失败');
        }else{
            $access=D('Access');
            $info['departmentOption']=$access->getOption('department',array('id'=>$info['department_id']));
            $this->vo=$info;
            $this->display();
        }
    }

    // 查看支付申请详情信息
    function readPay(){
        $ClaimPayment=D('ClaimPayment');
        $info=$ClaimPayment->relation(true)->find(I('id'));
        if($info['create_uid']!=getUid() && !auth_check()){
            $this->error('没有权限');
        }
        $info=$ClaimPayment->format(array(0=>$info));
        $this->vo=$info[0];
        $this->display();
    }

    /**
     * 有新的认帐信息
     */
    function newTips(){
        $map['type']=0;
        $map['status']=array('neq',-1);
        $map['company_id']=array('in','0',$this->userInfo['company_id']);
        $book=D('ClaimBooks')->field('id,name')->where($map)->select();
        $arrId='';
        foreach($book as $v){
            $arrId.=$v['id'].',';
        }
        $arrId=trim($arrId,',');
        $first=I('first');
        $where['status']=0;
        $where['book_id']=array('in',$arrId);
        $last_tips_time = cookie('last_tips_time');  //最后提示时间
        if($first){
            $rs=D('ClaimData')->where($where)->count();
            if($rs){
            $data['msg']="认帐系统还有".$rs."条未认帐信息  <br><a href='".U('ClaimSystemS/index')."' target='navTab' rel='newTips'>点击查看</a>";
            }
        }else{
            if($last_tips_time){  $where['create_time']=array('gt', $last_tips_time);}
            $rs= D('ClaimData')->where($where)->order('id desc')->limit(1)->find();
            if($rs){
            $data['msg']="认帐系统还有新的信息发布  <br>到帐金额".$rs['arrival_amount']."<a href='".U('ClaimSystemS/claimList/book_id/'.$rs['book_id'])."' target='navTab' rel='newTips'>点击查看</a>";
            }
        }
        cookie('last_tips_time',time());
        $this->ajaxReturn($data);
    }

}