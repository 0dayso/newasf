<?php
$url="http://flights.aishangfei.net/AutoComplete/s.htm?k=".$_REQUEST['k'];
$html=file_get_contents($url);  
echo $html;
?>