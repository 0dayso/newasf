<?php 
//
require_once 'conn.php';
header("Content-Type:text/html;charset=utf-8");

require_once 'Classes/PHPExcel.php';
require_once 'Classes/PHPExcel/IOFactory.php';
require_once 'Classes/PHPExcel/Reader/Excel5.php';
require_once 'Classes/PHPExcel/Reader/Excel2007.php';
$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format

global $objReader;
tree("./xls","xls");//读取的目录

function tree($directory,$file_type='xls'){ 
global $objReader;
	$mydir=dir($directory); 
	while($file=$mydir->read()){ 
		if($file=="." or $file==".."){continue;}
		
		if((is_dir("$directory/$file"))){ 
			tree("$directory/$file",$file_type); 
		}else{
			if(substr($file,(strrpos($file,'.')-strlen($file)))!=".$file_type"){
				continue;
			}
			$files="$directory/$file";
			$basename=basename($files,".$file_type");
			echo "读取".$files."  - ";
			$sql="select count(*) as count from nh_xls where file_name='$basename'";
			$rs=mysql_query($sql);
			$rs=mysql_fetch_assoc($rs);
			if($rs['count']){
				$error= "文件名：".$basename." 在数据库中已存在<br/>";
				echo $error;
				file_put_contents('data/error.txt', $files."--发生错误 --$error-- \n",FILE_APPEND);
				continue;
			}
			$filename=trim($files);
			$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow(); // 取得总行数
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数
			$k = 0;
			//循环读取excel文件,读取一条,插入一条
			$sql_val="";
			for($j=2;$j<=$highestRow;$j++){
			$aa="";
				for($l=1;$l<=abc($highestColumn);$l++){
					$a = $objPHPExcel->getActiveSheet()->getCell(abc($l).$j)->getValue();//获取A列的值
					if(empty($a)){	
						$not_empty = $abc[abc($l)]?$abc[abc($l)]:abc($l).$j;
						$a=$objPHPExcel->getActiveSheet()->getCell($not_empty)->getValue();
					}else{
						$abc[abc($l)]=abc($l).$j;
					}
					$aa.="'$a',";	
				}
				
				$sql_val .= "('',$aa '$basename'),";
			}
			$sql_val=trim($sql_val,',');
			

			$sql = "INSERT INTO asf_message_sys_tpl VALUES $sql_val";
			
			echo $sql;
			$rs=mysql_query($sql) or die(mysql_error());
			if($rs){
				echo "文件".$files." 信息已写入数据库<br>";
			}else{
				$error=mysql_error();
				file_put_contents('data/error.txt', $files."--发生错误 --$error-- \n",FILE_APPEND);
			}
		
		}
	} 
	$mydir->close(); 
} 

function abc($a){
	$abc=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	foreach($abc as $k=>$v){		
		if(is_numeric($a)){
			if(($a-1)==$k){
				return $v;
			}
		}else{
			if($a==$v){
				return $k+1;
			}
		}
	}
}

?>