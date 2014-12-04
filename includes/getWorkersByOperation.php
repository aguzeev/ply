<?php

require_once('init.php');

$ACCESSED_MODULE = 2;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['operationId']) ) {
	$operationId = intval(mysql_real_escape_string($_GET['operationId']));
} else {
	echo json_encode(array("result"=>"error","text"=>"Не задана операция"));
	die();
};
if ( isset($_GET['workday']) ) // если необходимо учитывать дату принятия и ухода с работы
	$workday = mysql_real_escape_string($_GET['workday']);
else { echo json_encode(array("result"=>"error","text"=>"Не указана дата.","source"=>"getWorkersByOperation.php")); die(); };

$list = '';
$workersByOperation = getWorkersByOperation($operationId,  $workday);

foreach ($workersByOperation[0] as $key => $option) {
	$list = $list . "<option value='$option'>" . $workersByOperation[1][$key] . "</option>";
}
echo $list;

?>