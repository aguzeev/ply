<?php

//$id1 = ;
//$id2 = ;
$date1 = "01.04.2012 08:00";
$date2 = "01.05.2012 08:00";

$db = mysql_connect("localhost:13306", "root", "plyfanera", true) or die (mysql_error());
mysql_select_db("machines_data", $db) or die (mysql_error());

$begin = date("U", strtotime($date1)); echo $begin;
$end = date("U", strtotime($date2)); echo $end;

$nTimes = round(($end - $begin) / 60 / 60 / 24, 0) + 1;
echo "Будет выведено значение за $nTimes дней<br>";

$counter = array();
for ($epoch = 0; $epoch < $nTimes; $epoch++) {
	$b = $begin + $epoch * 60 * 60 * 24; // начало суток
	$e = $b + 60 * 60 * 24; // конец суток
	
	$timestampB = date("Y-m-d G:i:s", $b);
	$timestampE = date("Y-m-d G:i:s", $e);
	
	$sql = "SELECT COUNT( * ) FROM `raute_shell` WHERE `timestamp` BETWEEN '$timestampB' AND '$timestampE'";
	$result = mysql_query($sql) or die();
	
	
	//$counter[date("d.m.Y", strtotime($b))] = mysql_result($result, 0);
	echo "$timestampB: " . mysql_result($result, 0) . "<br>";
}


?>