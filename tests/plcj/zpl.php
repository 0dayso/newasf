<?php


	$conn=mysql_connect("localhost","root","77169"); 
	mysql_select_db("curl_data");
	mysql_query("set names utf8");

//	$sql="select *,(select `to` from line_temp order by rand() limit 1)  from pl_temp limit 20";

//	$sql="select a.*,l.* from pl_temp ,(select `from`,`to` from line_temp order by rand()) l limit 20";
	
	$page=$_GET['p']?$_GET['p']:0;
	$num=3000;
	$start=$num*$page;
	$sql="select * from pl_temp_20131231 limit $start,$num";
	echo $sql;
	
	$rs=mysql_query($sql) or die(mysql_error());
	
	$sql2="select `from`,from_iata,`to`,to_iata from asf_line where `class`=1 and from_iata!='' and to_iata!='' order by rand() limit 1";
	
	$val='';
	while($row=mysql_fetch_assoc($rs)){
		$rs2=mysql_query($sql2) or die(mysql_error());
		while($row2=mysql_fetch_assoc($rs2)){  
			$from=$row2['from'];
			$to=$row2['to'];
			$to_iata=$row2['to_iata'];
			$from_iata=$row2['from_iata'];
		}
		//$sql2="select `id`,username,name,entry_time from asf_user3 where  status=1 and name!='' and avatar!='' and view=1 and qq!=''";
		$sql3="select `id`,username,name from asf_user where status=1 and public_mobile!=''  and avatar!='' and view=1 order by rand() limit 1";
		$rs3=mysql_query($sql3) or die(mysql_error());
		while($row3=mysql_fetch_assoc($rs3)){  
			$user=$row3['id'];
		}
		$row['contents']=str_replace('同程网','爱尚飞网',$row['contents']);
		$row['contents']=str_replace('同程','爱尚飞',$row['contents']);
		
	
		$time=rand(strtotime("2013-12-01"),strtotime("2014-02-10"));
		$val.="('$row[name]',$user,'$from','$from_iata','$to','$to_iata','$row[total]','$row[server]','$row[speed]','$row[price]','$row[contents]',$time),";
	}
	$val=trim($val,',');
	$sql="insert into zpl_20131231 (`name`,`user_id`,`from_city`,from_iata,`to_city`,to_iata,`total`,`server`,`speed`,`price`,`contents`,`create_time`)values $val"; 
//	echo $sql;
//	exit();
	mysql_query($sql) or die(mysql_error());
	
	//$v=str_replace('机票','',$v);


?>