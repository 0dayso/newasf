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
		$member=D('Member');	
		$where_use_id['use_id']=getUid();
		$hyid=$member->where($where_use_id)->field('asms_member_id')->select();	
		
		//$where['hyid']=array('in',$hyid);
		$where['zf_fkf']=0;//未支付			
		$no_zf=$orderDB->where($where)->limit(50)->select();
		foreach($no_zf as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);					
			$count=count($hc)-1;
			$no_zf[$k]['hc_n']=implode('->',$hc);
			if($v['lx'] == 1){
				$no_zf[$k]['jp_type']='单程';
			}
			if($v['lx'] == 2){
				$no_zf[$k]['jp_type']='往返';
			}			
			if($v['lx'] == 3){
				$no_zf[$k]['jp_type']='多程';
			}			
		}

		$where['zf_fkf']=1; //已支付
		$yes_zf=$orderDB->where($where)->limit(50)->select();
		foreach($yes_zf as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);
			$yes_zf[$k]['hc_n']=implode('->',$hc);
			
			if($v['lx'] == 1){
				$no_zf[$k]['jp_type']='单程';
			}
			if($v['lx'] == 2){
				$no_zf[$k]['jp_type']='往返';
			}			
			if($v['lx'] == 3){
				$no_zf[$k]['jp_type']='多程';
			}				
			
		}
		
		$where['zf_fkf']=2; //已取消
		$no_no=$orderDB->where($where)->select();
		foreach($no_no as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);
			$no_no[$k]['hc_n']=implode('->',$hc);
			if($v['lx'] == 1){
				$no_zf[$k]['jp_type']='单程';
			}
			if($v['lx'] == 2){
				$no_zf[$k]['jp_type']='往返';
			}			
			if($v['lx'] == 3){
				$no_zf[$k]['jp_type']='多程';
			}
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
		$count=count($list['hc_a'])-1;

		
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
			$orderDB=D('AsmsOrder');
			//航程信息
			if($_POST['t'] == 1){//单程
				$hb=array();
				$hb[]=array(
					'hbh'      =>  $_POST['hbh1'],
					'cw'       =>  $_POST['cw1'],
					'cfcity'      =>  $_POST['from1'],
					'ddcity'       =>  $_POST['to1'],					
					'hd_cfsj'  =>  $_POST['date1']
				);	
				$hb_info=json_encode($hb);
				$data['lx']=1;
			}
			
			if($_POST['t'] == 2){//往返
				$hb[0]=array(
					'hbh'      =>  $_POST['hbh2'],
					'cw'       =>  $_POST['cw2'],
					'cfcity'      =>  $_POST['from2'],
					'ddcity'       =>  $_POST['to2'],					
					'hd_cfsj'  =>  $_POST['date2']
				);
				$hb[1]=array(
					'hbh'      =>  $_POST['hbh3'],
					'cw'       =>  $_POST['cw3'],
					'cfcity'      =>  $_POST['from3'],
					'ddcity'       =>  $_POST['to3'],					
					'hd_cfsj'  =>  $_POST['date3']
				);
				$hb_info=json_encode($hb);
				$data['lx']=2;
			}
			
			if($_POST['t'] == 3){//多程
				$hcdata=$_POST['hcdata'];
				foreach($hcdata['hbh'] as $k=>$v){
					$hb[]=array(
						'hbh'      =>  $hcdata['hbh'][$k],
						'cw'       =>  $hcdata['cw'][$k],
						'cfcity'   =>  $hcdata['from'][$k],
						'ddcity'   =>  $hcdata['to'][$k],					
						'hd_cfsj'  =>  $hcdata['date'][$k]
					);
				}
				$hb_info=json_encode($hb);
				$data['lx']=3;				
			}
			//乘机人信息
			$i=0;
			foreach($_POST['info']['cjr_cjrxm'] as $k=>$v){
				$i++;
				$info[$i]=array(
					'cjr_cjrxm'	=>$_POST['info']['cjr_cjrxm'][$k],//姓名
					'cjr_lx'	=>$_POST['info']['cjr_lx'][$k],//乘客类型
					'cjr_clkid'	=>$_POST['info']['cjr_clkid'][$k],//证件号
					'cjr_xsj'	=>$_POST['info']['cjr_xsj'][$k],//票价
					'cjr_jsf'	=>$_POST['info']['cjr_jsf'][$k],//机建
					'cjr_tax'	=>$_POST['info']['cjr_tax'][$k]//税费
				);			
			}
			$obj=json_encode($info);
			
		
			//关联asms
//			$userinfo=$this->userInfo;
//			$asms_common=array(
//				'bxjsj'      =>'',
//				'clsx'       =>'',
//				'czfrom'     =>'',	
//				'close'      =>'',
//				'cgmk_gn'    =>'',
//				'cgmk_gj'    =>'',
//				'cu_ifljjf'  =>'',	
//				'clyy'       =>'',
//				'ddfrom'     =>'',
//				'ddxgtype'   =>'',
//				'gngjlx'     =>'',
//				'ifxf_dd'    =>'',
//				'iftb'       =>'',
//				'in_zk_fklx' =>'',
//				'mkbh'       =>'',
//				'openlx'     =>'',
//				'platid'     =>'',
//				'sfsgd'      =>'',
//				'sfbm'       =>'',
//				'tjlx'       =>'',
//				'type'       =>'',	
//				'td_name'    =>'',
//				'wjlpjsfjs'  =>'',
//				'xjjlfs'     =>'',
//				'xmdh'       =>'',
//				'xsmk'       =>'',	
//				'zhcs'       =>'',
//				'zrs'        =>'',
//				'zkfx'       =>'',				
//				'ddlx'       =>$data['lx'],//订单类型
//				'ddly'       =>'',
//				'ddzt'       =>'',
//				'ddtzbz'     =>'',
//				'ddlx_dd'    =>'',
//				'ddtzfs'     =>'',
//				'ct_lxrkh'   =>'',//联系人
//				'ct_hyxm'    =>'',//会员姓名
//				'ct_hyid'    =>$_POST['hyid'],//会员id
//				'ct_nxr'     =>$_POST['nklxr'],//联系人
//				'ct_nxrdh'   =>$_POST['lxdh'],//联系人电话
//				'ct_smsmobilno' =>'',
//				'ct_xcd'     =>'',
//				'ct_sjr'     =>'',
//				'ct_yzbm'    =>'',
//				'ct_sjdz'    =>'',
//				'dp_compid'  =>$userinfo['company_id'],//订票-公司id
//				'dp_deptid'  =>$userinfo['department_id'],//订票-部门id
//				'dp_dpyid'   =>$userinfo['id'],//订票-订票员id
//				'dp_compmc'  =>'',
//				'dp_dpyid'   =>'',	
//				'ds_compid_dd'=>'',
//				'ds_deptid_dd'=>'',
//				'ps_pszt'    =>'',
//				'ps_pszt'    =>'',
//				'ps_yqrqsj'  =>'',
//				'ps_compid_dd'=>'',
//				'ps_deptid_dd'=>'',
//				'pnrno'      =>'',
//				'pnr_no'     =>'',
//				'pnr_zt'     =>'',	
//				'pnr_lr'     =>'',
//				'pnr_nr_b'   =>'',
//				'pnr_hcglgj' =>'',
//				'version'    =>'',
//				'vipxmmc'    =>'',
//				'vipusermc'  =>'',
//				'vip_jsbmName'=>'',
//				'vip_jsbm'   =>'',
//				'vip_dp_userid'=>'',
//				'vip_bmdh'   =>'',
//				'vip_ddbh'   =>'',
//				'ct_cpfs'    =>'',//出票方式
//				'cplx'       =>'',//出票类型
//				'confirmDate'=>'',//确认日期
//				'confirmTime'=>'',//确认时间
//				'bxlx'       =>'',//保险类型
//				'qpbm'       =>'',	
//				'ps_lx'      =>'',//配送方式
//				'ps_city'    =>'',//配送城市
//				'ps_dz'      =>'',//配送地址
//				'yqdate'     =>'',//要求日期
//				'sj'         =>'',//时间	
//				'ps_bz'      =>'',//配送备注
//				'ddbz'       =>'',//订单备注
//				'cjr_index'  =>'',
//				'cjr_bxxjjsj'=>'',
//				'cjr_bx'     =>'',
//				'cjr_pjxjjsj'=>'',
//				'cjr_sjhm'   =>'',//手机号码
//				'cjr_sfmp'   =>'',//是否免费
//				'cjr_sfzsp'  =>'',//是否赠送票
//				'cjr_cgj'    =>'',//
//				'cjr_pjsjjsfl'=>'',
//				'cjr_pjsjjsj'=>'',
//				'cjr_jj'     =>'',//加价
//				'cjr_bxfs'   =>'',//保险份数
//				'cjr_bxdj'   =>$_POST['taxa'],//保险单价
//				'cjr_zsbx'   =>'',//赠送保险份数
//				'cjr_bxjl'   =>'',//保险奖励
//				'cjr_pjxjjsfl'=>'',//
//				'cjr_lwyj'   =>''
//			);
//			
//			foreach($info as $v){//乘机人信息
//				$cjr_str .= http_build_query($v)."&";
//			}			
//			foreach($hb as $v){//航班信息
//				$hb_str .= http_build_query($v)."&";
//			}
//			$common_str= http_build_query($asms_common)."&";			
//			$post_str=$common_str.$cjr_str.$hb_str;
//			
//			$url=C('ASMS_HOST')."/asms/ydzx/ddgl/kh_khdd_ddgl.shtml?cs=5&count=$page_r&start=$page_start&";					
//			$rs=curl_post($url,$post_str,COOKIE_FILE);				
//			if(!$rs){return -1;}
//			if(empty($rs)){
//				$this->error="连接失败";
//				return false;
//			}
		
			//写入数据库
			$data['xsj']=$_POST['xsj'];//票面价格
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['jj']=$_POST['jsf'];//机建费
			$data['xjj']=0;//现金券
			$data['ysje']=$_POST['ysje'];//应付金额
			$data['cpsj']=$_POST['cpsj'];//出票时间
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//联系人手机
			$data['email']=$_POST['email'];//联系人邮箱
			$data['hbh']=$_POST['num1'];//航班号			
			$data['rs']=$_POST['athud']+$_POST['chilren']+$_POST['baby'];//人数
			$data['xm']=$_POST['nklxr'];//姓名			
			$data['hyid']=I('id');//会员id			
			$data['zf_fkf']=0;//支付状态			
			$data['dprq']=time();//订票日期
			$data['update_time']=time();//更新时间
			$data['info_update_time']=time();//详情更新时间			
			$data['hd_info']=$hb_info;//航程信息
			$data['cjr_info']=$obj;//乘机人信息			
			
			$wh['ddbh']=$_POST['ddbh'];			
			$res=$orderDB->where($wh)->data($data)->save();
			if($res){
				$this->success('编辑成功');
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