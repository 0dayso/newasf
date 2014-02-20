<?php

/* 	帮助中心 首页/help
	订票帮助	/help/book
	会员帮助	/help/members
	支付方式	/help/pay */

class HelpAction extends IniAction {

    public function index(){
        $this->title="帮助中心";
		$this->display('members');
    }

    function members(){
        $this->title="会员服务 - 帮助中心";
        $this->display();
    }
	function assetaccount(){
        $this->title="资产账户 - 帮助中心";
        $this->display();
    }
    function pay(){
        $this->title="支付方式 - 帮助中心";
        $this->display();
    }
	
}