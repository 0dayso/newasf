<?php


	$conn=mysql_connect("localhost","root","77169"); 
	mysql_select_db("csv_db");
	mysql_query("set names utf8");

//	$sql="select *,(select `to` from line_temp order by rand() limit 1)  from pl_temp limit 20";

//	$sql="select a.*,l.* from pl_temp ,(select `from`,`to` from line_temp order by rand()) l limit 20";
	
	$sql="select * from `table 4`";
	
	$rs=mysql_query($sql) or die(mysql_error());
	$password=md5('123456');
	$create_time=time();
	while($row=mysql_fetch_assoc($rs)){
	//	$sql="update asf_user set `username`='b{$row[username]}' status=0 where `username`='$row[username]'";
	//	mysql_query($sql) or die(mysql_error());
	print_r($row);
//	$val=trim($val,',');
	if($row['password']){
		$sql="insert into asf_user(username,password,name,public_mobile,telephone,qq,avatar,company_id,department_id,position_id,create_time)values('$row[username]','$password','$row[name]','$row[public_mobile]','$row[telephone]','$row[qq]','/avatar/$row[avatar].jpg','$row[company_id]','$row[department_id]','$row[position_id]','$create_time')"; 
	}else{
		$sql="insert into asf_user(username,password,name,public_mobile,telephone,qq,avatar,company_id,department_id,position_id,create_time)values('$row[username]','$password','0','$row[public_mobile]','$row[telephone]','','','$row[company_id]','$row[department_id]','$row[position_id]','$create_time')"; 
	}
	mysql_query($sql) or die(mysql_error());
	
	echo $sql;
//	echo $sql;
//	exit();
	}
	mysql_query($sql) or die(mysql_error());
	
	//$v=str_replace('机票','',$v);


?>