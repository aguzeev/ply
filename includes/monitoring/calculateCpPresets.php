<?php

require_once('../init.php');

$ACCESSED_MODULE = 0; // everyone has access
$_LOGGING = false;
include('../cerber.php');



$query = "SELECT * FROM `control_pannel_presets`";
$result = mysql_query($query, $connection_stat) or die(mysql_error());

$presets = array();
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$presets[] = array(
		"user_id" => $row["user_id"],
		"machine" => $row["machine"],
		"field" => $row["field"],
		"period" => $row["period"]
	);
}
mysql_free_result($result);

echo json_encode($presets);

?>