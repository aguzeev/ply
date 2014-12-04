<?php

require_once('init.php');

$ACCESSED_MODULE = 3;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['appId'])) $appId = intval(mysql_real_escape_string($_GET['appId']));

$query = "SELECT * FROM `appointments` WHERE `id` = " . $appId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$appInfo = json_encode( array( 
	$row['appointment'],
	array( $row['department'], getDeptName($row['department']) ),
	unserialize( $row['allowedOperationsDefault'] )
) );

print_r($appInfo);


?>