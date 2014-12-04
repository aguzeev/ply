<?php

require_once('includes/init.php');

if(isset($_GET['date'])) $date = $_GET['date'];

//$date = '2012-12-24 08:00';
$thickness = 1.6; // default value
	  
$resultThickness = mysql_query("SELECT AVG(`thickness`) / 100000 FROM `raute_cutter`
	WHERE `timestamp` BETWEEN DATE_SUB('" . $date . "', INTERVAL 6 HOUR) AND DATE_ADD('" . $date . "', INTERVAL 12 HOUR)");

$thickness = mysql_result($resultThickness, 0);
mysql_free_result($resultThickness);

echo round($thickness, 4);

?>