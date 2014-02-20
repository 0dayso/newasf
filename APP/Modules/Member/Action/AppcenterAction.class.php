<?php
// 应用中心
class AppcenterAction extends IniAction {
    //好友fun享
	function fun(){
		$this->title="好友fun享";
        //分享链接
        $this->funUrl=U('/Member/Invite/reg','','','',true)."?id=".$this->userInfo['id'];
        //发送邮件
        if(I('act')=="sendMail" && I('mail')){
            if(C('VERIFY_CODE') && I('verify_code','','md5') != session('verify'))
                $this->error('验证码错误！');
            $content="我注册了爱尚飞国际机票网，感觉很专业，服务很好，价格也很实惠，强烈推荐你注册啊！$this->funUrl";
            $title=$this->userInfo['name']."邀请您注册爱尚飞国际机票网";
            if(sendMail(I('mail'),$title,$content)){
                $this->success('发送成功');
            }
            $this->error('发送失败');
        }
        //推荐商品
        $list=D('Mall')->where('type=1')->order('update_time desc')->limit('12')->select();
        $this->fun_list=$list;
		$this->display();
    }
	
	//预订评价
	function evaluate(){
		$evaluate=D('Evaluat');		
        $order=D("AsmsOrder");      
	    $wh1['member_id']=getUid();
	    $wh2['hyid']=ASMSUID;	 
		
		//待评价订
			//1、确定哪些订单还没有评价
		$a=$evaluate->field('order_id')->where($wh1)->select();
		$b=$order->field('ddbh')->where($wh2)->select();	
		foreach($b as $k=>$v){
			foreach($a as $key=>$val){
				if($val['order_id'] !== $v['ddbh']){
					$arr_id[$k]=$v['ddbh'];
				}
			}
		}
			//2、分页处理
		$size=15; //每页显示的记录
		$totlecount=count($arr_id);//总记录数        
		$allPage=ceil($totlecount/$size);//总页数		
			if($allPage==0){$allPage=1;}
		$nowpage=I('p');//定义当前页
			if($nowpage<=1){$nowpage=1;}	
		$lenth=($nowpage-1)*$size; 
		
			//3、查询未评价订单信息		
		$orderid['ddbh']=array('in',$arr_id);	
		$info=$order->field("ddbh,hc,xj,xjj,update_time,cpsj")->where($orderid)->limit($lenth,$size)->select();
		
			//4、
		foreach($info as $k=>$v){
			$v['hc']=str_split($v['hc'],3);
			$v['hc_a'] = D("City")->getCity( $v['hc']);
			$v['hc_n']=implode('-',$v['hc_a']);//航班行程
				
			$coun=count($v['hc'])-1;//往返或单程
			if($v['hc'][0] == $v['hc'][$coun]){
				$v['style']="往返";
			}else{
				$v['style']="单程";
			}	
			
			if($v['xjj'] == 0){//现金券
				$v['xjj']='未使用';
			}
			
			if(empty($v['cpsj'])){//订单状态和出票时间
				$v['cpsj']="--";
				$v['status']="未出票";
			}elseif($v['cpsj'] == "null"){
				$v['cpsj']="--";
				$v['status']="未出票";
			}else{
				$v['status']="已出票";
			}			
			$info[$k]=$v;
					
		}
		
		$this->assign('info',$info);
		$this->assign('nowpage',$nowpage);
		$this->assign('allPage',$allPage);		
		
		//预定评价记录
		$pagesize=5; //每页显示的记录
		$count= $evaluate->where($wh1)->count();//总记录数        
		$totlePage=ceil($count/$pagesize);//总页数		
			if($totlePage==0){$totlePage=1;}
		$page=I('p');//定义当前页
			if($page<=1){$page=1;}	
		$offset=($page-1)*$pagesize; 		
		$list=$evaluate->where($wh1)->field('id,from_city,to_city,total,server,speed,price,contents,status,create_time,order_id')->order('create_time DESC')->limit($offset,$pagesize)->select();
		
		foreach($list as $k=>$v){
			if($v['total'] == 5){//总体评价
				$list[$k]['total1']= '<a class="exc5">&nbsp;</a>'; 
			}elseif($v['total'] == 4){
				$list[$k]['total1']= '<a class="exc4">&nbsp;</a>'; 
			}elseif($v['total'] == 3){
				$list[$k]['total1']= '<a class="exc3">&nbsp;</a>'; 
			}elseif($v['total'] == 2){
				$list[$k]['total1']= '<a class="exc2">&nbsp;</a>'; 
			}elseif($v['total'] == 1){
				$list[$k]['total1']= '<a class="exc1">&nbsp;</a>'; 
			}else{
				$list[$k]['total1']= '<a class="exc0">&nbsp;</a>'; 
			}
			
			if($v['server'] == 5){//服务
				$list[$k]['server1']= '（很好）'; 
			}elseif($v['server'] == 4){
				$list[$k]['server1']= '（好）'; 
			}elseif($v['server'] == 3){
				$list[$k]['server1']= '(一般)'; 
			}elseif($v['server'] == 2){
				$list[$k]['server1']= '(不好)'; 
			}elseif($v['server'] == 1){
				$list[$k]['server1']= '(很差)'; 
			}else{
				$list[$k]['server1']= '(非常差)'; 
			}
			
			if($v['speed'] == 5){//出票速度
				$list[$k]['speed1']= '（非常快）'; 
			}elseif($v['speed'] == 4){
				$list[$k]['speed1']= '（比较快）'; 
			}elseif($v['speed'] == 3){
				$list[$k]['speed1']= '(一般)'; 
			}elseif($v['speed'] == 2){
				$list[$k]['speed1']= '(有点慢)'; 
			}elseif($v['speed'] == 1){
				$list[$k]['speed1']= '(很慢)'; 
			}else{
				$list[$k]['speed1']= '(非常慢)'; 
			}
						
			if($v['price'] == 5){//价格
				$list[$k]['price1']= '（非常实惠）'; 
			}elseif($v['price'] == 4){
				$list[$k]['price1']= '（很实惠）'; 
			}elseif($v['price'] == 3){
				$list[$k]['price1']= '(实惠)'; 
			}elseif($v['price'] == 2){
				$list[$k]['price1']= '(一般)'; 
			}elseif($v['price'] == 1){
				$list[$k]['price1']= '(有点贵)'; 
			}else{
				$list[$k]['price1']= '(很贵)'; 
			}
			//时间格式转换
			$list[$k]['create_time1']=date("Y-m-d H:i:s",$v['create_time']);
			
			//返回order确认是单程还是往返
			$wheres['id']=$v['order_id'];
			$routeinfo=$order->field('route')->where($wheres)->find();
			$json_to_arr=json_decode($routeinfo['route']);//航班形成信息转换成数组
			if(count($json_to_arr) == 2){
				$list[$k]['trip_way']="往返";
			}else{
				$list[$k]['trip_way']="单程";
			}
		}
		
		//ajax提交分页处理
		if ($this->isAjax()){
			if(I('t')=='order'){
				$data['totlePage']=$allPage;
				$data['page']=$nowpage;
				$data['status']=1;
				$data['list']=$info;	
				$this->ajaxReturn($data);
			}
			if(I('t')=='record'){
				$data['totlePage']=$totlePage;
				$data['page']=$page;
				$data['status']=1;
				$data['list']=$list;	
				$this->ajaxReturn($data);
			}
		}		

		//模板赋值
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->assign('totlePage',$totlePage);
		
		$this->title="预订评价";
		$this->display();
    }
	
}