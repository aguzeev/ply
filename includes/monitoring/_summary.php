<?php

  require_once('../init.php');
  
  if(isset($_GET['machine'])) $machine = $_GET['machine'];
  if(isset($_GET['field'])) $field = $_GET['field'];
  if(isset($_GET['start'])) $startDate = $_GET['start'];
  if(isset($_GET['end'])) $endDate = $_GET['end'];
  if(isset($_GET['operation'])) $operation = $_GET['operation'];
  
$scale = array(
	'raute_shell' => array(
		'curr_vol' => 1000000000,
		'percent_exit' => '10',
		'percent_core' => '10',
	),
	'raute_cutter' => array(
		'bin1_incr' => 1000000000,
		'bin2_incr' => '1000000000',
		'bin3_incr' => '1000000000',
	),
);

$beg = strtotime($startDate);
$end = strtotime($endDate);
$beg = date("Y-m-d G:i:s", $beg); //echo ("Begin: ".$beg."<br />");
$end = date("Y-m-d G:i:s", $end); //echo ("End: ".$end."<br /><br />");
	  
$result = mysql_query("SELECT `" . $field . "` FROM `" . $machine . "` 
		  WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'", $connection_hardware);
	  
// machine data processing
$val_temp = 0; $n = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$val_temp += $row[$field];
	$n++;
}

switch($operation) {
	case 'sum':
		$valTotal = $val_temp;
	break;
	case 'avg':
		if ($n) $valTotal = $val_temp / $n;
		else $valTotal = 0;
	break;
	case 'count':
		$valTotal = $n;
	break;
	default:
		$valTotal = $val_temp;
}

$valTotal = round($valTotal/$scale[$machine][$field], 2);

mysql_free_result ($result);

echo ($valTotal);

?>