<?php

// get tasks


require_once('../../includes/init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');

$sqlSelect = "SELECT `card_id` FROM `wh_cards`";

$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
$cards = array();
if ( mysql_num_rows($resultSelect) > 0 ) {
	while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
		$cards[] = $row["card_id"];
	}
	echo json_encode( array("result" => "ok", "cards" => $cards) );
} else {
	echo json_encode( array("result" => "empty") );
}