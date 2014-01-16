<?php
/*********************** 导出成为html格式 **************************/
require_once 'conn.php';

$sql = "select * from user";
ob_start();
$re=mysql_query($sql);
echo '<table>';
file_put_contents('first.html','<table>',FILE_APPEND);
while($row = mysql_fetch_assoc($re)){
	echo $str = "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['tel']}</td><td>{$row['phone']}</td><td>{$row['email']}</td><tr>\n";
	ob_flush();
	flush();
	file_put_contents('first.html',$str,FILE_APPEND);
}
echo '</table>';
file_put_contents('first.html','</table>',FILE_APPEND);

exit;
*/