<?php

require_once('init.php');

$ACCESSED_MODULE = 7;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['operationId']) ) $operationId = intval(mysql_real_escape_string($_GET['operationId']));
else { echo json_encode(array("result"=>"error","text"=>"Не указана операция")); die(); };
if ( isset($_GET['timeboardDate']) ) $timeboardDate = mysql_real_escape_string($_GET['timeboardDate']);
else { echo json_encode(array("result"=>"error","text"=>"Не указан день.","source"=>"getTimeboardPerDay")); die(); };
// if ( isset($_GET['workday']) ) // необходимо учитывать дату принятия и ухода с работы
//	$workday = mysql_real_escape_string($_GET['workday']);
// else { echo json_encode(array("result"=>"error","text"=>"Не указана дата принятия на работу.","source"=>"getTimeboardPerDay")); die(); }
	$workday = $timeboardDate; // необходимо учитывать дату принятия и ухода с работы

$timeboardNextDate = date("Y-m-d", strtotime($timeboardDate) + 60*60*24); // следующая да после полученной

/*$query = "SELECT `worker_id`, `time_begin`, `time_end` FROM `timeboard` WHERE `operation_id` = " . $operationId . " AND `time_begin` BETWEEN '" . $timeboardDate . "' AND '" . $timeboardNextDate . "'";

$result = mysql_query($query, $connection_stat) or die(mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$workers[] = $row['worker_id'];
}
$workersJSON = json_encode(array( $workers ));

echo($workersJSON);*/





$sql = "SELECT DISTINCT `time_begin`, `time_end` FROM `timeboard` WHERE `operation_id` = " . $operationId . " AND `time_begin` BETWEEN '" . $timeboardDate . " 08:00' AND '" . $timeboardNextDate . " 07:59' ORDER BY `time_begin` ASC"; // выбираем уникальные промежутки времени за полученный период
$resultPeriods = mysql_query($sql, $connection_stat) or die(mysql_error());
$timeBoardDay = array();
while ($rowPeriods = mysql_fetch_array($resultPeriods, MYSQL_ASSOC)) {
	$begin = $rowPeriods['time_begin'];
	$end = $rowPeriods['time_end'];
	
	/*
	// получаем список всех сотрудников, работавших в данный период над этой операциией
	$sqlWorkers = "SELECT `worker_id` FROM `timeboard` WHERE 
	`operation_id` = " . $operationId . " AND 
	`time_begin` = '" . $begin . "' AND 
	`time_end` = '" . $end . "'";
	
	
	$resultWorkers = mysql_query($sqlWorkers, $connection_stat) or die(mysql_error());
	$workersIds = array();
	$workersNames = array();
	while ($rowWorkers = mysql_fetch_array($resultWorkers, MYSQL_ASSOC)) {
		$workersIds[] = $rowWorkers['worker_id'];
		$workersNames[] = getWorkerName($rowWorkers['worker_id']);	
	}
	mysql_free_result($resultWorkers);
	*/
	
	
	$workers = getWorkersByOperation($operationId, $workday);
	
	// получаем список всех сотрудников, работавших в данный период над этой операциией
	$sqlMarkedWorkers = "SELECT `worker_id` FROM `timeboard` WHERE 
	`operation_id` = " . $operationId . " AND 
	`time_begin` = '" . $begin . "' AND 
	`time_end` = '" . $end . "'";
	
	$MarkedWorkers = array(); $MarkedWorkersNames = array();
	$resultMarkedWorkers = mysql_query($sqlMarkedWorkers, $connection_stat) or die(mysql_error());
	$MarkedWorkers = array();
	while ($rowMarkedWorkers = mysql_fetch_array($resultMarkedWorkers, MYSQL_ASSOC)) {
		$MarkedWorkers[] = $rowMarkedWorkers['worker_id'];
		$MarkedWorkersNames[] = getWorkerName($rowMarkedWorkers['worker_id'], true); // true для вывода сокращения И. О.
	}
	
	// получаем значение выполненного объёма в данный период по данной операции
	$sqlProductedValues = "SELECT `value` FROM `production` WHERE 
	`operation_id` = " . $operationId . " AND 
	`time_begin` = '" . $begin . "' AND 
	`time_end` = '" . $end . "'";
	
	$resultValues = mysql_query($sqlProductedValues, $connection_stat);		
	if (mysql_num_rows($resultValues) == 0) $productedValue = 0;
		else $productedValue = mysql_result($resultValues, 0);
	
	
	$timeBoardDay[] = array($begin, $end, $workers[0], $workers[1], $MarkedWorkers, $MarkedWorkersNames, $productedValue);
	
	/*
	$begin = date("H:i", strtotime($begin)); $end = date("H:i", strtotime($end));
	if ($begin == '08:00' & $end == '16:00') $begin = 'I смена';
		else if ($begin == '16:00' & $end == '00:00') $begin = 'II смена';
		else if ($begin == '00:00' & $end == '08:00') $begin = 'III смена';
				
	$timeBoardDay[] = array($begin, $end, $workers);
	*/
}
mysql_free_result($resultPeriods);

$timeBoardDayJSON = json_encode($timeBoardDay);

echo($timeBoardDayJSON);

?>