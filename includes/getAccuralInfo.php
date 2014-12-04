<?php

require_once('init.php');

$ACCESSED_MODULE = 9;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['workerId'])) $workerId = intval(mysql_real_escape_string($_GET['workerId']));
	else echo json_encode(array("result"=>"error","text"=>"Не указан сотрудник.","source"=>"getAccuralInfo"));
if(isset($_GET['day'])) $day = mysql_real_escape_string($_GET['day']);
	else echo json_encode(array("result"=>"error","text"=>"Не указан день.","source"=>"getAccuralInfo"));
$dayinTime = strtotime($day);
$nextday = date( "Y-m-d", mktime(0, 0, 0, date("m", $dayinTime)  , date("d", $dayinTime)+1, date("Y", $dayinTime)) );


$begin_start = $day . " 08:00";
$begin_stop = $nextday . " 08:00";
$end_start = $day . " 08:00";
$end_stop = $nextday . " 08:00";

$sqlAccural = "SELECT t2.operation_id, t3.value, t4.rate_1, t4.rate_2, t4.cond_2, t4.rate_3, t4.cond_3

FROM `workers` AS t1
INNER JOIN `timeboard` AS t2
INNER JOIN `production` AS t3
INNER JOIN `rates` AS t4

ON t1.id = t2.`worker_id`
AND t2.time_begin = t3.time_begin
AND t2.time_end = t3.time_end
AND t2.operation_id = t3.operation_id
AND t2.operation_id = t4.operation_id
AND t1.appointment = t4.app_id


WHERE t1.id = " . $workerId . " AND 
t2.time_begin BETWEEN '" . $begin_start . "' AND '" . $begin_stop . "' AND
t2.time_end BETWEEN '" . $end_start . "' AND '" . $end_stop . "' AND
ADDTIME(t4.start_date, '8:0:0') < '" . $begin_start . "' 
ORDER BY t4.start_date DESC LIMIT 1";

$sum = 0;
$parts = array();

$resultAccural = mysql_query($sqlAccural, $connection_stat);
if (mysql_num_rows($resultAccural) > 0) {
	while ($rowAccural = mysql_fetch_array($resultAccural, MYSQL_ASSOC)) {
		if (isset($rowAccural['cond_3']) & $rowAccural['value'] >= $rowAccural['cond_3']) $rate = $rowAccural['rate_3'];
			else if (isset($rowAccural['cond_2']) & $rowAccural['value'] >= $rowAccural['cond_2']) $rate = $rowAccural['rate_2'];
				else $rate = $rowAccural['rate_1'];
		$partSum = $rate * $rowAccural['value'];
		$parts[] = array("value" => $rowAccural['value'], "rate" => $rate, "operation" => getOperationName($rowAccural['operation_id']), "partSum" => $partSum);
		$sum = $sum + $partSum;
	}
}
$total = array("sum" => $sum, "parts" => $parts);
mysql_free_result($resultAccural);

echo json_encode($total);


?>