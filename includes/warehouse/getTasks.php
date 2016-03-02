<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');
require_once("warehouse_dictionary.php");

$taskTypes = array("lopping", "packing");
if ( isset($_GET['taskType']) ) $taskType = $taskTypes[ array_search($_GET['taskType'], $taskTypes) ];
else { echo json_encode(array("result"=>"error","text"=>"taskType: Не указан требуемый тип задания")); die(); }

/* v1
$sqlSelect = "SELECT * FROM `wh_tasks` WHERE `readyDate` >= CURRENT_DATE AND `taskType` = '" . $taskType . "' ORDER BY `readyDate` ASC, `timestamp` ASC";
*/

/* v2
$sqlSelect = "SELECT t.*, COALESCE(p.`quantity`, 0) AS readyQuant FROM `wh_tasks` t
LEFT OUTER JOIN `wh_packs` p ON
t.`type` = p.`type` AND
t.`width` = p.`width` AND
t.`length` = p.`length` AND
t.`thickness` = p.`thickness` AND
t.`sort_1` = p.`sort_1` AND
t.`sort_2` = p.`sort_2`
WHERE `readyDate` >= '" . date("Y-m-d") . " 08:00'
AND DATE(`readyDate`) 
AND `taskType` = '" . $taskType . "' ORDER BY `readyDate` ASC, `timestamp` ASC";*/

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
WHERE `readyDate` >= '" . date("Y-m-d") . " 08:00'
AND `taskType` = '" . $taskType . "' ORDER BY `readyDate` ASC, t.`timestamp` ASC";


$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
$prevReadyDate = "";
$content = array();
if ( mysql_num_rows($resultSelect) > 0 ) {
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		if ( $prevReadyDate != date( "d.m.Y", strtotime($row["readyDate"]) ) ) $prevReadyDate = date( "d.m.Y", strtotime($row["readyDate"]) );
		
		$hour = date("G", strtotime( $row["readyDate"]) );
		if ( $hour >= 8 and $hour < 20 ) $shift = 1;
		else $shift = 2;
		$date = strtotime( $prevReadyDate );
		$isEditable = strtotime($prevReadyDate) > date("U") ? true : false;
		$date_text = date("j", $date) . "&nbsp;" . $_monthes_rp[date("m", $date) - 1];
		
		if ( !isset($content[$prevReadyDate . "_" . $shift]) ) {
			$content[$prevReadyDate . "_" . $shift] = array(
				"isEditable" =>$isEditable,
				"date_text" => $date_text,
				"date" => $prevReadyDate,
				"shift" => $shift
			);
		}
		
		
		/*$content[$prevReadyDate . "_" . $shift][] = array(
			"taskType" => $taskType,
			"isEditable" =>$isEditable,
			"date_text" => $date_text,
			"date" => $prevReadyDate . "_" . $shift,
			"shift" => $shift,
			"taskData" => array(
				"id" => $row["id"],
				"type" => $row["type"],
				"type_text" => $wh_ply_types[ $row["type"] ],
				"width" => $row["width"],
				"length" => $row["length"],
				"thickness" => $row["thickness"],
				"sort_1" => $row["sort_1"],
				"sort_2" => $row["sort_2"],
				"sanding" => $row["sanding"],
				"sanding_text" => $wh_ply_sanding[ $row["sanding"] ],
				"quantity" => $row["quantity"],
				"comment" => $row["comment"],
				"readyness" => $row["readyQuant"]
			)
		);*/
		
		$content[$prevReadyDate . "_" . $shift]["tasksData"][] = array(
				"id" => $row["id"],
				"type" => $row["type"],
				"type_text" => $wh_ply_types[ $row["type"] ],
				"width" => $row["width"],
				"length" => $row["length"],
				"thickness" => $row["thickness"],
				"sort_1" => $row["sort_1"],
				"sort_2" => $row["sort_2"],
				"sanding" => $row["sanding"],
				"sanding_text" => $wh_ply_sanding[ $row["sanding"] ],
				"quantity" => $row["quantity"],
				"comment" => $row["comment"],
				"readyness" => $row["readyQuant"]
		);
	}
	echo json_encode( array("result" => "ok", "taskType" => $taskType, "content" => $content) );
} else {
	echo json_encode( array("result" => "empty") );
}