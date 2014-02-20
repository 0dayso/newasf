<?php
// 特惠活动控制器
class SpecialofferAction extends IniAction {
	
    public function index(){
        $this->title="特惠活动";
        $this->display();
    }
	
	//主题活动
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
	
	//品牌特惠
	public function virgin_atlantic_sales(){
        $this->title="圣诞元旦双节狂欢";
        $this->display();
    }
	public function flysas_sales(){
        $this->title="北欧航空--京沪惠游欧洲";
        $this->display();
    }
	public function cathaypacific_sales(){
        $this->title="国泰航空-出发美澳";
        $this->display();
    }
	
}