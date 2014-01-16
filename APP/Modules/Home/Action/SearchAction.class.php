<?php
//搜索
class SearchAction extends IniAction {
	function index(){
		
	
	}
	
	//订单查询
	function booking(){
		$booking=D('Booking');
		$order_id=I('order_id');
		if(empty($order_id)){
			exit('不能为空');
		}
		$booking->relation(true)->where("order_id='$order_id'")->find();
		
		$this->order=$booking;
		
		print_r($booking);
		
	//	$this->display();	
	}


}

?>