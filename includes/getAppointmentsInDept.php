<?php

require_once('init.php');

$ACCESSED_MODULE = 3;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['dept']) ) {
	$dept = intval(mysql_real_escape_string($_GET['dept']));
} else {
	$dept = 0;
};

if ($dept == 0) $query = "SELECT `appointments`.`id`, `appointment`, `departments`.`departmentName` FROM `appointments`, `departments` WHERE `appointments`.`department` = `departments`.`id`"; // должности по всем подразделениям
	else $query = "SELECT `id`, `appointment` FROM `appointments` WHERE `department` = '" . $dept . "'";
$result = mysql_query($query, $connection_stat) or die(mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($dept == 0) $appointments[] = array('id' => $row['id'], 'appointment' => ($row['departmentName'] . ' — ' . $row['appointment']));
		else $appointments[] = array('id' => $row['id'], 'appointment' => $row['appointment']);
}


echo( json_encode($appointments) );

?>