<?php

require_once('init.php');

$ACCESSED_MODULE = 4;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['operationId'])) $operationId = intval(mysql_real_escape_string($_GET['operationId']));

$query = "SELECT `operation` FROM `operations` WHERE `id` = " . $operationId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$userInfo = json_encode(array($row['operation']));

print_r($userInfo);


?>