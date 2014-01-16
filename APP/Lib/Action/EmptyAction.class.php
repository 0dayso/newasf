<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pengfei
 * Date: 13-7-7
 * Time: 下午9:27
 * To change this template use File | Settings | File Templates.
 */
class EmptyAction extends Action{
    function _empty(){
        header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码
        @header("Status: 404 Not Found");
        $this->title="404 错误";
        $this->display("Public:404");exit;
    }
}