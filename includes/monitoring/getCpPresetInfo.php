<?php

require_once('../init.php');
$ACCESSED_MODULE = 10;
$_LOGGING = false;
include('../cerber.php');

if ( isset($_GET['database_id']) ) $database_id = intval(mysql_real_escape_string($_GET['database_id']));
else { echo json_encode(array("result"=>"error","text"=>"Не указан ID виджета")); die(); };


$_SESSION['v2_user_id'];

$query = "SELECT `order`, `machine`, `variant`, `period` FROM `control_pannel_presets` WHERE `id` = " . $database_id . " AND `user_id` = " . $_SESSION['v2_user_id'] . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error("Can't get widget info"));

$row = mysql_fetch_array($result, MYSQL_ASSOC);
$presetInfo = array(
	"order" => $row["order"],
	"machine" => $row["machine"],
	"variant" => $row["variant"],
	"period" => $row["period"],
);
mysql_free_result($result);

echo json_encode($presetInfo);

?>