<?php
$conn=mysql_connect("localhost","root","77169"); 
	mysql_select_db("CSV_DB");
	mysql_query("set names utf8");
	
	$sql="select zhou from tabledd group by zhou";
	
	$rs=mysql_query($sql) or die(mysql_error());
	
	while($row=mysql_fetch_assoc($rs)){ 
		print_r($row);
	}
	exit;
	$sql="select * from tabledd where zhou like '美洲'";
	
	
	
	$rs=mysql_query($sql) or die(mysql_error());
	
	while($row=mysql_fetch_assoc($rs)){  
	
	$time=explode('-',$row['time']);
	$row['time']=$time[1];
	
	
		if($row['fr']=='广州'){
			$array['gz'][]=$row;
		}
		if($row['fr']=='北京'){
			$array['bj'][]=$row;
		}
		if($row['fr']=='上海'){
			$array['sh'][]=$row;
		}
		if($row['fr']=='香港'){
			$array['xg'][]=$row;
		}
	}
	print_r($array);
	
	cache_write('file', 'mzarr', $array); 
	//写入 
function cache_write($name, $var, $values) { 
    $cachefile = $name.'.php'; 
    $cachetext = "<?php\r\n". 
    "". 
    '$'.$var.'='.arrayeval($values). 
    "\r\n?>"; 
    if(!swritefile($cachefile, $cachetext)) { 
        exit("File: $cachefile write error."); 
    } 
} 
//数组转换成字串 
function arrayeval($array, $level = 0) { 
    $space = ''; 
    for($i = 0; $i <= $level; $i++) { 
        $space .= "\t"; 
    } 
    $evaluate = "Array\n$space(\n"; 
    $comma = $space; 
    foreach($array as $key => $val) { 
        $key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key; 
        $val = !is_array($val) && (!preg_match("/^\-?\d+$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val; 
        if(is_array($val)) { 
            $evaluate .= "$comma$key => ".arrayeval($val, $level + 1); 
        } else { 
            $evaluate .= "$comma$key => $val"; 
        } 
        $comma = ",\n$space"; 
    } 
    $evaluate .= "\n$space)"; 
    return $evaluate; 
} 
//写入文件 
function swritefile($filename, $writetext, $openmod='w') { 
    if(@$fp = fopen($filename, $openmod)) { 
        flock($fp, 2); 
        fwrite($fp, $writetext); 
        fclose($fp); 
        return true; 
    } else { 
        runlog('error', "File: $filename write error."); 
        return false; 
    } 
}
	
	

?>