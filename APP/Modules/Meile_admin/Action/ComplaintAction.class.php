<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-10-24
 * Time: 下午2:22
 * To change this template use File | Settings | File Templates.
 */

class ComplaintAction extends CommonAction{
    function _before_index(){
        $this->relation=true;
    }

   function read(){
        $this->vo=D('Complaint')->getInfo();
        $this->display();
    }


}