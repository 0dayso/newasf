<?php
header("Pragma: no-cache");
require 'common/common.php';
require 'member.class.php';

$user=new member;
$rs=$user->get_member_info('1311121527288425');

print_r($rs);
echo $user->getError();
?>