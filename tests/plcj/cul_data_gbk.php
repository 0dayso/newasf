<?php//评论	$url="http://www.17u.net/plane/judgeall.asp";	$page=isset($_GET['pages'])?$_GET['pages']:1;	$num=file_get_contents('./num.txt');	if($num){		$page=$num;		var_dump($page);	}	$page++;	file_put_contents('./num.txt',$page);	if($page==1000){		echo $page;		exit;	}//	print_r(parse_url($url));		//echo $page['query'];		//print_r( $_GET['url']);		$data=getinfo($url."?page=$page");		$preg="/<ul class=\"ccli_670\">[\s\S]*<span style=\"float:left; padding-right:5px\">([\s\S]*)于[\s\S]*<span class=\"sp01\">([\s\S]*)<\/span>[\s\S]*<span class=\"li01\">([\s\S]*)<\/span>[\s\S]*<\/ul>/Ui";	preg_match_all($preg,$data,$info);		//print_r($info[3]);exit;		$conn=mysql_connect("localhost","root","77169"); 	mysql_select_db("curl_data");	mysql_query("set names utf8");	$val='';	$pl=function($v){			$rand=mt_rand(1,100);					if(strpos($v,'好评')!=false){ //好评				return $hp=$rand>50?5:4;			}elseif(strpos($v,'中评')!=false){ //中评				return $hp=$rand>50?4:3;			}elseif(strpos($v,'差评')!=false){ //差评				return $hp=$rand>80?3:2;			}else{				return $zp=$rand<30?3:4;			}		};			if(!empty($info[3])){			foreach($info[2] as $k=>$v){			$v=strip_tags($v);			trim($v);			$content=$info[3][$k];			$name=$info[1][$k];			$val.="('$name',".$pl($v).",".$pl($v).",".$pl($v).",".$pl($v).",'$content'),";		}		$val=trim($val,',');						$sql="insert into pl_temp_20131231 (`name`,`total`,`server`,`speed`,`price`,`contents`)values $val;";  		echo $sql;	//	exit;		mysql_query($sql) or die(mysql_error());				echo "<script type='text/javascript'>location.href='http://localhost/newasf/tests/plcj/cul_data_gbk.php?pages=$page&url=$url?page=$page'</script>"; 		}				function getinfo($url){	//获取内容$headers = array("Connection: keep-alive","Cache-Control: max-age=0","Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5","User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US)","Avail-Dictionary: xdC_A6dv","Accept-Language: zh-CN,zh;q=0.8","Accept-Charset: utf-8;GBK,q=0.7,*;q=0.3"		);		$cookie_file=dirname(__FILE__).'/cookie.txt';		$ch = curl_init();		curl_setopt ($ch,CURLOPT_URL, $url);		curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1); 		curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,60); 	//	curl_setopt($ch,CURLOPT_POST,1);	//	curl_setopt($ch,CURLOPT_POSTFIELDS,$curlPost);		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);		curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file);		curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file);			//引入cookie 文件		$newfile = curl_exec($ch);		return $newfile;}?>