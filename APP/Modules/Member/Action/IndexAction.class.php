<?php
// 首页控制器
class IndexAction extends IniAction {

	function index(){
		$userInfo=$this->userInfo;
		$points=D('Points');		
		
		//账户安全等级
		$mail=D(CheckEmail);
		$where['member_id']=$userInfo['id'];
		$mail=$mail->where($where)->select();
		if(!empty($mail)){
			foreach($mail as $key=>$val){
				$userInfo['check_email']=$val['is_check'];
			}
		}else{
			$userInfo['check_email']=0;
		}
		
		//现金券总数
		$wh['member_id']=$userInfo['id'];
		$wh['type2']=2;
		$this->overage=$points->where($wh)->sum('points');
		if($this->overage<=0 && $this->overage==''){
			$this->overage=0;
		}
		
		//积分总数		
		$wh['type2']=0;	
		$this->totlejf=$points->where($wh)->sum('points');
		if($this->totlejf<=0 && $this->totlejf==''){
			$this->totlejf=0;
		}
		//爱钻总数
		$wh['type2']=1;	
		$this->totleac=$points->where($wh)->sum('points');
		if($this->totleac<=0 && $this->totleac==''){
			$this->totleac=0;
		}
		
		//会员等级
		$order=D('Order');
		$arr=$order->where($where)->select();
		if(!empty($arr)){
			$totalorder=count($arr);
		}else{
			$totalorder=0;
		}		
		$totalmoney=$order->where($where)->sum('cunstomer_payment');
		
		if($totalorder<=2 || $totalmoney<=8000){
			$this->dj="普通会员";
		}
		if(($totalorder>2 && $totalorder<=5) || ($totalmoney>8000 && $totalmoney<=20000)){
			$this->dj="铜牌会员";
		}		
		if(($totalorder>5 && $totalorder<=12) || ($totalmoney>20000 && $totalmoney<=50000)){
			$this->dj="银牌会员";
		}		
		if($totalorder>12 || $totalmoney>50000){
			$this->dj="白金会员";
		}
		
		$this->title="会员中心";
		$this->display();
        }

	
	
}