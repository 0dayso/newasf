<?php
// 首页控制器
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
		$where['member_id']=$userInfo['id'];
		$where['type2']=2;		
		$pagesize=12; //每页显示的记录
		$count= $points->where($where)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($where)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
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
			if($val['xjj'] == 0){//判断是否有现金券消费				
				if(is_array($val)){
                	$val = $AsmsOrder->format($val);
				}	
				$val['hc_n']=implode('-',D("City")->getCity(str_split($val['hc'],3)));				
				$cash[$key]=$val;					
			}
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
		
		//print_r($cash);
		
		//使用记录
		$this->title="现金卷";
		$this->display();
    }	
	
	//-----------------------------------------------------------------------------
	//我的积分
	function integral(){
		$userInfo=$this->userInfo;	//获取会员信息
		$points=D('Points');    //实例化points 
				
		//---------------------------------------
		//积分明细				
		$wh['member_id']=$userInfo['id'];	
		$wh['type2']=0;	
		$this->totle=$points->where($wh)->sum('points');//积分总数
		
		$where['member_id']=$userInfo['id'];	
		$where['type']=0;		
		$pagesize=12; //每页显示的记录
		$count= $points->where($where)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($where)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
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
		$exchange=D('mall_exchange');
		$where['member_id']=$userInfo['id'];			 
		$num=10;
		$count1= $exchange->where($where)->count();//总记录数
		$totlePage1=ceil($count1/$num);//总页数
			if($totlePage1==0){$totlePage1=1;}
		$page1=I('p');//定义当前页
			if($page1<=1){$page1=1;}
		$exchange=$exchange->where($where)->field('info,status,create_time,order_num')->limit(($page1-1)*$num.','.$num)->select();			
		foreach($exchange as $k=>$v){
		  $exchange[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		
		}		
		foreach($exchange as $key=>$value){	
			$obj=json_decode($value['info'],true);			
			$arrs[]=$obj[0]['mall_id'];
			for($i=$key;$i<count($arrs);$i++){
				$mall=D('Mall');
				$whereMallId['id']=$arrs[$i];
				$mall=$mall->field('img')->where($whereMallId)->find();
				$img=$mall['img'];
			}			
			foreach($obj as $k=>$v) {
				if($value['status'] == 0){
					$value['status']='未发货';
				}else if($value['status'] == 1){
					$value['status']='已发货';
				}else{
					$value['status']='已完成';
				}
				$v['status']=$value['status'];
				$v['create_time']=$value['create_time'];
				$v['order_num']=$value['order_num'];
				$v['img']=$img;
				$arr2[]=$v;
					
			}					
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
				$data['exchange']=$arr2;
				$this->ajaxReturn($data);
			}
		}
		
		//模板赋值
		$this->assign('totlePage',$totlePage);
		$this->assign('page',$page);
		$this->assign('list',$list);
		
		$this->assign('totlePage1',$totlePage1);
		$this->assign('page1',$page1);
		$this->assign('exchange',$arr2);
		
		$this->title="我的积分";
		$this->display();		
    }	
	
    
	//-----------------------------------------------------------------------------
	//我的爱钻
	function aizuan(){		
		$userInfo=$this->userInfo;	//获取会员信息
		$points=D('Points');    //实例化points 
				
		//爱钻总数				
		$wh['member_id']=$userInfo['id'];	
		$wh['type2']=1;	
		$this->totle=$points->where($wh)->sum('points');
		
		//---------------------------------------
		//爱钻明细
		$where['member_id']=$userInfo['id'];	
		$where['type']=1;		
		$pagesize=12; //每页显示的记录
		$count= $points->where($where)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$points->where($where)->order('create_time DESC')->limit($offset,$pagesize)->select();
		
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
		$exchange=D('mall_exchange');
		$where['member_id']=$userInfo['id'];		 
		$num=10;
		$count1= $exchange->where($where)->count();//总记录数
		$totlePage1=ceil($count1/$num);//总页数
			if($totlePage1==0){$totlePage1=1;}
		$page1=I('p');//定义当前页
			if($page1<=1){$page1=1;}
		$exchange=$exchange->where($where)->field('info,status,create_time,order_num')->limit(($page1-1)*$num.','.$num)->select();			
		foreach($exchange as $k=>$v){
		  $exchange[$k]['create_time'] = date("Y-m-d",$list[$k]['create_time']);//时间戳格式化		
		}		
		foreach($exchange as $key=>$value){	
			$obj=json_decode($value['info'],true);			
			$arrs[]=$obj[0]['mall_id'];
			for($i=$key;$i<count($arrs);$i++){
				$mall=D('Mall');
				$whereMallId['id']=$arrs[$i];
				$mall=$mall->field('img')->where($whereMallId)->find();
				$img=$mall['img'];
			}			
			foreach($obj as $k=>$v) {
				if($value['status'] == 0){
					$value['status']='未发货';
				}else if($value['status'] == 1){
					$value['status']='已发货';
				}else{
					$value['status']='已完成';
				}
				$v['status']=$value['status'];
				$v['create_time']=$value['create_time'];
				$v['order_num']=$value['order_num'];
				$v['img']=$img;
				$arr2[]=$v;
			}					
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
				$data['exchange']=$arr2;
				$this->ajaxReturn($data);
			}
		}

		//模板赋值
		$this->assign('totlePage',$totlePage);
		$this->assign('page',$page);
		$this->assign('list',$list);
		
		$this->assign('totlePage1',$totlePage1);
		$this->assign('page1',$page1);
		$this->assign('exchange',$arr2);
		
		$this->title="我的爱钻";
		$this->display();
    }
	
	//-----------------------------------------------------------------------------
	//我的礼品
	function gift(){		
		$userInfo=$this->userInfo;	
		$mall=D('Mall');		
		$cart=D('MallCart');
		$collect=D('MallCollect');
		$exchange=D('MallExchange');
		 
		if(I('act') !== ''){
		 	if(I('act')== 'add'){
				if(isset($_GET['num']) && I('num')<1){
                    $this->error("添加失败,商品数量不能小于1");                    
				}
				$cart->addCart()?$this->success("添加成功",U('/Member/Assetaccount/gift')):$this->error("添加失败");			
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
print_R($ids);				
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
		$where1['member_id']=$userInfo['id'];
		$where1['type2']=0;//积分
		$exchange1=$exchange->field('info,status,create_time,order_num')->where($where1)->select();
		foreach($exchange1 as $key=>$value){	
			$obj=json_decode($value['info'],true);			
			$arrs[]=$obj[0]['mall_id'];
			for($i=$key;$i<count($arrs);$i++){
				$mall=D('Mall');
				$whereMallId['id']=$arrs[$i];
				$mall=$mall->field('img')->where($whereMallId)->find();
				$img=$mall['img'];
			}			
			foreach($obj as $k=>$v) {
				$v['status']=$value['status'];
				$v['create_time']=$value['create_time'];
				$v['order_num']=$value['order_num'];
				$v['img']=$img;
				$arr1[]=$v;
			}					
		}		
		$where1['type2']=1;//爱钻
		$exchange2=$exchange->field('info,status,create_time,order_num')->where($where1)->select();
		foreach($exchange2 as $key=>$value){	
			$obj=json_decode($value['info'],true);			
			$arrs[]=$obj[0]['mall_id'];
			for($i=$key;$i<count($arrs);$i++){
				$mall=D('Mall');
				$whereMallId['id']=$arrs[$i];
				$mall=$mall->field('img')->where($whereMallId)->find();
				$img=$mall['img'];
			}			
			foreach($obj as $k=>$v) {
				$v['status']=$value['status'];
				$v['create_time']=$value['create_time'];
				$v['order_num']=$value['order_num'];
				$v['img']=$img;
				$arr2[]=$v;
			}					
		}		
		$this->assign('exchange1',$arr1);
		$this->assign('exchange2',$arr2);
		
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
		
		if($_POST){
			if(empty($_POST)){
				$this->error('请选择至少一个要兑换的商品');
			}elseif($_POST['num'] == 0){
				$this->error('商品数量不能为0');
			}else{
				$id=I('id');//该id不是商品的id，而是购物车自增的id			
				$num=I('num');
							
				$cartid['member_id']=$userInfo['id'];
				
				if(is_array($id)){//如果数量有变，对数据库进行数量更新
					$cartid['id']=array('in',$id);	
					$n=$cart->field('num')->where($cartid)->select();
					foreach($n as $k=>$v){
						if($num[$k] !== $v[$k]['num']){
							$cart->where($cartid)->setField('num',$num);
						}
					}
				}else{
					$cartid['id']=$id;
					$n=$cart->where($cartid)->getField('num');
					if($num !== $n){
						$cart->where($cartid)->setField('num',$num);
					}				
				}
				
				$ids=$cart->field('mall_id')->where($cartid)->select();						
				foreach($ids as $k=>$v){
					$mall_ids[]=$v['mall_id'];//商品的id
				}			
				
				$Where['id']=array('in',$mall_ids);			
				$this->mall_info=$cart->where($Where)->order('create_time desc')->relation(true)->select();	
										
					
				foreach($this->mall_info as $k => $v){//计算总积分和爱钻
					if($v['type'] == 0){
						$this->totlejifen += $v['jifen'];
					}else{
						$this->totleaizuan += $v['jifen'];
					}
				}
			}			
		}
		
		//地址
		//$where['member_id']=$userInfo['id'];
		$where['member_id']=1103;//测试用
		$where['is_default']=1;		
		$addressInfo=$address->where($where)->find();
		$province_city = D('DeliverAddress')->xml_address($addressInfo['province'],$addressInfo['city']);		
		$this->assign('province_city',$province_city);	
		$this->assign('addressInfo',$addressInfo);
		
		$this->title="兑换确认";
		$this->display();
    }
	
	function address(){
		$this->title="地址确认";
		$this->display();
    }
	
	
}