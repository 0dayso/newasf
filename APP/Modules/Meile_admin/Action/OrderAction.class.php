<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-24
 * Time: 下午2:22
 * To change this template use File | Settings | File Templates.
 */

class OrderAction extends CommonAction{
    function _before_index(){
     //   $this->relation=true;
    }

   function read(){
        $this->vo=D('Complaint')->getInfo();
        $this->display();
    }

    /*
     * 支付订单
     */
    function payOrder(){
        $this->relation=true;
        if(I('so')){
            $where['member_id']  = array('like',"%".I('so')."%");
            $where['order_id_arr']  = array('like',"%".$_POST['so']."%");
            $where['route']  = array('like',"%".I('so')."%");
            $where['order_price']  = array('like',"%".I('so')."%");
            $where['remark']  = array('like',"%".I('so')."%");
            $where['_logic'] = 'or';
            $map['_complex']=$where;
        }
        if(I('so_date1')&& I('so_date2')){
            $map['update_time']=array(array('egt',strtotime(I('so_date1'))),array('elt',strtotime(I('so_date2'))));
        }

        $this->order='id desc';
        if(!empty($map))
            $this->map = $map;
        $this->index(D('PayOrder'));
     //   print_r($this->list);
        $this->display();
    }

    function asmsOrder(){
        R('Asms/orderList');
        $this->display('Asms/orderList');
    }


}