<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * To change this template use File | Settings | File Templates.
 */

class MemberAction extends IniAction{
	
	//资产账户
    function cashcoupon(){
        $this->title='现金券';
        $this->display();
    }
    function index(){
        $this->display();
    }
}