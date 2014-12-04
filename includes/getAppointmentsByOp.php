<?php

require_once('init.php');

$ACCESSED_MODULE = 3;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['opId']) ) {
	$opId = intval(mysql_real_escape_string($_GET['opId']));
} else {
	echo json_encode(array("result"=>"error","text"=>"Не указана операция.","source"=>"getAppointmentsByOp"));
	die();
};

// получаем все должности, допущенные к определённой операции
$query = "SELECT `id`, `appointment` FROM `appointments` WHERE `allowedOperationsDefault` LIKE '%\"" . $opId . "\"%'";
$result = mysql_query($query, $connection_stat) or die(mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$appointments[] = array($row['id'], $row['appointment']);
}
mysql_free_result($result);

echo( json_encode($appointments) );

?>