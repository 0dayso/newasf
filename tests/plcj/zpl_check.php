<?php


	$conn=mysql_connect("localhost","root","77169"); 
	mysql_select_db("curl_data");
	mysql_query("set names utf8");

	
	$city='北京,上海,广州,深圳,天津,南京,武汉,沈阳,西安,成都,重庆,杭州,青岛,大连,宁波,济南,哈尔滨,长春,厦门,郑州,长沙,福州,乌鲁木齐,昆明,兰州,无锡,南昌,贵阳,南宁,合肥,太原,石家庄,呼和浩特,唐山,烟台,泉州,包头,佛山,东莞,机场,酒店';
	
	$city=explode(',',$city);
	
	var_dump($city);
	

	foreach($city as $v){
	//echo 1555;
		$sql="delete from zpl_20131231 where contents like '%$v%'";
		$rs=mysql_query($sql) or die(mysql_error());	
		echo $rs;
	}

//	$sql="insert into zpl (`name`,`user_id`,`from_city`,`to_city`,`total`,`server`,`speed`,`price`,`contents`,`create_time`)values $val"; 
//	echo $sql;
//	exit();
//	mysql_query($sql) or die(mysql_error());
	
	//$v=str_replace('机票','',$v);


?>