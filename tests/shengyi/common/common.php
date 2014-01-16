<?php
if(function_exists('ini_set')){
	ini_set('max_execution_time', '0');
}


//用来存放cookie的文件
define('COOKIE_FILE', dirname(__FILE__)."/cookie.txt");
$cookie_file= COOKIE_FILE;

require dirname(__FILE__).'/global.func.php';
//require(dirname(__FILE__)."/../nusoap/lib/nusoap.php");

connect();
/*
$sql="select * from member";
$rs=mysql_query($sql) or die(mysql_errno());;

while($row=mysql_fetch_array($rs)){
    print_r($row);
}*/

//==========================
//	登陆操作
//==========================
function check_login(){
    $fields = "bh=6000&method=checkLogin&kl=77169&call=&callnum=&randtime"; //登陆post 数据
    $index=curl_post(C('ASMS_HOST').'/asms/','',COOKIE_FILE,0);
    if(!$index){
     return  curl_login(C('ASMS_HOST').'/sysmanage/login.shtml',$fields,COOKIE_FILE);
    }
}
check_login();

?>