<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 13-8-1
 * Time: 下午2:03
 * To change this template use File | Settings | File Templates.
 */
function selectMobileNum(){   //查余额
    $flag = 0;
    //要post的数据
    $argv = array(
        'sn'=>'SDK-GKG-010-00236', //替换成您自己的序列号
        'pwd'=>'283358',//替换成您自己的密码
    );

    $params="";//构造要post的字符串
    foreach ($argv as $key=>$value) {
        if ($flag!=0) {
            $params .= "&";
            $flag = 1;
        }
        $params.= $key."="; $params.= urlencode($value);
        $flag = 1;
    }
    $length = strlen($params);
    //创建socket连接
    $fp = fsockopen("sdk2.zucp.net",80,$errno,$errstr,10) or exit($errstr."--->".$errno);
    //构造post请求的头
    $header = "POST /webservice.asmx/GetBalance HTTP/1.1\r\n";
    $header .= "Host:sdk2.zucp.net\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: ".$length."\r\n";
    $header .= "Connection: Close\r\n\r\n";  //添加post的字符串

    $header .= $params."\r\n"; //发送post的数据
    fputs($fp,$header);
    $inheader = 1;
    while (!feof($fp)) {
        $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
        if ($inheader && ($line == "\n" || $line == "\r\n")) {
            $inheader = 0;
        }
        if ($inheader == 0) {
            // echo $line;
        }
    }
    $line=str_replace("<string xmlns=\"http://tempuri.org/\">","",$line);
    $line=str_replace("</string>","",$line);
    $result=explode("-",$line);
    if(count($result)>1)
        echo '发送失败返回值为:'.$line;
    else
        echo '剩余:'.$line."条";
}