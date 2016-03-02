<?php

// get formats


require_once('../../includes/init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');
require_once("../../includes/warehouse/warehouse_dictionary.php");

$params = array();

$sqlChecksum = "CHECKSUM TABLE `wh_formats`";
$row = mysql_fetch_array( mysql_query($sqlChecksum, $connection_stat), MYSQL_ASSOC );
$checksum = $row['Checksum'];
if ( $checksum != intval($checksum) ) {
	echo json_encode( array("result" => "error", "text" => "Unable to calculate checksum") );
	die();
}

$sqlSelect = "SELECT * FROM `wh_formats`";
$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );

if ( mysql_num_rows($resultSelect) > 0 ) {
	$formats = array();
	$maxTimestamp = date("U", 0);
	
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		$formats[] = array(
			"type" => $row["type"],
			"length" => intval( $row["length"] ),
			"width" => intval( $row["width"] ),
			"thickness" => intval( $row["thickness"] )
		);
		if ( strtotime($row["timestamp"]) > $maxTimestamp ) $maxTimestamp = strtotime($row["timestamp"]);
	}
	echo json_encode( array(
		"result" => "ok",
		"last_update" => date("c", $maxTimestamp),
		"checksum" => $checksum,
		"formats" => $formats)
	);
} else {
	echo json_encode( array("result" => "empty") );
}