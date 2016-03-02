<?php

// import packs

require_once('../../includes/init.php');

header('Content-type: application/json');

if ( true ) {
	ini_set('error_reporting', -1);
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);
}

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');


// *************************
// ***** packs import *****
// *************************
if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
	
	$json = file_get_contents('php://input');
	$packs = json_decode( $json, true );	
	
	if ( is_null($packs) ) {
		echo json_encode( array("result" => "error", "text" => "No packs specified") );
		die();
	}
	
	$packs_to_import = array();
	$i = 0;
	$max_values_for_query = 30;
	
	$pieces_to_import = array_chunk($packs, $max_values_for_query);
	$sqlValues = "";
	
	foreach ( $pieces_to_import as $piece ) {
		$sqlValues = "(" . implode("), (", array_map("myImplode", $piece)) . ")
		";
		$sqlInsert = "INSERT INTO `wh_packs` (`id`, `timestamp`, `operator`, `grade`, `type`, `length`, `width`, `thickness`, `sort_1`,  `sort_2`, `sanding`, `quantity`, `volume`, `comment`) VALUES " . $sqlValues;
		
		//echo $sqlInsert . "<br><br>";
		$resultInsert = mysql_query($sqlInsert, $connection_stat);
		if ( $resultInsert === false ) {
			echo json_encode( array("result" => "error", "text" => "MySQL Error: " . mysql_error($connection_stat), "sql" => $sqlInsert) );
			die();
		}
	}
	
	echo json_encode( array("result" => "ok") );
	
// *************************
// *** get last imported ***
// *************************
} elseif ( $_SERVER['REQUEST_METHOD'] == "GET" ) { // get the last pack's number
	$sqlSelect= "SELECT `id`, `timestamp` FROM`wh_packs` ORDER BY `id` DESC LIMIT 1";
	$resultSelect = mysql_query($sqlSelect, $connection_stat);
	
	if ( mysql_num_rows( $resultSelect ) > 0 ) {
		$lastRecord = mysql_fetch_array($resultSelect, MYSQL_ASSOC);
		echo json_encode( array("result" => "ok", "last_pack" => array("id" => $lastRecord["id"], "timestamp" => $lastRecord["timestamp"])) );
	} else {
		echo json_encode( array("result" => "empty", "text" => "There are no packs in DB") );
	}
} else {
	echo json_encode( array("result" => "error", "text" => "Unknown method") );
}

function myImplode( $array ) {
	$array = array_map("addslashes", $array);
	return "'" . implode("', '", $array) . "'";
}