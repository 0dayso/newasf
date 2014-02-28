<?php
//  Author: yinpengfei <me@yin.cc>

//定义项目名称和路径
define('APP_NAME', 'APP');
define('APP_PATH', './APP/');
// 开启调试模式
define('APP_DEBUG',TRUE);
define("WEB_ROOT", dirname(__FILE__) . "/");
define("COOKIE_FILE", WEB_ROOT.APP_NAME."/cookie_#");

//非框架 初始化程序
if(file_exists('./init.php')){
    require( "./init.php");
}
// 加载框架入口文件
require( "./ThinkPHP/ThinkPHP.php");

?>