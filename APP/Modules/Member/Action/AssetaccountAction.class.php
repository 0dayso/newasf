<?php

// 资产账户
class AssetaccountAction extends IniAction {	
	function index(){
		$this->title="会员中心";
		$this->display();
    }
	
	//-----------------------------------------------------------------------------------
	//现金卷
	function cashcoupon(){
		$userInfo=$this->userInfo;
		$points=D('points');
		$exchange=D('MallExchange');
		
		//余额
		$wh['member_id']=$userInfo['id'];
		$wh['type2']=2;
		$this->overage=$points->where($wh)->sum('points');
		if($this->overage<=0 && $this->overage==''){
			$this->overage=0;
		}
		
		//发放记录		
		$pagesize=12; //每页显示的记录
		$count= $points->where($wh)->count();//总记录数		
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($wh)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
		foreach($list as $k=>$v){
		  $time=date("Y",$list[$k]['create_time']);
		  $time=$time+1;
		  $list[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		  
		}
						
		foreach($list as $key=>$value){//向数组加入元素time
			$list[$key]['time']=$time;
		}	
			
		//---------------------------------------
		//使用记录
		$AsmsOrder=D('AsmsOrder');  
		$w['hyid']=$userInfo['asms']['hyid'];	
		$num=12;		
		$count1= $AsmsOrder->where($w)->count();//总记录数		
		$totlePage1=ceil($count1/$num);//总页数
			if($totlePage1==0){$totlePage1=1;}
		$page1=I('p');//定义当前页
			if($page1<=1){$page1=1;}
		$cash=$AsmsOrder->field('ddbh,xjj,hc,ddzt,lx,zf_fkf,update_time')->where($w)->limit(($page1-1)*$num.','.$num)->select();		
		foreach($cash as $key=>$val){
				if(is_array($val)){
                	$val = $AsmsOrder->format($val);
				}	
				$val['hc_n']=implode('-',D("City")->getCity(str_split($val['hc'],3)));
				$cash[$key]=$val;
			$cash[$key]['update_time']=date("Y-m-d",$cash[$key]['update_time']);//时间戳格式化
		} 
				
		if ($this->isAjax()){
			if(I('t')=='detail'){			
				$data['totlePage']=$totlePage;
				$data['page']=$page;
				$data['status']=1;
				$data['list']=$list;	
				$this->ajaxReturn($data); 			
			}
			if(I('t')=='exchange'){
				$data['totlePage']=$totlePage1;
				$data['page']=$page1;
				$data['status']=1;
				$data['exchange']=$cash;
				$this->ajaxReturn($data);
			}
		}
		
		//模板赋值
		$this->assign('totlePage',$totlePage);
		$this->assign('page',$page);
		$this->assign('list',$list);
		
		$this->assign('totlePage1',$totlePage1);
		$this->assign('page1',$page1);
		$this->assign('exchange',$cash);

		$this->title="现金卷";
		$this->display();
    }	
	
	//-----------------------------------------------------------------------------
	//我的积分
	function integral(){
		$userInfo=$this->userInfo;	//获取会员信息
		$points=D('Points');    //实例化points 
		$exchange=D('mall_exchange');
		
		//---------------------------------------
		//积分明细				
		$wh['member_id']=$userInfo['id'];	
		$wh['type2']=0;	
		$this->totle=$points->where($wh)->sum('points');//积分总数		
		
		$pagesize=12; //每页显示的记录
		$count= $points->where($wh)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($wh)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
		foreach($list as $k=>$v){
		  $time=date("Y",$list[$k]['create_time']);
		  $time=$time+1;
		  $list[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		  
		}
						
		foreach($list as $key=>$value){//向数组加入元素time
			$list[$key]['time']=$time;
		}	
				
		//---------------------------------------
		//兑换记录
		$count1= $exchange->where($wh)->count();//总记录数
		$totlePage1=ceil($count1/$pagesize);//总页数
			if($totlePage1==0){$totlePage1=1;}
		$page1=I('p');//定义当前页
			if($page1<=1){$page1=1;}
		$exchange=$exchange->where($wh)->field('info,status,create_time,order_num')->limit(($page1-1)*$pagesize.','.$pagesize)->select();	
		
		foreach($exchange as $k=>$v){
			$exchange[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		  
			$obj = json_decode($v['info'],true);//json转化为数组
			
			//图片
			$mall=D('Mall');
			$whereMallId['id']=$obj['mall_id'];
			$mall=$mall->field('img')->where($whereMallId)->find();
			$exchange[$k]['img']=$mall['img'];
			
			//订单状态
			if($v['status'] == 0){
				$exchange[$k]['status2']='未发货';
			}elseif($v['status'] == 1){
				$exchange[$k]['status2']='已发货';
			}else{
				$exchange[$k]['status2']='已完成';
			}
			
			$exchange[$k]['mall_id']=$obj['mall_id'];
			$exchange[$k]['num']=$obj['num'];
			$exchange[$k]['jifen']=$obj['jifen'];
			$exchange[$k]['title']=$obj['title'];
			
			//删除多余的元素
			unset($exchange[$k]['info']);
			unset($exchange[$k]['status']);
		}
		
		 //ajax
		if ($this->isAjax()){
			if(I('t')=='detail'){			
				$data['totlePage']=$totlePage;
				$data['page']=$page;
				$data['status']=1;
				$data['list']=$list;	
				$this->ajaxReturn($data); 			
			}
			if(I('t')=='exchange'){
				$data['totlePage']=$totlePage1;
				$data['page']=$page1;
				$data['status']=1;
				$data['exchange']=$exchange;
				$this->ajaxReturn($data);
			}
		}
		
		//模板赋值
		$this->assign('totlePage',$totlePage);
		$this->assign('page',$page);
		$this->assign('list',$list);
		
		$this->assign('totlePage1',$totlePage1);
		$this->assign('page1',$page1);
		$this->assign('exchange',$exchange);
		
		$this->title="我的积分";
		$this->display();		
    }	
	
    
	//-----------------------------------------------------------------------------
	//我的爱钻
	function aizuan(){		
		$userInfo=$this->userInfo;	//获取会员信息
		$points=D('Points');    //实例化points 
		$exchange=D('mall_exchange');	
		
		//爱钻总数				
		$wh['member_id']=$userInfo['id'];	
		$wh['type2']=1;	
		$this->totle=$points->where($wh)->sum('points');
		
		//---------------------------------------
		//爱钻明细
		$pagesize=12; //每页显示的记录
		$count= $points->where($wh)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($wh)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
		foreach($list as $k=>$v){
		  $time=date("Y",$list[$k]['create_time']);
		  $time=$time+1;
		  $list[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		  
		}
						
		foreach($list as $key=>$value){//向数组加入元素time
			$list[$key]['time']=$time;
		}	
				
		//---------------------------------------
		//兑换记录
		$count1= $exchange->where($wh)->count();//总记录数
		$totlePage1=ceil($count1/$pagesize);//总页数
			if($totlePage1==0){$totlePage1=1;}
		$page1=I('p');//定义当前页
			if($page1<=1){$page1=1;}
		$exchange=$exchange->where($wh)->field('info,status,create_time,order_num')->limit(($page1-1)*$pagesize.','.$pagesize)->select();	
		
		foreach($exchange as $k=>$v){
			$exchange[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		  
			$obj = json_decode($v['info'],true);//json转化为数组
			
			//图片
			$mall=D('Mall');
			$whereMallId['id']=$obj['mall_id'];
			$mall=$mall->field('img')->where($whereMallId)->find();
			$exchange[$k]['img']=$mall['img'];
			
			//订单状态
			if($v['status'] == 0){
				$exchange[$k]['status2']='未发货';
			}elseif($v['status'] == 1){
				$exchange[$k]['status2']='已发货';
			}else{
				$exchange[$k]['status2']='已完成';
			}
			
			$exchange[$k]['mall_id']=$obj['mall_id'];
			$exchange[$k]['num']=$obj['num'];
			$exchange[$k]['jifen']=$obj['jifen'];
			$exchange[$k]['title']=$obj['title'];
			
			//删除多余的元素
			unset($exchange[$k]['info']);
			unset($exchange[$k]['status']);
		}
		
		 //ajax
		if ($this->isAjax()){
			if(I('t')=='detail'){			
				$data['totlePage']=$totlePage;
				$data['page']=$page;
				$data['status']=1;
				$data['list']=$list;	
				$this->ajaxReturn($data); 			
			}
			if(I('t')=='exchange'){
				$data['totlePage']=$totlePage1;
				$data['page']=$page1;
				$data['status']=1;
				$data['exchange']=$exchange;
				$this->ajaxReturn($data);
			}
		}

		//模板赋值
		$this->assign('totlePage',$totlePage);
		$this->assign('page',$page);
		$this->assign('list',$list);
		
		$this->assign('totlePage1',$totlePage1);
		$this->assign('page1',$page1);
		$this->assign('exchange',$exchange);
		
		$this->title="我的爱钻";
		$this->display();
    }
	
	//-----------------------------------------------------------------------------
	//我的礼品
	function gift(){		
		$userInfo=$this->userInfo;	
		//print_r($userInfo);
		$mall=D('Mall');		
		$cart=D('MallCart');
		$collect=D('MallCollect');
		$exchange=D('MallExchange');
		 
		if(I('act') !== ''){//收藏产品、加入购物车
		 	if(I('act')== 'add'){
				if(isset($_GET['num']) && I('num')<1){
                    $this->error("添加失败,商品数量不能小于1");                    
				}
				$cart->addCart()?$this->success("添加成功",U('/Member/Assetaccount/gift?status=cart')):$this->error("添加失败");			
			}elseif(I('act')== 'sc'){
				if(isset($_GET['num']) && I('num')<1){
                    $this->error("收藏失败,商品数量不能小于1");                    
				}
			    $collect->addCollect()?$this->success("添加成功",U('/Member/Assetaccount/gift')):$this->error("添加失败");			
			}
		}		 
		 
		//-------------------
		//我的收藏	
		$wh['member_id']=$userInfo['id'];		
		$pagesize=8; 
		
			//所有礼品收藏		
		$count= $collect->where($wh)->count();//总记录数        
		$allPage=ceil($count/$pagesize);//总页数
			if($allPage==0){$allPage=1;}
		$nowpage=I('p');//定义当前页
			if($nowpage<=1){$nowpage=1;}
		$offset=($nowpage-1)*$pagesize; 		
		$mycollect=$collect->where($wh)->order('create_time desc')->relation(true)->limit($offset,$pagesize)->select();//所有礼品
		
		foreach($mycollect as $k=>$v){
			if($v['type']==0){
				$mycollect[$k]['jifen']= "积fun：<em>".$v['jifen']."</em>";
			}else{
				$mycollect[$k]['jifen']="爱钻：<b>x".$v['jifen']."</b>";
			}
			
		}		
			//积分收藏
		$wh['type']=0;
		$count= $collect->where($wh)->count();//总记录数	
		$jfPage=ceil($count/$pagesize);//总页数
			if($jfPage==0){$jfPage=1;}
		$nowpagejf=I('p');//定义当前页
			if($nowpagejf<=1){$nowpagejf=1;}
		$offset=($nowpagejf-1)*$pagesize;
		$jifensc=$collect->where($wh)->order('create_time desc')->limit($offset,$pagesize)->relation(true)->select();//积分收藏
		
			//爱钻收藏
		$wh['type']=1;
		$count= $collect->where($wh)->count();//总记录数        
		$azPage=ceil($count/$pagesize);//总页数
			if($azPage==0){$azPage=1;}
		$nowpageaz=I('p');//定义当前页			
			if($nowpageaz<=1){$nowpageaz=1;}
		$offset=($nowpageaz-1)*$pagesize;
		$aizuansc=$collect->where($wh)->order('create_time desc')->limit($offset,$pagesize)->relation(true)->select();//爱钻收藏			

		if ($this->isAjax()){
			if(I('t') == 'allgift'){//所有礼品
				$data['totlePage']=$allPage;
				$data['page']=$nowpage;
				$data['status']=1;
				$data['list']=$mycollect;	
				$this->ajaxReturn($data);				
			}
			if(I('t') == 'jfgift'){//积分礼品
				$data['totlePage']=$jfPage;
				$data['page']=$nowpagejf;
				$data['status']=1;
				$data['list']=$jifensc;	
				$this->ajaxReturn($data);			
			}	
			if(I('t') == 'azgift'){//爱钻礼品
				$data['totlePage']=$azPage;
				$data['page']=$nowpageaz;
				$data['status']=1;
				$data['list']=$aizuansc;	
				$this->ajaxReturn($data);			
			}
		}
			//删除收藏的礼品
		if($this->isAjax()){
			if(I('act') == 'delcollect'){
				$delcollect_id['id']=I('id');
				$success=$collect->where($delcollect_id)->delete();
				if($success){
					$this->success();
				}
			}
		}
				
		//模板赋值
		$this->assign('allPage',$allPage);//所有礼品
		$this->assign('nowpage',$nowpage);//所有礼品		
		$this->assign('mycollect',$mycollect);//所有礼品	
		
		$this->assign('jfPage',$jfPage);//积分收藏
		$this->assign('nowpagejf',$nowpagejf);//积分收藏
		$this->assign('jifensc',$jifensc);//积分收藏
		
		$this->assign('azPage',$azPage);//爱钻收藏
		$this->assign('nowpageaz',$nowpageaz);//爱钻收藏
		$this->assign('aizuansc',$aizuansc);//爱钻收藏
			
		 //-------------------		
		//我的购物车		
		$WhereCart['member_id']=$userInfo['id'];
		$WhereCart['type']=0;//积分
		$this->CartJifen=$cart->where($WhereCart)->order('create_time desc')->relation(true)->select();		
		foreach($this->CartJifen as $v){//合计积分
			$this->totlejf += $v['num']*$v['jifen'];
			if($this->totlejf == 0){$this->totlejf;}
		}

		$WhereCart['type']=1;//爱钻
		$this->CartAizuan=$cart->where($WhereCart)->order('create_time desc')->relation(true)->select();
		foreach($this->CartAizuan as $v){//合计爱钻
			$this->totleaz += $v['num']*$v['jifen'];
			if($this->totlejf == 0){$this->totlejf;}
		}
			
		   //ajax删除、清除、移动处理		   
		if($this->isAjax()){
			$id=I('id');//该id不是商品的id，而是数据自增的id
			$type=I('act');	
			$wheres['member_id']=$userInfo['id'];			
			if(is_array($id)){
				$wheres['id']=array('in',$id);		
			}else{
				$wheres['id']=$id;
			} 
						
			$ids=$cart->field('mall_id')->where($wheres)->select();						
			foreach($ids as $k=>$v){
				$mall_ids[]=$v['mall_id'];//商品的id
			}
						
			//删除
			if($type == 'del'){
				$wheres['member_id']=$userInfo['id'];
				$rs=$cart->where($wheres)->delete();
				if($rs){
					$this->success();				
				}
				$this->error();				
			}			
			//移入收藏夹
			if($type == 'move'){
				$id['member_id']=$userInfo['id'];				
				$sc=$collect->where($id)->select();//查询户收藏夹里面的内容
				if(empty($sc)){//如果该用户的收藏夹为空
					$id['mall_id']=array('in',$mall_ids);
					$cart2=$cart->where($wheres)->select();
					foreach($cart2 as $k=>$v){
						$condition['mall_id']=$v['mall_id'];
						$condition['member_id']=$v['member_id'];
						$condition['type']=$v['type'];
						$condition['create_time']=time();
						$collect->add($condition);		
					}
					$cart->where($wheres)->delete();
					$this->success();
				}else{//如果该用户的收藏夹不为空
					foreach($sc as $key=>$value){
						$mallids[]=$value['mall_id'];
					}
					$newids= array_diff($mall_ids,$mallids);//取两个数组的差集
					if(empty($newids)){//如果差集为空，说明已经收藏过这些产品了							
						$cart->where($wheres)->delete();
						$this->success();
					}else{//差值不为空
						$idsarr['mall_id']=array('in',$newids);
						$idsarr['member_id']=$userInfo['id'];	
						
						$cart2=$cart->where($idsarr)->select();
						
						print_r($cart2);
						
						foreach($cart2 as $k=>$v){
							$condition['mall_id']=$v['mall_id'];
							$condition['member_id']=$v['member_id'];
							$condition['type']=$v['type'];
							$condition['create_time']=time();								
							$collect->add($condition);								
						}
						$cart->where($wheres)->delete();
						$this->success();
					}
				}
				$this->error();				
			}
			//清楚失效产品
			if($type == 'invalid'){	
				$idsarr['mall_id']=array('in',$mall_ids);
				$idsarr['member_id']=$userInfo['id'];			
				$clean=$mall->field('id,status')->where($idsarr)->select();
				foreach($clean as $k=>$v){
					if($v['status'] == 0){
						$ids[]=$v['id'];
					}				
				}			
				$idsarr['mall_id']=array('in',$ids);
				$res=$cart->where($idsarr)->delete();
				if($res){
					$data['gid']=$ids;
					$data['status']=1;					
					$this->AjaxReturn($data);
				}else{
					$this->error();
				}
			}	
		} 
		
		
		 //-------------------
		 //已兑换礼品		
		$where4['member_id']=$userInfo['id'];
		$where4['type2']=0;//积分
		$exchange1=$exchange->field('info,status,create_time,order_num,type2')->where($where4)->select();		
		
		foreach($exchange1 as $key=>$value){	
			$obj[$key]=json_decode($value['info'],true);			
			$whereMallId['id']=$obj[$key]['mall_id'];
			$mallimg=$mall->field('img')->where($whereMallId)->find();
			$obj[$key]['img']=$mallimg['img'];
			$obj[$key]['order_num']=$exchange1[$key]['order_num'];
			$obj[$key]['create_time']=$exchange1[$key]['create_time'];
			$obj[$key]['status']=$exchange1[$key]['status'];
		}
		
		$where4['type2']=1;//爱钻
		$exchange2=$exchange->field('info,status,create_time,order_num,type2')->where($where4)->select();
		foreach($exchange2 as $key=>$value){	
			$obj2[$key]=json_decode($value['info'],true);			
			$whereMallId['id']=$obj[$key]['mall_id'];
			$mallimg=$mall->field('img')->where($whereMallId)->find();
			$obj2[$key]['img']=$mallimg['img'];
			$obj2[$key]['order_num']=$exchange2[$key]['order_num'];
			$obj2[$key]['create_time']=$exchange2[$key]['create_time'];
			$obj2[$key]['status']=$exchange2[$key]['status'];
		}
		
		$this->assign('exchange1',$obj);
		$this->assign('exchange2',$obj2);
		
		$this->title="我的礼品";
		$this->display();
    }
		
	
	//-----------------------------------------------------------------------------
	//兑换确认
	function exchange(){
		$mall=D('Mall');		
		$cart=D('MallCart');
		$collect=D('MallCollect');
		$exchange=D('MallExchange');
		$userInfo=$this->userInfo;
		$address=D('DeliverAddress');
				
		foreach($_POST['id'] as $key=>$val){
			$cartid['id']=$val;
			$mallid=$cart->field('mall_id')->where($cartid)->find();
			$goods[$key]['mall_id']=$mallid['mall_id'];
			foreach($_POST['num'] as $k=>$v){
				if($k == $val){
					$goods[$key]['num']=$v[0];	
				}					
			}
		}
		foreach($goods as $k=>$v){
			$con['id']=$v['mall_id'];					
			$data[$k]=$mall->field('id,type,jifen,title,img')->where($con)->find();
		}
		foreach($data as $key=>$val){
			foreach($goods as $k=>$v){
				$data[$k]['mall_id']=$v['mall_id'];
				$data[$k]['num']=$v['num'];
				$data[$k]['sumjifen']=$v['num']*$data[$k]['jifen'];				
			}
			$totlejifen += $data[$key]['sumjifen'];
		}
		//print_r($data);
		$this->assign('totlejifen',$totlejifen);
		$this->assign('data',$data);
		
		//地址
		$where['member_id']=$userInfo['id'];
		//$where['member_id']=1103;//测试用
		$where['is_default']=1;		
		$addressInfo=$address->where($where)->find();
		$province_city = D('DeliverAddress')->xml_address($addressInfo['province'],$addressInfo['city']);		
		$this->assign('province_city',$province_city);	
		$this->assign('addressInfo',$addressInfo);
		
		$this->title="兑换确认";
		$this->display();
    }
	
	function exchange2(){
		if($_POST){
			$address=D('DeliverAddress');
			$exchange=D('MallExchange');
			
			$wh['member_id']=$this->userInfo['id'];
			$wh['id_default']=1;
			$address=$address->where($wh)->order('update_time')->find();
			$address=preg_replace("/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '$1'))", json_encode($address));
			
			foreach($_POST as $key=>$val){
				foreach($val as $k=>$v){
					$arr[$k][$key]=$v;
				}		
			}
			foreach($arr as $k => $v){
				unset($v['type2']);
				//$json[$k]=json_encode($v);
				$json[$k]=preg_replace("/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '$1'))", json_encode($v));
			}
			
			foreach($json as $k=>$v){
				foreach($arr as $key => $val){
					$data[$k]['member_id']=$this->userInfo['id'];
					$data[$k]['order_num']='M'.$this->userInfo['id'].'D'.date('Ymd',time()).'N'.rand(0,1000);//M会员ID，D日期，N随机数
					$data[$k]['type2']=$val['type2'];
					$data[$k]['info']=$v;
					$data[$k]['address']=$address;
					$data[$k]['status']=0;
					$data[$k]['create_time']=time();				
				}
			}
			foreach($data as $v){
				$exchange->create($v);
				$res=$exchange->add();
			}
			if($res){
				$this->success('恭喜你，下单成功！',U('/Member/Assetaccount/gift?status=dh'));
			}
		}else{
			$this->error('该页面不存在');
		}
    }
	
	
}