<?php


	$conn=mysql_connect("localhost","root","gzyiqifei"); 
	mysql_select_db("newasf04");
	mysql_query("set names utf8");

//	$sql="select *,(select `to` from line_temp order by rand() limit 1)  from pl_temp limit 20";

//	$sql="select a.*,l.* from pl_temp ,(select `from`,`to` from line_temp order by rand()) l limit 20";
	
//	$page=$_GET['p']?$_GET['p']:0;
//	$num=2000;
//	$start=$num*$page;
//	$sql="select * from asf_evaluat3";
//	echo $sql;
//	$rs=mysql_query($sql) or die(mysql_error());
//	
	
	$sql2="select `id`,username,name,entry_time from asf_user3 where  status=1 and name!='' and avatar!='' and view=1 and qq!=''";
	$rs2=mysql_query($sql2) or die(mysql_error());

	while($row2=mysql_fetch_assoc($rs2)){
	//mysql> select floor((unix_timestamp()-(select entry_time from asf_user where id=3))*rand());
		$sql="update asf_evaluat,asf_user set asf_evaluat.create_time=asf_user.entry_time+floor((unix_timestamp()-asf_user.entry_time)*rand()) where asf_user.id=$row2[id] and asf_evaluat.user_id=$row2[id]";		
		echo $sql."<br>";
		mysql_query($sql) or die(mysql_error());
	}
	exit;
	$val='';
	while($row=mysql_fetch_assoc($rs)){
		while($row2=mysql_fetch_assoc($rs)){
		
	//	mysql_query($sql) or die(mysql_error());
		}
		
	}

	
	
	//$v=str_replace('机票','',$v);


?>