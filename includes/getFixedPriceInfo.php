<?php

require_once('init.php');

$ACCESSED_MODULE = 6;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['fixedpriceId'])) $fixedpriceId = intval(mysql_real_escape_string($_GET['fixedpriceId']));



$query = "SELECT * FROM `fixedprice` WHERE `id` = " . $fixedpriceId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$dept = getDeptByApp($row['app_id']);
$fixedpriceInfo = json_encode( array(
	"id" => $row['id'],
	"sector_id" => $dept['id'],
	"sector_title" => $dept['title'],
	"app_id" => $row['app_id'],
	"price" => $row['price'],
	"date_start" => date("d.m.Y", strtotime($row['date_start']))
) );

mysql_free_result($result);
print_r($fixedpriceInfo);


?>