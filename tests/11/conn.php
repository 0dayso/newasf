<?php
if(function_exists('ini_set')){
	ini_set('max_execution_time', '0');
}
mysql_connect('localhost','root','77169');
mysql_select_db('curl_data');
mysql_query('set names utf8');