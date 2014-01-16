<?php
// 首页控制器
class PagesAction extends IniAction {
    public function index(){	
		if(I('pages')){
			
		}
		$this->display();
		
    }
	public function guandao(){
        $this->title="关岛活动";
		$this->display();
		
    }
	public function HaiHang(){
        $this->title="全新北京直飞芝加哥全新北京直飞芝加哥";
		$this->display();
		
    }
	
	
}