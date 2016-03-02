<?php

// get tasks
header('Content-Type: text/json; charset=utf-8');

require_once('../../includes/init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
$warning = array();
//include('../cerber.php');
require_once("../../includes/warehouse/warehouse_dictionary.php");

$taskTypes = array("lopping", "packing");
if ( isset($_GET['taskType']) ) $taskType = $taskTypes[ array_search($_GET['taskType'], $taskTypes) ];
else { echo json_encode(array("result"=>"error","text"=>"taskType: please specify type of the task")); die(); }

if ( isset($_GET['dateShift']) ) {
	$dateShift = addslashes($_GET['dateShift']);
	$_GET["date"] = date("d.m.Y", strtotime($dateShift));
	$warning[] = "'dateShift' is deprecated. Please use 'date' instead. Now date " . $_GET["date"] . " will be used.";
}

if ( isset($_GET['date']) ) $date = addslashes($_GET['date']);
else { echo json_encode(array("result"=>"error","text"=>"date: please specify required date in format: yyyy.mm.dd", "warning" => $warning)); die(); }


$dateStart = date("Y-m-d", strtotime($date) ) . " 08:00";
$dateEnd = date("Y-m-d", (strtotime($date) + 24 * 60 * 60 ) ) . " 07:59";

$sqlSelect = "SELECT t.*,
(SELECT SUM(p.`quantity`) FROM `wh_packs` p
WHERE
    t.`type` = p.`type` AND
    t.`width` = p.`width` AND
    t.`length` = p.`length` AND
    t.`thickness` = p.`thickness` AND
    t.`sort_1` = p.`sort_1` AND
    t.`sort_2` = p.`sort_2` AND
 	p.`timestamp` BETWEEN
       DATE_SUB(t.`readyDate`, INTERVAL 1 HOUR) AND
       DATE_ADD(t.`readyDate`, INTERVAL 11 HOUR)
) AS `readyQuant`
FROM `wh_tasks` t
WHERE `readyDate` >= '" . $dateStart . "'
AND `readyDate` < '" . $dateEnd . "'
AND `taskType` = '" . $taskType . "' ORDER BY `readyDate` ASC, t.`timestamp` ASC";

//echo $sqlSelect . "<br><br>";
$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
$content = array();
if ( mysql_num_rows($resultSelect) > 0 ) {
	$isFirst = true;
	
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		if ( $isFirst ) {
			$isFirst = false;
			
			$hour = date("G", strtotime( $row["readyDate"]) );
			if ( $hour >= 8 and $hour < 20 ) $shift = 1; else $shift = 2;
			
			$date = strtotime( $row["readyDate"]);
			$date_text = date("j", $date) . "&nbsp;" . $_monthes_rp[date("m", $date) - 1];
			
			$content = array(
				"date_text" => $date_text,
				//"date" => date("Y-m-d", $date),
				"date" => date("c", $date),
				"shift" => $shift
			);
		}
		
		$content["tasks_data"][] = array(
				"id" => intval( $row["id"] ),
				"type" => $row["type"],
				"type_text" => $wh_ply_types[ $row["type"] ],
				"width" => intval( $row["width"] ),
				"length" => intval( $row["length"] ),
				"thickness" => intval( $row["thickness"] ),
				"sort_1" => intval( $row["sort_1"] ),
				"sort_2" => intval( $row["sort_2"] ),
				"sanding" => intval( $row["sanding"] ),
				"sanding_text" => $wh_ply_sanding[ $row["sanding"] ],
				"quantity" => intval( $row["quantity"] ),
				"comment" => $row["comment"]
		);
	}
	echo json_encode( array("result" => "ok", "task_type" => $taskType, "content" => $content, "warning" => $warning), JSON_UNESCAPED_UNICODE );
} else {
	echo json_encode( array("result" => "empty", "warning" => $warning) );
}