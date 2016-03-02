<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
include('../cerber.php');
if ( $permition != 2 ) {
	echo json_encode( array("result" => "restricted", "text" => "У вас не хватает прав для редактирования") );
	die();
}

if ( isset($_GET["type"]) ) { $type = mysql_real_escape_string($_GET['type']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify ply type")); die(); }

if ( isset($_GET["width"]) ) { $width = intval( $_GET['width']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify ply width")); die(); }

if ( isset($_GET["length"]) ) { $length = intval($_GET['length']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify ply length")); die(); }

if ( isset($_GET["thickness"]) ) { $thickness = intval($_GET['thickness']); }
else { echo json_encode(array("result" => "error", "text" => "Please specify ply thickness")); die(); }

$sqlCheckDuplicates = "SELECT * FROM `wh_formats` WHERE 
	`type` = '" . $type . "' AND
	`length` = " . $length . " AND
	`width` = " . $width . " AND
	`thickness` = " . $thickness;
	
$resultCheckDuplicates = mysql_query($sqlCheckDuplicates, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
if ( mysql_num_rows($resultCheckDuplicates) > 0 ) {
	echo json_encode( array("result" => "duplicate", "text" => "Format already exists") );
	die();
}
	
$sqlInsert = "INSERT INTO `wh_formats` (`type`, `length`, `width`, `thickness`) VALUES (
	'" . $type . "',
	" . $length . ",
	" . $width . ",
	" . $thickness . "
	)";
$result = mysql_query($sqlInsert, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );

if ( $result ) {
	echo json_encode( array("result" => "ok") );
} else {
	echo json_encode( array("result" => "error", "text" => "Error while DB insert") );
	die();
}

?>