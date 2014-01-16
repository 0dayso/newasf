<?php
class ActivityAction extends IniAction{
     public function index(){
        $this->title="活动频道";
        $this->newList=D('News')->getList(33,4,1);
         $this->newlist=$this->newList['list'];
		$this->display();
    }
	
	public function theme(){
        $this->title="主题活动";
		$this->display();
    }
	
	public function trademark(){
        $this->title="品牌专区";
		$this->display();
    }
	
	public function sale(){
        $this->title="特价汇";
		$this->display();
    }
	
	public function activity(){
        $this->title="活动首页";
		$this->display();
    }
	
	public function rule(){
        $this->title="活动规则";
		$this->display();
    }
}