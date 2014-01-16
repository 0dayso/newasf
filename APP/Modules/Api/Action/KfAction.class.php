<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-8-5
 * Time: 下午4:47
 * To change this template use File | Settings | File Templates.
 */
class KfAction extends IniAction{
    function index(){

    }
    function leftKf(){
        if($this->userinfo['user_id']){
            $content = $this->fetch();
            header("Content-type: text/javascript; charset=utf-8");
            echo $content;
        }
    }
}