<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
include('../cerber.php');
require_once("warehouse_dictionary.php");

if ( isset($_GET['dateStart']) ) $dateStart = mysql_real_escape_string( $_GET['dateStart'] );
else { $dateStart = date("d.m.Y", strtotime("2015-12-01 00:00")); }

$sqlSelect = "SELECT *, COUNT(*) as `packsCount`
FROM `wh_packs`
WHERE `timestamp` >= '" . date("Y-m-d 08:00:00", strtotime($dateStart)) . "'
GROUP BY CONCAT_WS('_', `width`, `length`, `thickness`, `sort_1`, `sort_2`, `sanding`, `quantity`, `client`) 
ORDER BY `client` ASC, `grade` ASC, `timestamp` ASC";
$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
$prevDate = "";
$prevGrade = "";
$content = array();
if ( mysql_num_rows($resultSelect) > 0 ) {
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		/*if ( $prevDate != $row["timestamp"] ) $prevDate = $row["timestamp"];
		if ( $prevGrade != $row["grade"] ) $prevGrade = $row["grade"];
		
		if ( !isset($content[$prevDate]) ) $content[$prevDate] = array();
		if ( !isset($content[$prevDate]) ) $content[$prevDate] = array();
		$isEditable = strtotime($prevReadyDate) > date("U") ? true : false;*/
		
		$date = strtotime( $row["timestamp"] );
		$hour = date("G", $date );
		if ( $hour >= 8 and $hour < 20 ) $shift = 1; else $shift = 2;
		$volume = intval($row["width"]) * intval($row["length"]) * intval($row["thickness"]) / 10 * intval($row["quantity"]) * intval($row["packsCount"]) / 1000000000;
		
		$content[] = array(
			"timestamp" => $row["timestamp"],
			"date" => date("d.m.Y", $date),
			"shift" => $shift,
			"id" => $row["id"],
			"grade" => $row["grade"],
			"type" => $row["type"],
			"type_text" => $wh_ply_types[ $row["type"] ],
			"width" => $row["width"],
			"isStandartWidth" => in_array($row["width"], $wh_standart_width),
			"length" => $row["length"],
			"isStandartLength" => in_array($row["length"], $wh_standart_length),
			"thickness" => $row["thickness"],
			"sort_1" => $row["sort_1"],
			"sort_2" => $row["sort_2"],
			"sanding" => $row["sanding"],
			"sanding_text" => $wh_ply_sanding[ $row["sanding"] ],
			"quantity" => $row["quantity"],
			"packsCount" => $row["packsCount"],
			"volume" => round($volume, 4),
			"comment" => $row["comment"],
			"client" => $row["client"],
		);
	}
	echo json_encode( array("result" => "ok", "packs" => $content) );
} else {
	echo json_encode( array("result" => "empty") );
}