<?php
// 国际机票（查询预订）
class FlightAction extends Action {
	
	//首页
    public function index(){
		$this->title="国际机票查询预订";
        $this->display();
    }
	
	//列表
    public function flightlist(){
		$this->title="国际机票查询列表";
        $this->display();
    }
	
	//订单
    public function bookinginfo(){
		$this->title="航班订单";
        $this->display();
    }
	
}