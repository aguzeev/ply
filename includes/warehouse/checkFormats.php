<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
include('../cerber.php');
require_once("warehouse_dictionary.php");

if ( isset($_POST['formats']) ) $formats = json_decode($_POST['formats'], true);

if ( sizeof($formats) > 0 ) {
	$formatsResult = array();
	foreach ( $formats as $format ) {
		$sqlCount = "SELECT COUNT(*) FROM `wh_formats` WHERE
		`type` = '" . $format["type"] . "' AND
		`width` = '" . $format["width"] . "' AND
		`length` = '" . $format["length"] . "' AND
		`thickness` = '" . $format["thickness"] . "'";
		$resultCount = mysql_query($sqlCount, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
		$count = mysql_result( $resultCount, 0 );
		if ( $count > 0 ) $formatsResult[ $format["id"] ] = true;
		else $formatsResult[ $format["id"] ] = false;
	}
	echo json_encode( array("result" => "ok", "checkResults" => $formatsResult) );
} else {
	echo json_encode( array("result" => "empty") );
}