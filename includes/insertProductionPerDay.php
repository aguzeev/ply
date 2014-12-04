<?php

require_once('init.php');

$ACCESSED_MODULE = 8;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['operationId']) ) $operationId = intval(mysql_real_escape_string($_GET['operationId']));
else { echo json_encode(array("result"=>"error","text"=>"Не указана операция")); die(); };
if ( isset($_GET['timeboardDate']) ) $timeboardDate = mysql_real_escape_string($_GET['timeboardDate']); // дата, в которой редактируется табель
else { echo json_encode(array("result"=>"error","text"=>"Не указана дата")); die(); }

if ( isset($_GET['timeboardObject']) ) $timeboardObject = json_decode( $_GET['timeboardObject'] );
else { echo json_encode(array("result"=>"error","text"=>"Неверный формат пришедших данных")); die(); };


$timeboardNextDate = date("Y-m-d", strtotime($timeboardDate) + 60*60*24); // следующая дата после полученной

$sqlDelete = "DELETE FROM `production` WHERE `operation_id` = $operationId AND `time_begin` BETWEEN '$timeboardDate 00:01' AND '$timeboardNextDate 00:00'";
mysql_query($sqlDelete, $connection_stat) or die(mysql_error());

foreach($timeboardObject as $key => $tbObj) {
	switch ($tbObj -> timeBegin) {
		case 'I смена':
			$timeBegin = $timeboardDate . ' 08:00';
			$timeEnd = $timeboardDate . ' 16:00';
		break;
		case 'II смена':
			$timeBegin = $timeboardDate .  ' 16:00';
			$timeEnd = $timeboardNextDate . ' 00:00';
		break;
		case 'III смена':
			$timeBegin = $timeboardNextDate . ' 00:00';
			$timeEnd = $timeboardNextDate . ' 08:00';
		break;
		default:
			$b = mysql_real_escape_string(str_replace(" ", "", $tbObj -> timeBegin));
			$e = mysql_real_escape_string(str_replace(" ", "", $tbObj -> timeEnd));
        	$timeBegin = $timeboardDate . ' ' . $b;
			$timeEnd = $timeboardDate . ' ' . $e; 
	}
	$prodValue = mysql_real_escape_string(str_replace(",", ".", $tbObj -> prodValue));
	$sqlInsert = "INSERT INTO `production` (
	`id`, `operation_id`, `time_begin`, `time_end`, `value`)
	VALUES (NULL , '$operationId', '$timeBegin', '$timeEnd', '$prodValue')";
	mysql_query($sqlInsert, $connection_stat) or die(mysql_error());
}
//print_r($timeboardObject);



?>