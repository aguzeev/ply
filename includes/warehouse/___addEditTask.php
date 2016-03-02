<?php

require_once('../init.php');

$ACCESSED_MODULE = 10;
$_LOGGING = false;
include('../cerber.php');

$successText = "[success]";

if ( isset($_GET["id"]) ) { $id = mysql_real_escape_string($_GET['id']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify id")); die(); }

if ( isset($_GET["action"]) ) { $action = mysql_real_escape_string($_GET['action']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify action")); die(); }

if ( $action != "delete" ) {
	
	if ( isset($_GET["readyDate"]) ) { $readyDate = mysql_real_escape_string($_GET['readyDate']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply ready date")); die(); }
	
	if ( isset($_GET["type"]) ) { $type = mysql_real_escape_string($_GET['type']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply type")); die(); }
	
	if ( isset($_GET["width"]) ) { $width = intval( $_GET['width']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply width")); die(); }
	
	if ( isset($_GET["length"]) ) { $length = intval($_GET['length']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply length")); die(); }
	
	if ( isset($_GET["thickness"]) ) { $thickness = intval($_GET['thickness']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply thickness")); die(); }
	
	if ( isset($_GET["sort_1"]) ) { $sort_1 = intval($_GET['sort_1']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply sort_1")); die(); }
	
	if ( isset($_GET["sort_2"]) ) { $sort_2 = intval($_GET['sort_2']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply sort_2")); die(); }
	
	if ( isset($_GET["sanding"]) ) { $sanding = intval($_GET['sanding']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply sanding")); die(); }
	
	if ( isset($_GET["quantity"]) ) { $quantity = intval($_GET['quantity']); }
	else { echo json_encode(array("result" => "error", "text" => "Please specify ply quantity")); die(); }
	
	if ( isset($_GET["comment"]) ) { $comment = mysql_real_escape_string($_GET['comment']); }
	else { $comment = ""; }
	
	$volume = $length * $width * $thickness / 10 * $quantity;
	$readyDate = date("Y-m-d", strtotime($readyDate));
	
	
	if ( $action == "add" ) {
		// adding new task
		$sqlInsert = "INSERT INTO `wh_tasks` (`readyDate`, `type`, `length`, `width`, `thickness`, `sort_1`, `sort_2`, `sanding`, `quantity`, `volume`, `comment`) VALUES (
			'" . $readyDate . "',
			'" . $type . "',
			" . $length . ",
			" . $width . ",
			" . $thickness . ",
			" . $sort_1 . ",
			" . $sort_2 . ",
			" . $sanding . ",
			" . $quantity . ",
			" . $volume . ",
			'" . $comment . "')";
		$result = mysql_query($sqlInsert, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
		$successText = "Задание добавлено";
		
	} else {
		
		//editing existing task
		$sqlUpdate = "UPDATE `wh_tasks` SET
		`readyDate` = '" . $readyDate . "',
		`type` = '" . $type . "',
		`length` = " . $length . ",
		`width` = " . $width . ",
		`thickness` = " . $thickness . ",
		`sort_1` = " . $sort_1 . ",
		`sort_2` = " . $sort_2 . ",
		`sanding` = " . $sanding . ",
		`quantity` = " . $quantity . ",
		`volume` = " . $volume . ",
		`comment` = '" . $comment . "'
		WHERE `id` = " . $id . " LIMIT 1";
		$result = mysql_query($sqlUpdate, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
		$successText = "Задание изменено";
	}
} else {
	//deleting task
	$sqlDelete = "DELETE FROM `wh_tasks` WHERE `id` = " . $id . " LIMIT 1";
	$result = mysql_query($sqlDelete, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
	$successText = "Успешно удалено";
}

if ( $result ) {
	echo json_encode( array("result" => "ok", "text" => $successText) );
} else {
	echo json_encode( array("result" => "error", "text" => "DB error") );
	die();
}


?>