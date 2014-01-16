<?php
// 关于我们控制器
class AboutAction extends Action {
	
	//我们的服务
    public function service(){
        $this->display();
    }
	
	//常见问题
	public function faq(){
        $this->display();
    }
	
	
}