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
		$asmsid=D('User')->where('id='.getUId())->field('asms_user_id,username')->find();		
		$username=substr($asmsid['username'],0,3);
		
		if($username=="can"){//国际客服部
			$this->user_type="can";
			$res=$orderDB->userOrder($asmsid['asms_user_id']);	
			if($res){		
				foreach($res as $key=>$val){
					///
					if($val['lx'] == 1){
						$res[$key]['jp_type']="单程"; 
					}elseif($val['lx'] == 2){
						$res[$key]['jp_type']="往返";
					}elseif($val['lx'] == 3){
						$res[$key]['jp_type']="联程";
					}elseif($val['lx'] == 4){
						$res[$key]['jp_type']="缺口";
					}
					///
					$res[$key]['hyid']=$val['hykh'];
					///
					$hc= $orderDB->format($val['hc']);
					$hc=str_split($hc,3);
					$hc= D("City")->getCity($hc);
					$res[$key]['hc_n']=implode('->',$hc);	
					///
					if($val['ddzt'] ==8 || $val['ddzt'] ==7){//已取消
						$no_no[]=$res[$key];					
					}else{
						if($val['zf_fkf'] == 0){//未支付
							$no_zf[]=$res[$key];									
						}else{//已支付
							$yes_zf[]=$res[$key];	
						}				
					}
				}
			}		
		}else{//国际商旅部		
			$where['user_id']=getUid();		
			$numPerPage=I('numPerPage');//每页显示数量
				if($numPerPage==""){$numPerPage=30;}
			$pageNum=I('pageNum');//定义当前页 
				if($pageNum<=1){$pageNum=1;}		
			$offset=($pageNum-1)*$numPerPage;//步长	
			$this->numPerPage=$numPerPage;	
			$this->currentPage=$pageNum;
			
			//已取消
			$where['ddzt']=array('in',array('7','8')); 
			$this->totalCount33= $orderDB->where($where)->count();//总记录数
			$no_no=$orderDB->where($where)->limit($offset,$numPerPage)->order('update_time desc')->select();
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
			$this->totalCount11= $orderDB->where($where)->count();//总记录数
			$no_zf=$orderDB->where($where)->limit($offset,$numPerPage)->order('update_time desc')->select();
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
			$this->totalCount22= $orderDB->where($where)->count();//总记录数
			$yes_zf=$orderDB->where($where)->limit($offset,$numPerPage)->order('update_time desc')->select();
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
			if($_POST['xsj'] == ""){				
				$this->error('票面总价不能为空,请输入数字');
			}			
			if($_POST['sf'] == ""){				
				$this->error('总税费不能为空,请输入数字，如无请填0');
			}	
			if($_POST['taxa']==""){				
				$this->error('总保险费不能为空,请输入数字，如无请填0');
			}				
			if($_POST['jsf'] == ""){				
				$this->error('总机建费不能为空,请输入数字，如无请填0');
			}
			if($_POST['ysje'] == ""){				
				$this->error('应付金额不能为空,请输入数字，如无请填0');
			}			
			if($_POST['athud']== ""){				
				$this->error('成人数量不能为空,请输入数字，如无请填0');
			}
			if($_POST['chilren']== ""){				
				$this->error('儿童数量不能为空,请输入数字，如无请填0');
			}			
			if($_POST['baby']== ""){				
				$this->error('婴儿数量不能为空,请输入数字，如无请填0');
			}
			if($_POST['nklxr']==""){				
				$this->error('联系人姓名不能为空');
			}				
			if($_POST['lxdh']==""){				
				$this->error('联系人手机号不能为空');
			}
			
			$orderDB=D('AsmsOrder');
			//航程信息
			if($_POST['t'] == 1){//单程
				if(empty($_POST['hbh1'])){				
					$this->error('航班号不能为空');
				}				
				if(empty($_POST['cw1'])){				
					$this->error('舱位不能为空');
				}				
				if(empty($_POST['fjjx1'])){				
					$this->error('机型不能为空');
				}	
				if(empty($_POST['from1'])){				
					$this->error('出发城市不能为空');
				}					
				if(empty($_POST['to1'])){				
					$this->error('到达城市不能为空');
				}	
				if(empty($_POST['date1'])){				
					$this->error('出发日期不能为空');
				}	
				if(empty($_POST['time1'])){				
					$this->error('出发时间不能为空');
				}					
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
				$data['lx']=1;//类型-单程
				$data['hc']=$hd_cfcity1.$hd_ddcity1;//航程
				$data['hbh']=$_POST['hbh1'];//航班号
				$data['qfsj']=$_POST['date1'].''.$_POST['time1'];//起飞时间
				$data['cw']=$_POST['cw1'];//舱位
			}
						
			if($_POST['t'] == 2){//往返
				if(empty($_POST['hbh2']) || empty($_POST['hbh3'])){				
					$this->error('航班号不能为空');
				}				
				if(empty($_POST['cw2']) || empty($_POST['cw3'])){				
					$this->error('舱位不能为空');
				}				
				if(empty($_POST['fjjx2']) || empty($_POST['fjjx3'])){				
					$this->error('机型不能为空');
				}	
				if(empty($_POST['from2']) || empty($_POST['from3'])){				
					$this->error('出发城市不能为空');
				}					
				if(empty($_POST['to2']) || empty($_POST['to3'])){				
					$this->error('到达城市不能为空');
				}	
				if(empty($_POST['date2']) || empty($_POST['date3'])){				
					$this->error('出发日期不能为空');
				}	
				if(empty($_POST['time2']) || empty($_POST['time3'])){				
					$this->error('出发时间不能为空');
				}			
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
					'hc'        =>$hd_cfcity2.$hd_ddcity2.'""'.$cityname2.'-'.$ddcityname2,
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
				$data['lx']=2; //类型-往返
				$data['hc']=$hd_cfcity2.$hd_ddcity2.$hd_cfcity3.$hd_ddcity3;//航程
				$data['hbh']=$_POST['hbh2'].'-'.$_POST['hbh3'];//航班号
				$data['qfsj']=$_POST['date2'].''.$_POST['time2'];//起飞时间
				$data['cw']=$_POST['cw2'];//舱位
			}
			
			if($_POST['t'] == 3){//多程			
				$hb=array();
				$hcdata=$_POST['hcdata'];
				foreach($hcdata['hbh'] as $k=>$v){
					if(empty($hcdata['hbh'][$k])){				
						$this->error('航班号不能为空');
					}				
					if(empty($hcdata['cw'][$k])){				
						$this->error('舱位不能为空');
					}				
					if(empty($hcdata['fjjx'][$k])){				
						$this->error('机型不能为空');
					}	
					if(empty($hcdata['from'][$k])){				
						$this->error('出发城市不能为空');
					}					
					if(empty($hcdata['to'][$k])){				
						$this->error('到达城市不能为空');
					}	
					if(empty($hcdata['date'][$k])){				
						$this->error('出发日期不能为空');
					}	
					if(empty($hcdata['time'][$k])){				
						$this->error('出发时间不能为空');
					}					
					
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
					$data['hc'].=$hd_cfcity.$hd_ddcity;//航程
					$data['hbh'].=$hcdata['hbh'][$k].'-';//航班号	
				}
				$hb_info=json_encode($hb);
				$data['lx']=3;//类型-多程	
				$data['qfsj']=$_POST['date3'].''.$_POST['time4'];//起飞时间
				$data['cw']=$hcdata['cw'][4];//舱位				
			}

			//乘机人信息
			$i=0;
			foreach($_POST['info']['cjr_cjrxm'] as $k=>$v){
				if($_POST['info']['cjr_cjrxm'][$k]==""){				
					$this->error('乘机人姓名不能为空');
				}			
				if($_POST['info']['cjr_clkid'][$k]==""){				
					$this->error('证件号码不能为空');
				}
				if($_POST['info']['cjr_zjlx'][$k]==""){				
					$this->error('证件类型不能为空');
				}	
				if($_POST['info']['cjr_xsj'][$k]== ""){				
					$this->error('票价不能为空,请输入数字');
				}	
				$i++;
				$info[$i]=array(
					'cjr_cjrxm'	=>$_POST['info']['cjr_cjrxm'][$k],//姓名
					'cjr_lx'	=>$_POST['info']['cjr_lx'][$k],//乘客类型
					'cjr_zjlx'	=>$_POST['info']['cjr_zjlx'][$k],//证件类型
					'cjr_clkid'	=>$_POST['info']['cjr_clkid'][$k],//证件号
					'cjr_xsj'	=>$_POST['info']['cjr_xsj'][$k],//票价
					'cjr_jsf'	=>$_POST['info']['cjr_jsf'][$k],//机建
					'cjr_tax'	=>$_POST['info']['cjr_tax'][$k]//税费
				);
			}
			$obj=json_encode($info);
			
			//写入数据库			
			$data['xsj']=$_POST['xsj'];//票面价格
			$data['sf']=$_POST['sf'];//税费
			$data['taxa']=$_POST['taxa'];//保险
			$data['jj']=$_POST['jsf'];//机建费
			$data['xjj']=0;//现金券
			$data['ysje']=$_POST['ysje'];//应付金额			
			$data['nklxr']=$_POST['nklxr'];//联系人姓名
			$data['lxdh']=$_POST['lxdh'];//联系人手机
			$data['email']=$_POST['email'];//联系人邮箱					
			$data['rs']=$_POST['athud']+$_POST['chilren']+$_POST['baby'];//人数
			$data['xm']=$_POST['nklxr'];//姓名
			$data['zf_fkf']=$_POST['zf_fkf'];//支付状态
			$data['ddzt']=0;
			$data['xj']=$_POST['xsj']+$_POST['sf']+$_POST['taxa']+$_POST['jsf'];//小计			
			$data['order_logo']=1;
			$data['cpsj']=substr($_POST['cpsj'],5).''.$_POST['cpsj_p'];//出票时间
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
		//
		if($list['order_logo'] == 1){
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
			$cjr=json_decode($list['cjr_info']);			
			$cjr=get_object_vars($cjr);			
			foreach($cjr as $k=>$v){				
				if($v->cjr_lx == 1){$v->lx = '成人';$men++;$this->assign('men',$men);}
				if($v->cjr_lx == 2){$v->lx = '儿童';$chl++;$this->assign('chl',$chl);}
				if($v->cjr_lx == 3){$v->lx = '婴儿';$baby++;$this->assign('baby',$baby);}				
				$cjr_info[]=array(
					'cjr_cjrxm'=>$v->cjr_cjrxm,									  
					'lx'    =>$v->lx,
					'cjr_clkid' =>$v->cjr_clkid,
					'cjr_xsj'   =>$v->cjr_xsj,
					'cjr_jsf'   =>$v->cjr_jsf,
					'cjr_tax'   =>$v->cjr_tax
				);
			}
			//支付状态
			if($list['zf_fkf'] == 0){			
				$list['zf_status']="[待支付]";
			}elseif($list['zf_fkf'] == 1){
				$list['zf_status']="[已付款]";
			}else{
				$list['zf_status']="[已取消]";
			}	
		}else{
			$list=$orderDB->getOrderInfo(I('id'));
			//航班信息
			foreach($list['hd_info'] as $key=>$val){
				$hdinfo[]=array(
					'hc'  =>$val['hd_cityname'].'->'.$val['hd_ddcityname'],
					'cfsj'=>$val['cfsj'],
					'ddsj'=>$val['ddsj'],
					'hbh' =>$val['hbh'],
					'cw'  =>$val['hd_cw'],
					'jx'  =>$val['hd_fjjx']
				);
			}
			//乘客信息
			foreach($list['cjr_info'] as $key=>$val){
				if($val['cjr_cjrlx'] == 1){
					$men++;
					$this->assign('men',$men);
					$cjrlx="成人";
				}
				if($val['cjr_cjrlx'] == 2){
					$chl++;
					$this->assign('chl',$chl);
					$cjrlx="儿童";
				}
				if($val['cjr_cjrlx'] == 3){
					$baby++;
					$this->assign('baby',$baby);
					$cjrlx="婴儿";
				}
                 
				 $cjr_info[]=array(
					'cjr_cjrxm' =>$val['cjr_cjrxm'],			  
					'lx'        =>$cjrlx,	
					'cjr_clkid' =>$val['cjr_clkid'],
					'cjr_xsj'   =>$val['cjr_xsj'],
					'cjr_jsf'   =>$val['cjr_jsf'],
					'cjr_tax'   =>$val['cjr_tax']					
				 );	
			}
			//支付状态
			if($list['zf_fkf'] == 0){			
				$list['zf_status']="[待支付]";
			}elseif($list['zf_fkf'] == 1){
				$list['zf_status']="[已付款]";
			}else{
				$list['zf_status']="[已取消]";
			}			
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
			$where['ddbh']=$_POST['ddbh'];
			$res=$orderDB->where($where)->setField('ddzt',8);
			if($res){
				$this->success('取消成功');
			}
		}else{
			$this->display();
		}
	}

}