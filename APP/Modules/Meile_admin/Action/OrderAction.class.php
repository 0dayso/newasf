<?php

class OrderAction extends CommonAction{
    function _before_index(){
   		//$this->relation=true;
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

	//asms订单
    function asmsOrder(){
        R('Asms/orderList');
        $this->display('Asms/orderList');
    }
	
	
	//订单管理
	function index(){
		$uid=getUid();
		$orderDB=D('AsmsOrder');
		
		$where['zf_fkf']=0;//未支付
        $no_zf=$orderDB->where($where)->select();
		foreach($no_zf as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);					
			$count=count($hc)-1;
			if($hc[0] == $hc[$count]){
				$no_zf[$k]['jp_type']='往返';
			}else{
				$no_zf[$k]['jp_type']='单程';
			}
			 $no_zf[$k]['hc_n']=implode('->',$hc);
		}

        $where['zf_fkf']=1; //已支付
        $yes_zf=$orderDB->where($where)->select();
		foreach($yes_zf as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);					
			$count=count($hc)-1;
			if($hc[0] == $hc[$count]){
				$yes_zf[$k]['jp_type']='往返';
			}else{
				$yes_zf[$k]['jp_type']='单程';
			}
			 $yes_zf[$k]['hc_n']=implode('->',$hc);
		}
		
		$where['zf_fkf']=2; //已取消
        $no_no=$orderDB->where($where)->select();
		foreach($no_no as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);					
			$count=count($hc)-1;
			if($hc[0] == $hc[$count]){
				$no_no[$k]['jp_type']='往返';
			}else{
				$no_no[$k]['jp_type']='单程';
			}
			 $no_no[$k]['hc_n']=implode('->',$hc);
		}
		
		$this->assign('no_zf',$no_zf);
		$this->assign('yes_zf',$yes_zf);
		$this->assign('no_no',$no_no);
		
		$this->display();
	}
	
	
	//编辑订单
	function order_edit(){		
		$orderDB=D('AsmsOrder');
		$where['ddbh']= I('id');
		$list=$orderDB->where($where)->find();
		
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
		
		$list['hc']= $orderDB->format($list['hc']);
		$list['hc']=str_split($list['hc'],3);
		$list['hc_a'] = D("City")->getCity( $list['hc']);
		$count=count($list['hc_a']);		
		if($list['hc_a'][0] == $list['hc_a'][$count]){
			$list['hc_d']=2;
			//去程
			for($i=0;$i<=$count-2;$i++){
				$hc1[]=$list['hc_d'][$i];
			}
			//返程
			$hc2[]=$list['hc_d'][$count-1];
			$hc2[]=$list['hc_d'][$count];
			
			$list['hc_n1']=implode('-',$hc1);
			$list['hc_n2']=implode('-',$hc2);
		}else{
			$list['hc_d']=1;
			$list['hc_n']=implode('-',$list['hc_a']);
		}
		
		//航班信息
		$hd_info=json_decode($list['hd_info']);
		$list['hd_info']=$hd_info;
		
		//乘客信息
		$cjr_info=json_decode($list['cjr_info']);
		$cjr_info=get_object_vars($cjr_info);
		foreach($cjr_info as $k=>$v){
			if($v->cjr_cjrlx == 1){$v->lx = '成人';$men++;$this->assign('men',$men);}
			if($v->cjr_cjrlx == 2){$v->lx = '儿童';$chl++;$this->assign('chl',$chl);}
			if($v->cjr_cjrlx == 3){$v->lx = '婴儿';$baby++;$this->assign('baby',$baby);}
		}
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
		
		//数据提交处理
		if($_POST){
			$wheres['ddbh']=$_POST['ddbh'];					
			$data['xsj']=$_POST['xsj'];//票面价格，即销售价
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['ysje']=$_POST['ysje'];//应付金额
			$data['cpsj']=$_POST['cpsj'];//出票时间
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//手机号
			$data['email']=$_POST['email'];//邮箱			
			$res=$orderDB->data($data)->save();
			if($res){
				echo "<script>alert('编辑成功');</script>";
			}else{
				echo "<script>alert('编辑失败');</script>";
			}
		}		
	    $this->display();
	}
	
	//查看订单
	function order_view(){
		$orderDB=D('AsmsOrder');
		$where['ddbh']= I('id');
		$list=$orderDB->where($where)->find();

		$list['hc']= $orderDB->format($list['hc']);
		$list['hc']=str_split($list['hc'],3);
		$list['hc_a'] = D("City")->getCity( $list['hc']);
		$count=count($list['hc_a']);
		if($list['hc_a'][0] == $list['hc_a'][$count-1]){
			$list['hc_d']=2;			
			//去程
			for($i=0;$i<=$count-2;$i++){
				$hc1[]=$list['hc_a'][$i];
			}
			//返程
			$hc2[]=$list['hc_a'][$count-2];
			$hc2[]=$list['hc_a'][$count-1];
			
			$list['hc_n1']=implode('->',$hc1);
			$list['hc_n2']=implode('->',$hc2);
		}else{
			$list['hc_d']=1;
			$list['hc_n']=implode('->',$list['hc_a']);
		}
				
		//航班信息
		$hd_info=json_decode($list['hd_info']);
		$list['hd_info']=$hd_info;

		//乘客信息
		$cjr_info=json_decode($list['cjr_info']);
		$cjr_info=get_object_vars($cjr_info);
		foreach($cjr_info as $k=>$v){
			if($v->cjr_cjrlx == 1){$v->lx = '成人';$men++;$this->assign('men',$men);}
			if($v->cjr_cjrlx == 2){$v->lx = '儿童';$chl++;$this->assign('chl',$chl);}
			if($v->cjr_cjrlx == 3){$v->lx = '婴儿';$baby++;$this->assign('baby',$baby);}
		}
		
		//支付状态
		if($list['zf_fkf'] == 0){			
			$list['zf_status']="[待支付]";
		}elseif($list['zf_fkf'] == 1){
			$list['zf_status']="[已付款]";
		}else{
			$list['zf_status']="[已取消]";
		}
		
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
	    $this->display();
	}

}