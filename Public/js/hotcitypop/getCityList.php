<?php
$url="http://autocomplete.ufeifan.com/AutoComplete/s.htm?lan=cn&k=".$_REQUEST['k'];
$html=file_get_contents($url);  
echo $html;
?>