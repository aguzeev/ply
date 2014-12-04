<?php

$cookieTime = 604800; // ставим куку на 7 дней
$MACHINES_LIST = array();
$MACHINES_DATA = array();
$MACHINES_IDS = array();
$_IS_INCLUDED = false;
$_DEBUG = false;
date_default_timezone_set('Europe/Moscow');

//$maxDiffBeforeDaily = 60 * 60 * 24 * 30000; // максимальный период, после которого данные берутся из агрегированных дневных значений

// timezone correction
$timeDifference = intval(mysql_result(mysql_query("select timediff(now(), convert_tz(now(), @@session.time_zone, '+04:00'))", $connection_stat), 0)) * 60 * 60;

// getting all machines into the array
$queryMachines = "SELECT `id`, `table_name`, `title` FROM `machines`";
$resultMachines = mysql_query($queryMachines, $connection_hardware) or die(mysql_error());
if (mysql_num_rows($resultMachines) == 0) {
	//echo json_encode(array("result"=>"error","text"=>"No machines added","source"=>"constants"));
	echo '<p>В базе данных нет информации ни об одном станке</p>';
	die();
}
while ($rowMachines = mysql_fetch_array($resultMachines, MYSQL_ASSOC)) {
	// getting fields for every machine
	$machineId = $rowMachines['id'];
	$MACHINES_LIST[$rowMachines['table_name']] = $rowMachines['title'];
	// $MACHINES_TITLE_BY_ID[$machineId] = $rowMachines['title']; !!! machineNames see in constants.php
	$MACHINES_IDS[$rowMachines['id']] = $rowMachines['title'];
	$MACHINES_TABLENAMES[$rowMachines['id']] = $rowMachines['table_name'];
	$MACHINES_FIELDS[$rowMachines['table_name']] = array();
	$queryFields = "SELECT `field_name` FROM `machine_frame_fields` WHERE `machine_id` = " . $machineId;
	$resultFields = mysql_query($queryFields, $connection_hardware) or die(mysql_error());
	while ($rowFields = mysql_fetch_array($resultFields, MYSQL_ASSOC)) {
		$MACHINES_FIELDS[$rowMachines['table_name']][] = $rowFields['field_name'];
	}
}
mysql_free_result($resultMachines); mysql_free_result($resultFields);

// Получаем в ассоциативный массив все возможные должности и перечень допустимых операций по каждой должности
$query = "SELECT `id`, `appointment`, `allowedOperationsDefault` FROM `appointments`";
$appResult = mysql_query($query, $connection_stat) or die(mysql_error());
while ($row = mysql_fetch_array($appResult, MYSQL_ASSOC)) {
	$apps[ $row['id'] ] = $row['appointment'];
	$defaultOp[ $row['id'] ] = unserialize( $row['allowedOperationsDefault'] );
}
mysql_free_result($appResult);

// Получаем все возможные подразделения
$query = "SELECT `id`, `departmentName` FROM `departments`";
$deptResult = mysql_query($query, $connection_stat) or die(mysql_error());
while ($row = mysql_fetch_array($deptResult, MYSQL_ASSOC)) {
	$dept[ $row['id'] ] = $row['departmentName'];
}
mysql_free_result($deptResult);

// Получаем все возможные операции из списка операций
$query = "SELECT `id`, `operation` FROM `operations`";
$opResult = mysql_query($query, $connection_stat) or die(mysql_error());
while ($row = mysql_fetch_array($opResult, MYSQL_ASSOC)) {
	$allOperations[ $row['id'] ] = $row['operation'];
}
mysql_free_result($opResult);

// Получаем все возможные модули
$query = "SELECT `id`, `moduleName`, `moduleTitle` FROM `modules`";
$modResult = mysql_query($query, $connection_stat) or die(mysql_error());
while ($row = mysql_fetch_array($modResult, MYSQL_ASSOC)) {
	$allModules[ $row['id'] ] = $row['moduleTitle'];
	$allModulesUrls[ $row['moduleName'] ] = $row['moduleTitle'];
}
mysql_free_result($modResult);

// Пресеты станков для поиска лучших смен
$leaderShiftsVariants = array(
	//'0-1',
	'1-4',
	//'3-4',
	'3-5',
	'4-1',
	//'5-7',
	'6-2',
	'6-13',
	'8-0'
);

?>