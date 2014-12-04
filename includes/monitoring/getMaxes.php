<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
require_once('../init.php');
require_once('plotVariants.php');
date_default_timezone_set('Europe/Moscow');

$total_result = array();
//print_r($MACHINES_LIST);
//echo $MACHINES_LIST['lopping'];
$monthes = array("янв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "ноя", "дек");

$resultMaxes = mysql_query("SELECT `timestamp`, `machine`, `value` FROM `maxes`", $connection_hardware);
while ($row = mysql_fetch_array($resultMaxes, MYSQL_ASSOC)) {
	$tstamp = strtotime($row["timestamp"]);
	$hour = intval( date( "G", $tstamp ) );
	
	if ( $hour == 16 ) {
		$shift = date("d.m.Y", $tstamp) . "<br>1-я смена";
	} elseif ( $hour == 0 ) {
		$shift = date("d.m.Y", $tstamp - 1) . "<br>2-я смена";
	} elseif ( $hour == 8 ) {
		$shift = date("d.m.Y", $tstamp - 28801) . "<br>3-я смена";
	} else {
		$shift = date("d.m.Y", $tstamp) . "<br>" . $hour . " часов";
	}
	
	if ( date("U") - $tstamp < 172800 ) $isNew = true;
	else $isNew = false;
		
	//$total_result[] = array( "machine" => $MACHINES_LIST[ $row['machine'] ], "date" => $row['timestamp'], "dateShift" => $shift, "value" => $row['value'] );
	$index = $row['machine'];
	
	$db_result[$index] = array(
		"title" => $MACHINES_LIST[ $row['machine'] ],
		"date" => $row['timestamp'],
		"dateShift" => $shift,
		"value" => $row['value'],
		"link" => "index.php?act=monitoring.month&machine=" . $row['machine'] .
			"&year=" . date( "Y", strtotime($row['timestamp']) ) .
			"&month=" . (date( "n", strtotime($row['timestamp']) )),
		"isNew" => $isNew
	);
}

foreach ( $leaderShiftsVariants as $monthVar ) {
	$monthVarArray = explode("-", $monthVar);
	$machineN = $monthVarArray[0];
	$variant = $monthVarArray[1];
	$machine = $plotVariants[$machineN][$variant]['machine'];
	
	$total_result[] = $db_result[$machine];
}

mysql_free_result($resultMaxes);

echo json_encode($total_result);

?>