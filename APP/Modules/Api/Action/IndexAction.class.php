<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-7-23
 * Time: 下午5:32
 * To change this template use File | Settings | File Templates.
 */
class IndexAction extends Action{
        function index(){
         echo "api";
         echo    session_id();
        }

}