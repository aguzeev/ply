<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');
require_once("warehouse_dictionary.php");

$params = array();
if ( isset($_GET['type']) ) $params["type"] = addslashes( $_GET['type'] );
if ( isset($_GET['width']) ) $params["width"] = intval( $_GET['width'] );
if ( isset($_GET['length']) ) $params["length"] = intval( $_GET['length'] );
if ( isset($_GET['thickness']) ) $params["thickness"] = intval( $_GET['thickness'] );

if ( sizeof($params) > 0 ) {
	foreach ( $params as $key => $value ) $params[$key] = "`" . $key . "` = '" . $value . "'";
	$sqlCondition = " WHERE " . implode(" AND ", $params);
}


$sqlSelect = "SELECT * FROM `wh_formats`" . $sqlCondition;
$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );

if ( mysql_num_rows($resultSelect) > 0 ) {
	$formats = array();
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		$formats[] = $row;
	}
	echo json_encode( array("result" => "ok", "formats" => $formats) );
} else {
	echo json_encode( array("result" => "empty") );
}