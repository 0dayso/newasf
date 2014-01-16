<?php
$time=strtotime("2013-2-1");
echo date("Y-m-d H:i:s",$time+3600*24)."\n";
echo cal_days_in_month(CAL_GREGORIAN,3,2010);
echo date('t', $time);