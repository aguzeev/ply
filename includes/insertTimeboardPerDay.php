<?php

require_once('init.php');

/*ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);*/

$ACCESSED_MODULE = 7;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['operationId']) ) $operationId = intval(mysql_real_escape_string($_GET['operationId']));
else { echo json_encode(array("result"=>"error","text"=>"Не указана операция")); die(); };
if ( isset($_GET['timeboardDate']) ) $timeboardDate = mysql_real_escape_string($_GET['timeboardDate']); // дата, в которой редактируется табель
else { echo json_encode(array("result"=>"error","text"=>"Не указана дата")); die(); }
/*
$toReplace = array(".", " ");
if ( isset($_GET['timeBegin']) ) $timeBegin = mysql_real_escape_string( str_replace($toReplace, '-' , $_GET['timeBegin']) );
else { echo json_encode(array("result"=>"error","text"=>"Не указано время начала работ")); die(); };
if ( isset($_GET['timeEnd']) ) $timeEnd = mysql_real_escape_string( str_replace($toReplace, '-' , $_GET['timeEnd']) );
else { echo json_encode(array("result"=>"error","text"=>"Не указано время окончания работ")); die(); };
if ( isset($_GET['workerIds']) ) $workerIds = mysql_real_escape_string($_GET['workerIds']);
else { echo json_encode(array("result"=>"error","text"=>"Не указаны сотрудники")); die(); };
*/
if ( isset($_GET['timeboardObject']) ) $timeboardObject = json_decode( $_GET['timeboardObject'] );
else { echo json_encode(array("result"=>"error","text"=>"Неверный формат пришедших данных")); die(); };


$timeboardNextDate = date("Y-m-d", strtotime($timeboardDate) + 60*60*24); // следующая дата после полученной

$sqlDelete = "DELETE FROM `timeboard` WHERE `operation_id` = $operationId AND `time_begin` BETWEEN '$timeboardDate 08:00' AND '$timeboardNextDate 07:59'";
mysql_query($sqlDelete, $connection_stat) or die(mysql_error());

if ( $_DEBUG ) echo $sqlDelete . "<br>";
if ( $_DEBUG ) print_r( $timeboardObject ) . "<br>";
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
			
			// поправки для отображения периодов с 00:00 до 07:59 в предыдущих сутках
			$hoursB = intval(substr($b, 0, 2)); $minutesB = intval(substr($b, 0, 2));
			$hoursE = intval(substr($e, 0, 2)); $minutesE = intval(substr($e, 3, 2));
			if ( $hoursB < 8 ) { // физически пришедший период должен быть в следующих сутках
				$timeBegin = $timeboardNextDate . ' ' . $b;
				$timeEnd = $timeboardNextDate . ' ' . $e; 
			} else {			
				$timeBegin = $timeboardDate . ' ' . $b;
				$timeEnd = $timeboardDate . ' ' . $e; 
			}
	}
	foreach ($tbObj -> workers as $workerIndex => $worker) {
		$sqlInsert = "INSERT INTO `timeboard` (
		`id`, `worker_id`, `operation_id`, `time_begin`, `time_end`)
		VALUES (NULL , '$worker', '$operationId', '$timeBegin', '$timeEnd')";
		if ( $_DEBUG ) echo $sqlInsert . "<br>";
		mysql_query($sqlInsert, $connection_stat) or die(mysql_error());
	}
}
//print_r($timeboardObject);



?>