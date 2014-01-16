<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午3:33
 * To change this template use File | Settings | File Templates.
 */
class OrderAction extends IniAction{
    function index(){
        $booking=D("BookingView");
        import("ORG.Util.Page");       //载入分页类
        $where=array();
        if(I('where')){
            foreach(I('where') as $k=>$v){
                if($v!=''){
                    $where[$k]=$v;
                }
            }
        }
        if(I('search')!=''){
            $search=I('search');
            $where['_string'] = "(order_id like '%{$search}%')  OR ( email like '%{$search}%') OR ( cell like '%{$search}%') OR ( price_detail like '%{$search}%') ";
        }
     //   $where['member_id']=array("neq","");

        $count = M("booking")->where($where)->count();
        $page = new Page($count,20,'','',1);
        $showPage = $page->show();
        $this->assign("page", $showPage);

        $this->list=$booking->bookingList($where,$page->firstRow.','.$page->listRows);

        if(IS_AJAX){
            $datalis['list']=$this->list;
            $datalis['page']=$showPage;
            $this->ajaxReturn($datalis);exit;
        }


        $this->title="订单管理";
        $this->display();
    }


    function orderEdie(){
        if($_GET['act']){
            if(I('act')=='view'){
                $this->ajaxReturn( D('Member')->userInfo(I('id')));
            }elseif($_GET['act']=='edit'){
                if(IS_POST){
                    echo  json_encode(D('Booking')->edit());
                    exit;
                }
                $where['id']=I('id');
                $this->info=D('BookingView')->bookingInfo($where);
            //    print_r( $this->info);
                $this->display('order_edit');
            }
        }
    }

}