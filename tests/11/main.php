<?php
require_once 'conn.php';

function  get_gb_to_utf8($value){
	$value_1   = $value;
	$value_2   =   @iconv( "gb2312", "utf-8//IGNORE",$value_1);
	$value_3   =   @iconv( "utf-8", "gb2312//IGNORE",$value_2);
	if(strlen($value_1)==strlen($value_3)){
		return   $value_2;
	}else{
		return   $value_1;
	}
}


$contents = file_get_contents('data/all.txt');
$array = explode("\n", trim($contents));
$array = array_unique($array);
array_multisort($array);

ob_start();
include 'Snoopy.class.php';
header("Content-Type:text/html;charset=utf-8");

foreach ($array as $key=>$val){
	$val =  trim($val);
	if($val==false){
		continue;
	}
	
	$snoopy = new Snoopy;
	$snoopy->agent = "Mozilla/5.0 (Windows NT 6.1; rv:21.0) Gecko/20100101 Firefox/21.0";
	$snoopy->referer = "http://sale.cs-air.com";
	$snoopy->rawheaders["COOKIE"]= 'dns=telecom; JSESSIONID=0000x07gh7KKSaqP589jjhWk0Hi:-1';
	$snoopy->fetch("http://saled.csair.com/ECS/data/order/retrievePassengerInfo.xsql?orderno={$val}&orderflag=C");
	preg_match('/\<CONTACT\>(.*?)\<\/CONTACT\>/is',get_gb_to_utf8($snoopy->results),$match);
	if(isset($match[1])){
		$data = explode('|', $match[1]);
		echo $match[1].'<br/>';
		echo $sql = "insert into user2 values('','$val','$data[0]','$data[1]','$data[2]','$data[3]','first')";
		mysql_query($sql);
		echo '<hr/>';
		ob_flush();
		flush();
	}else{
		echo "<font color='red'>".$val.'失败</font><br/>';
		ob_flush();
		flush();
		file_put_contents('data/error.txt', $val."--发生错误\n",FILE_APPEND);
	}
	
	
}

//csnlianggr	ab654321
