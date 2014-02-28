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
		$orderDB=D('AsmsOrder');
		$where['user_id']=getUid();
		
		//$this->totalCount= $orderDB->where($where)->count();//总记录数 
		//	$numPerPage=ceil($totalCount/$pagesize);//总页数
		
		//已取消
		$where['ddzt']=array('in',array('7','8')); 
		$no_no=$orderDB->where($where)->order('update_time desc')->select();
		foreach($no_no as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);
			$no_no[$k]['hc_n']=implode('->',$hc);
			if($v['lx'] == 1){
				$no_no[$k]['jp_type']="单程";
			}elseif($v['lx'] == 2){
				$no_no[$k]['jp_type']="往返";
			}elseif($v['lx'] == 3){
				$no_no[$k]['jp_type']="多程";
			}
		}
		
		//未支付
		$where['ddzt']=array('not in','7,8'); 
		$where['zf_fkf']=0;
		$no_zf=$orderDB->where($where)->order('update_time desc')->select();		
		foreach($no_zf as $k=>$v){			
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);
			$no_zf[$k]['hc_n']=implode('->',$hc);
			
			if($v['lx'] == 1){
				$no_zf[$k]['jp_type']="单程";
			}if($v['lx'] == 2){
				$no_zf[$k]['jp_type']="往返";
			}elseif($v['lx'] == 3){
				$no_zf[$k]['jp_type']="多程";
			}		
		}
		
		//已支付
		$where['zf_fkf']=1; 
		$yes_zf=$orderDB->where($where)->order('update_time desc')->select();
		foreach($yes_zf as $k=>$v){
			$hc= $orderDB->format($v['hc']);
			$hc=str_split($hc,3);
			$hc= D("City")->getCity($hc);
			$yes_zf[$k]['hc_n']=implode('->',$hc);
			
			if($v['lx'] == 1){
				$yes_zf[$k]['jp_type']="单程";
			}elseif($v['lx'] == 2){
				$yes_zf[$k]['jp_type']="往返";
			}elseif($v['lx'] == 3){
				$yes_zf[$k]['jp_type']="多程";
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
				
		//航班信息
		if($list['lx'] == 1){
			$hd_info=json_decode($list['hd_info']);
			foreach($hd_info as $k=>$v){			
				$hdinfo1[]=array(
					'date'=>$v->hd_cfsj,
					'time'=>$v->hd_cfsj_p,
					'hbh' =>$v->hbh,
					'cw'  =>$v->cw,
					'jx'  =>$v->hd_fjjx,
					'cfcity'=>$v->hd_cityname.'('.$v->hd_cfcity.')',
					'ddcity'=>$v->hd_ddcityname.'('.$v->hd_ddcity.')'
				);
			}
		}

		if($list['lx'] == 2){
			$hd_info=json_decode($list['hd_info']);
			foreach($hd_info as $k=>$v){			
				$hdinfo2[]=array(
					'date'=>$v->hd_cfsj,
					'time'=>$v->hd_cfsj_p,
					'hbh' =>$v->hbh,
					'cw'  =>$v->cw,
					'jx'  =>$v->hd_fjjx,
					'cfcity'=>$v->hd_cityname.'('.$v->hd_cfcity.')',
					'ddcity'=>$v->hd_ddcityname.'('.$v->hd_ddcity.')'				
				);
			}
		}
		
		if($list['lx'] == 3){
			$hd_info=json_decode($list['hd_info']);
			foreach($hd_info as $k=>$v){			
				$hdinfo3[]=array(
					'date'=>$v->hd_cfsj,
					'time'=>$v->hd_cfsj_p,
					'hbh' =>$v->hbh,
					'cw'  =>$v->cw,
					'jx'  =>$v->hd_fjjx,
					'cfcity'=>$v->hd_cityname.'('.$v->hd_cfcity.')',
					'ddcity'=>$v->hd_ddcityname.'('.$v->hd_ddcity.')'				
				);
			}
		}
		
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
		}elseif($list['zf_fkf'] == 2){
			$list['zf_status']="[已取消]";
		}
		
		$this->assign('hdinfo1',$hdinfo1);
		$this->assign('hdinfo2',$hdinfo2);
		$this->assign('hdinfo3',$hdinfo3);
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
		
		if($_POST){				
			//航程信息
			if($_POST['t'] == 1){//单程
				$from_wz1=stripos($_POST['from1'],'(');
				$cityname1=substr($_POST['from1'],0,$from_wz1);//出发城市名-中文
				$hd_cfcity1=substr($_POST['from1'],-4,-1);//出发城市名-三字码
				
				$to_wz1=stripos($_POST['to1'],'(');
				$ddcityname1=substr($_POST['to1'],0,$to_wz1);//中文
				$hd_ddcity1=substr($_POST['to1'],-4,-1);	//三字码						   
											  
				$hb=array();
				$hb[]=array(
					'hbh'       =>$_POST['hbh1'],
					'cw'        =>$_POST['cw1'],				
					'hd_cfsj'   =>$_POST['date1'],
					'hd_cfsj_p' =>$_POST['time1'],
					'cfsj'      =>$_POST['date1'].'&nbsp;'.$_POST['time1'],
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity1.$hd_ddcity1.'$nbsp;$nbsp;'.$cityname1.'-'.$ddcityname1,
					'hd_cfcity' =>$hd_cfcity1,
					'hd_ddcity' =>$hd_ddcity1,
					'hd_cityname'=>$cityname1,					
					'hd_ddcityname'=>$ddcityname1					
				);	
				$hb_info=json_encode($hb);
				$data['lx']=2; //类型-往返
				$data['hc']=$hd_cfcity2.$hd_ddcity2.$hd_cfcity3.$hd_ddcity3;//航程
				$data['hbh']=$_POST['hbh2'].'-'.$_POST['hbh3'];//航班号
			}
						
			if($_POST['t'] == 2){//往返
				$hb=array();
				//去程
				$from_wz2=stripos($_POST['from2'],'(');
				$cityname2=substr($_POST['from2'],0,$from_wz2);//出发城市名-中文
				$hd_cfcity2=substr($_POST['from2'],-4,-1);//出发城市名-三字码
				
				$to_wz2=stripos($_POST['to2'],'(');
				$ddcityname2=substr($_POST['to2'],0,$to_wz2);//中文
				$hd_ddcity2=substr($_POST['to2'],-4,-1);	//三字码	
				
				$hb[]=array(
					'hbh'       =>$_POST['hbh2'],
					'cw'        =>$_POST['cw2'],				
					'hd_cfsj'   =>$_POST['date2'],
					'hd_cfsj_p' =>$_POST['time2'],
					'cfsj'      =>$_POST['date2'].'&nbsp;'.$_POST['time2'],
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity2.$hd_ddcity2.'$nbsp;$nbsp;'.$cityname2.'-'.$ddcityname2,
					'hd_cfcity' =>$hd_cfcity2,
					'hd_ddcity' =>$hd_ddcity2,
					'hd_cityname'=>$cityname2,					
					'hd_ddcityname'=>$ddcityname2	
				);
				
				//返程
				$from_wz3=stripos($_POST['from3'],'(');
				$cityname3=substr($_POST['from3'],0,$from_wz3);//出发城市名-中文
				$hd_cfcity3=substr($_POST['from3'],-4,-1);//出发城市名-三字码
				
				$to_wz3=stripos($_POST['to3'],'(');
				$ddcityname3=substr($_POST['to3'],0,$to_wz3);//中文
				$hd_ddcity3=substr($_POST['to3'],-4,-1);	//三字码					
				$hb[]=array(
					'hbh'       =>$_POST['hbh3'],
					'cw'        =>$_POST['cw3'],				
					'hd_bzbz'   =>$_POST['date3'],
					'hd_bzbz_p' =>$_POST['time3'],
					'ddsj'      =>$_POST['date3'].'&nbsp;'.$_POST['time3'],
					'hd_fjjx'   =>$_POST['fjjx1'],
					'hc'        =>$hd_cfcity3.$hd_ddcity3.'$nbsp;$nbsp;'.$cityname3.'-'.$ddcityname3,
					'hd_cfcity' =>$hd_cfcity3,
					'hd_ddcity' =>$hd_ddcity3,
					'hd_cityname'=>$cityname3,					
					'hd_ddcityname'=>$ddcityname3
				);
				$hb_info=json_encode($hb);
				$data['lx']=2;
				$data['hc']=$hd_cfcity2.$hd_ddcity2.$hd_cfcity3.$hd_ddcity3;
			}
			
			if($_POST['t'] == 3){//多程
				$hb=array();
				$hcdata=$_POST['hcdata'];
				foreach($hcdata['hbh'] as $k=>$v){
					
					$from_wz=stripos($hcdata['from'][$k],'(');
					$cityname=substr($hcdata['from'][$k],0,$from_wz);//出发城市名-中文
					$hd_cfcity=substr($hcdata['from'][$k],-4,-1);//出发城市名-三字码
					
					$to_wz=stripos($hcdata['to'][$k],'(');
					$ddcityname=substr($hcdata['to'][$k],0,$to_wz);//中文
					$hd_ddcity=substr($hcdata['to'][$k],-4,-1);	//三字码
					
					$hb[]=array(
						'hbh'       =>$hcdata['hbh'][$k],
						'cw'        =>$hcdata['cw'][$k],
						'hd_cfsj'   =>$hcdata['date'][$k],
						'hd_cfsj_p' =>$hcdata['time'][$k],
						'cfsj'      =>$hcdata['date'][$k].'&nbsp;'.$hcdata['time'][$k],
						'hd_fjjx'   =>$hcdata['fjjx'][$k],
						'hc'        =>$hd_cfcity.$hd_ddcity.'&nbsp;&nbsp;'.$cityname.'-'.$ddcityname,
						'hd_cfcity' =>$hd_cfcity,
						'hd_ddcity' =>$hd_ddcity,					
						'hd_cityname'=>$cityname,
						'hd_ddcityname'=>$ddcityname
					);
					$data['hc'].=$hd_cfcity.$hd_ddcity;
					$data['hbh'].=$hcdata['hbh'][$k].'-';//航班号
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

			//写入数据库
			$year=substr($_POST['cpsj'],0,4);//取得年份
			$month=substr($_POST['cpsj'],5,2);//取得月份			
			$day=substr($_POST['cpsj'],8,2);//取得几号
			$hour=substr($_POST['cpsj_p'],0,2)?substr($_POST['cpsj_p'],0,2):'00';
			$min=substr($_POST['cpsj_p'],3,2)?substr($_POST['cpsj_p'],3,2):'00';
			$second=substr($_POST['cpsj_p'],6,2)?substr($_POST['cpsj_p'],6,2):'00';			
			$date=$year.$month.$day.$hour.$min.$second;
			$data['cpsj']=strtotime($date);//出票时间
			
			$data['xsj']=$_POST['xsj'];//票面价格
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['jj']=$_POST['jsf'];//机建费
			$data['xjj']=0;//现金券
			$data['ysje']=$_POST['ysje'];//应付金额
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//联系人手机
			$data['email']=$_POST['email'];//联系人邮箱
			$data['hbh']=$_POST['num1'];//航班号			
			$data['rs']=$_POST['athud']+$_POST['chilren']+$_POST['baby'];//人数
			$data['xm']=$_POST['nklxr'];//姓名					
			$data['zf_fkf']=$_POST['zf_fkf'];//支付状态			
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
				
		//航班信息
		$hd_info=json_decode($list['hd_info']);
		foreach($hd_info as $k=>$v){			
			$hdinfo[]=array(
				'hc'  =>$v->hd_cityname.'->'.$v->hd_ddcityname,
				'cfsj'=>$v->cfsj,
				'hbh' =>$v->hbh,
				'cw'  =>$v->cw,
				'jx'  =>$v->hd_fjjx
			);
		}

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
		}elseif($list['zf_fkf'] == 2){
			$list['zf_status']="[已取消]";
		}
		
		$this->assign('hdinfo',$hdinfo);
		$this->assign('cjr_info',$cjr_info);
	    $this->assign('list',$list);
		$this->display();
	}
	
	//取消订单
	function order_cancel(){
		$orderDB=D('AsmsOrder');
		$this->ddbh=I('id');
		if($_POST){
			$where['ddbh']=I('id');
			$res=$orderDB->where($where)->setField('ddzt',8);
			if($res){
				$this->success('取消成功');
			}
		}else{
			$this->display();
		}
	}

}