<?php
header("Pragma: no-cache");
require 'common/common.php';
require 'member.class.php';

$member=new member;
$member->insert_db();

?>