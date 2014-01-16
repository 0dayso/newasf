<?php
// 特惠活动控制器
class SpecialofferAction extends IniAction {
	
    public function index(){
        $this->title="特惠活动";
        $this->display();
    }
	
	public function sdyd2013(){
        $this->title="圣诞元旦双节狂欢";
        $this->display();
    }
	
	public function dijialaixi(){
        $this->title='一线城市往返欧洲低价来袭';
        $this->display();
    }
	
	public function chaoditejia(){
        $this->title='二线城市出发超低特价大放送';
        $this->display();
    }
	
	public function thtjbjbm(){
        $this->title='特惠推荐-北京往返北美';
        $this->display();
    }
	
}