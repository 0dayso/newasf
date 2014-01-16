<?php
// 个人设置
class SettingAction extends IniAction {

    //我的信息
	function myinfo(){
		$this->title="我的信息";
		$this->display();
    }
	
	//设置头像
	function seticon(){
		$this->title="设置头像";
		$this->display();
    }
	
	//安全中心
	function securitycenter(){
		$this->title="安全中心";
		$this->display();
    }
	//常用旅客
	function passengerlist(){
		$this->title="常用旅客";
		$this->display();
    }
	//常用旅客
	function passenger(){
		$this->title="常用旅客";
		$this->display();
    }
	//修改密码
	function password(){
		$this->title="修改密码";
		$this->display();
    }
	
	//更换手机号
	function replacephone(){
		$this->title="更换手机号";
		$this->display();
    }
	//更换新手机号
	function replacephone2(){
		$this->title="更换手机号";
		$this->display();
    }
	//手机账号申诉
	function appeal(){
		$this->title="手机账号申诉";
		$this->display();
    }
	//邮箱验证
	function mailboxverify(){
		$this->title="邮箱验证";
		$this->display();
    }
	//更换邮箱
	function replacemailbox(){
		$this->title="更换邮箱";
		$this->display();
    }
	//更换邮箱
	function replacemailbox2(){
		$this->title="更换邮箱";
		$this->display();
    }
	//邮寄地址
	function address(){
		$this->title="邮寄地址";
		$this->display();
    }
	//邮寄地址列表
	function addresslist(){
		$this->title="邮寄地址列表";
		$this->display();
    }
	//站内信
	function message(){
		$this->title="站内信";
		$this->display();
    }
	
	
	
}