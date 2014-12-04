<?php

require_once('init.php');

$ACCESSED_MODULE = 2;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['dept']) ) {
	$dept = intval(mysql_real_escape_string($_GET['dept']));
} else {
	$dept = 0;
};

if ($dept == 0) $query = "SELECT `id`, `appointment` FROM `appointments`"; // сотрудники по всем подразделениям
	else $query = "SELECT `id`, `appointment` FROM `appointments` WHERE `department` = '" . $dept . "'";
$result = mysql_query($query, $connection_stat) or die(mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$appointments[] = array($row['id'], $row['appointment']);
}


echo( json_encode($appointments) );

?>