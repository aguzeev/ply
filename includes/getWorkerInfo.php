<?php


require_once('init.php');

$ACCESSED_MODULE = 2;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['workerId'])) $workerId = intval(mysql_real_escape_string($_GET['workerId']));



$query = "SELECT * FROM `workers` WHERE `id` = " . $workerId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

// получаем список доступных операций в должности
$query = "SELECT `allowedOperationsDefault` FROM `appointments` WHERE `id` = " . $row['appointment'] . " LIMIT 1";
$opRes = mysql_query($query, $connection_stat) or die(mysql_error());

$defOpArray = unserialize(mysql_result($opRes, 0));
if ($defOpArray === 'null') {
	$defOp = array();
} else {
	foreach($defOpArray as $value) {
		//$defOp[] = getOperationName($value);
		$defOp[] = array($value, getOperationName($value));
	}
}
$dSt = ($row['date_start_work'] != '') ? date("d.m.Y", strtotime($row['date_start_work'])) : '';
$dFin = ($row['date_finish_work'] != '') ? date("d.m.Y", strtotime($row['date_finish_work'])) : '';

$appInfo = json_encode( array(
	$row['name_family'],
	$row['name_first'],
	$row['name_middle'],
	$row['sector'], // или getDeptName( $row['sector'] ),
	$row['appointment'], // или getAppName( $row['appointment'] ),
	//unserialize( $defOp ), // операции из должности (id)
	$defOp,
	unserialize( $row['additionalOperations'] ), // персональные операции
	$dSt,
	$dFin,
	$row['isActive'],
) );

print_r($appInfo);


?>