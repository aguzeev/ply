<?php

require_once('init.php');

$ACCESSED_MODULE = 5;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['rateId'])) $rateId = intval(mysql_real_escape_string($_GET['rateId']));



$query = "SELECT * FROM `rates` WHERE `id` = " . $rateId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$rateInfo = json_encode( array(
	"start_date" => date("d.m.Y", strtotime($row['start_date'])),
	"op" => $row['operation_id'],
	"app" => $row['app_id'],
	"rate_1" => $row['rate_1'],
	"rate_2" => $row['rate_2'],
	"cond_2" => $row['cond_2'],
	"rate_3" => $row['rate_3'],
	"cond_3" => $row['cond_3'],
) );

print_r($rateInfo);


?>