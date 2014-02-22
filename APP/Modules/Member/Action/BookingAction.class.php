<?php
// 首页控制器
class BookingAction extends IniAction {

    //订票中心
	function index(){
		$this->title="订票中心";
        $orderDB=D("AsmsOrder");        
		
		$where['hyid']=ASMSUID;
	//	$where['hyid']=0;

        //取消订单
        if($_GET['act']=='cancel' && I('id')){
            $id=I('id');
            $where['ddbh']=array('in',$id);
            if($orderDB->where($where)->count()){
                $rs=$orderDB->orderCancelAll($id);
                if($rs){
                    $this->success("成功","?update=1");
                }
                $this->error("失败");
            }else{
                $this->success("操作已完成");
            }
        }

        $pending=array();
        $where['ddzt']=array('in',array('7','8'));
        $list['cancel']=$orderDB->where($where)->select();

        $pagesize=10;//
        $where['ddzt']=array('not in',array('7','8'));
        $where['zf_fkf']=0;//未支付
        $list['pending']=$orderDB->where($where)->limit($pagesize)->select();

        $where['zf_fkf']=1; //已支付
        $list['process']=$orderDB->where($where)->limit($pagesize)->select();

        foreach($list as $key=>$val){
            if(is_array($val))
                 $val = $orderDB->format($val);
            foreach($val as $k=>$v){
                $v['hc']=str_split($v['hc'],3);
                $v['hc_a'] = D("City")->getCity( $v['hc']);
                $v['hc_n']=implode('-',$v['hc_a']);
                $list[$key][$k]=$v;
            }
        }

        $pending['count']=count($list['pending']);
        foreach($list['pending'] as $val){
            $pending['price']+=$val['xj'];
        }
        $this->pending=$pending;
        $this->list=$list;
     //  print_R($this->list);
		$this->display();
    }

    /*
     * Ajax 请求订单类表
     */
    function orderAjax(){
        if(IS_AJAX){
            $orderDB=D("AsmsOrder");
            $pagesize=10; //每页显示的记录
            $where['hyid']=ASMSUID;
            $where['zf_fkf']=I('zf_fkf')?I('zf_fkf'):0;//订单状态
            $count= $orderDB->where($where)->count();//总记录数
            $totlePage=ceil($count/$pagesize);//总页数
            $page=I('p')>1 && I('p')<=$totlePage?I('p'):1;//定义当前页
            $offset=($page-1)*$pagesize;
            $list= $orderDB->where($where)->limit($offset,$pagesize)->select();

            $data['totlePage']=$totlePage;
            $data['page']=$page;
            $data['status']=1;
            $data['list']=$list;
            $this->ajaxReturn($data);
        }
    }
	
	//国际机票订单详情
	function order(){
		$this->title="国际机票订单详情";
        $orderDB=D("AsmsOrder");
        $info=$orderDB->getOrderInfo(I('ddbh'));
        $info=$orderDB->format($info);

        $info['lx1']=$info['lx2']=$info['lx3']=0;//定义乘客类型数
        foreach($info['cjr_info'] as $key=>$val){
            $info['cjr_info'][$key]['lx']=$orderDB->cjrlx[$val['cjr_cjrlx']];
            if($val['cjr_cjrlx']==1){  //乘机人类型
                $info['lx1'] ++;
            }elseif($val['cjr_cjrlx']==2){
                $info['lx2'] ++;
            }else{
                $info['lx3'] ++;
            }
        }
        if($info['zf_fkf']==0 && !in_array($info['ddzt'],array('7','8'))){
            $this->is_pending=1; //需支付
        }
     //   dump($info);
        $this->info=$info;
		$this->display();
    }
	
	//订单在线支付
	function onlinepay(){
     //  dump( D("AsmsOrder")->orderPay(I("ddbh"),ASMSUID,'0.1',"201402211139551579"));
        if(!I("ddbh")) $this->error('请选择要支付的订单');

        $orderDB=D("AsmsOrder");
        if(!is_array(I("ddbh"))){
			$ddbh=explode (",",I("ddbh"));		
        }else{
            $ddbh=I("ddbh");
        }
        //生成支付id
		$wh['ddbh']=array('in',$ddbh);
        $this->order_pay_id=date("YmdHis").rand(1000,2000);
        $this->order_id_arr=implode(',',$ddbh);
  //$rs= $orderDB->orderPayList($ddbh);
  
		$res=$orderDB ->where($wh)->select();
		$rs['list']=$res;		
		foreach($res as $key=>$val){
			$rs['total_price'] += $val['xj'];
		
		} 
		
        $this->pay_total_price=$rs['total_price'];//总价格
        $this->pay_count=count($ddbh); //总个数
		
        $pay_list=$orderDB->format($rs['list']); //格式化
        foreach($pay_list as $k=>$v){
            $v['hc']=str_split($v['hc'],3);
            $v['hc_a'] = D("City")->getCity($v['hc']);
            $v['hc_n']=implode('-',$v['hc_a']);
            $pay_list[$k]=$v;
        }

        //行程
        foreach($pay_list as $key=>$val){
            $route[]=$val['hc_n'];
        }
        $this->route=implode(',',$route);

        $this->pay_list=$pay_list;
        $PayOrderDB=D('PayOrder');
        $where['member_id']=getUid();
        $list=$PayOrderDB->where($where)->limit(15)->select();
        $this->list = $PayOrderDB->format($list);
		
		$this->title="订单在线支付";
		$this->display();
    }

    /*
     * 支付返回
     */
    function payReturn(){

    }
	
}