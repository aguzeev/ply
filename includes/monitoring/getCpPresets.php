<?php

require_once('../init.php');

$ACCESSED_MODULE = 10; // !!! before including cerber;
$_LOGGING = false;
include('../cerber.php');

$_DEBUG = false;

if ($_DEBUG) echo $_SESSION['v2_user_id'] . "<br>";

$query = "SELECT * FROM `control_pannel_presets` WHERE `user_id` = '" . $_SESSION['v2_user_id'] . "' ORDER BY `order`";
if ($_DEBUG) echo $query;
$result = mysql_query($query, $connection_stat) or die(mysql_error());

$presets = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$presets[] = array(
		//"user_id" => $row["user_id"],
		"order" => $row["order"],
		"machine" => $row["machine"],
		"variant" => $row["variant"],
		"period" => $row["period"],
		"database_id" => $row['id']
	);
}
mysql_free_result($result);



echo json_encode($presets);

?>