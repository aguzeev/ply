<?php

require_once('includes/init.php');
$ACCESSED_MODULE = 10; // everyone has access
$_LOGGING = false;
include('includes/cerber.php');



$query = "SELECT `timestamp` FROM `synclog` ORDER BY `timestamp` DESC LIMIT 1";
$result = mysql_query($query, $connection_hardware) or die(mysql_error());

$lastSync = mysql_result($result, 0);

mysql_free_result($result);

echo "<p class='lastSync'>Последняя синхронизация с заводом: " . date("d.m.Y H:i", strtotime($lastSync)) . "</p>";

?>