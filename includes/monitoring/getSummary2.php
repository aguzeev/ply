<?php

  require_once('../init.php');
  
  if(isset($_GET['machine'])) $machine = $_GET['machine'];
  if(isset($_GET['field'])) $field = $_GET['field'];
  if(isset($_GET['start'])) $startDate = $_GET['start'];
  if(isset($_GET['end'])) $endDate = $_GET['end'];
  if(isset($_GET['operation'])) $operation = $_GET['operation'];
  
  if ( isset($_GET['callback_position']) ) $callback_position = mysql_real_escape_string($_GET['callback_position']);

$wasCorrected = false;

/*$machine = 'raute_shell';
$field = 'curr_vol';
$startDate = '2012-10-05';
$endDate = '2012-10-12';
$operation = 'sum';*/

$scale = array(
	'raute_shell' => array(
		'curr_vol' => 1000000000,
		'percent_exit' => '10',
		'percent_core' => '10',
		'core_vol' => 1000000000,
		'r_init' => 10,
		'r_core' => 10,
		'id' => 1
	),
	'raute_cutter' => array(
		'bin1_incr' => 1000000000,
		'bin2_incr' => '1000000000',
		'bin3_incr' => '1000000000',
	),
	'boiler' => array(
		'temp_specified' => 10,
		'temp_send' => '10',
		'temp_receive' => '10',
		'power' => '10',
	),
	'warmer' => array(
		'packages' => 1,
		'velocity' => '16.66667',
		'sqLd' => '1',
		'totalSq' => '100'
	),
	'lopping' => array(
		'part_id' => 1,
		'value' => 1000000000,
		'velocity_short' => 1,
		'velocity_long' => 1,
	),
	'grinding' => array(
		'value_bin1' => 1000000000,
		'value_bin2' => 1000000000,
		'value_bin3' => 1000000000,
		'part_id' => 1,
		'velocity' => 1,
	),
	'merger' => array(
		'count' => 1,
		'length' => 1000,
		'width' => 1000,
		'press1_merg_count' => 1,
		'press1_cut_count' => 1,
		'press1_length' => 1000,
		'press2_merg_count' => 1,
		'press2_cut_count' => 1,
		'press2_length' => 1000,
		'press3_merg_count' => 1,
		'press3_cut_count' => 1,
		'press3_length' => 1000,
		'press1_merg_value' => 1000000000,
		'press2_merg_value' => 1000000000,
		'press3_merg_value' => 1000000000,
		'merg_total_value' => 1000000000,
	),
	'merger_old' => array(
		'count' => 1,
		'length' => 1000,
		'width' => 1000,
		'press1_merg_count' => 1,
		'press1_cut_count' => 1,
		'press1_length' => 1000,
		'press2_merg_count' => 1,
		'press2_cut_count' => 1,
		'press2_length' => 1000,
		'press3_merg_count' => 1,
		'press3_cut_count' => 1,
		'press3_length' => 1000,
		'press1_merg_value' => 1000000000,
		'press2_merg_value' => 1000000000,
		'press3_merg_value' => 1000000000,
		'merg_total_value' => 1000000000,
	),
	'saw' => array(
		'tree_format' => 1
	),
);


$beg = strtotime($startDate);
$end = strtotime($endDate);
$beg = date("Y-m-d G:i:s", $beg); //echo ("Begin: ".$beg."<br />");
$end = date("Y-m-d G:i:s", $end); //echo ("End: ".$end."<br /><br />");

$diff = strtotime($endDate) - strtotime($startDate);
if ($diff >= $maxDiffBeforeDaily) { // берём общие дневные значения
	if ($scaleForField > 100) $scaleForField = 1; // обобщённые дневные значения не надо переводить в другие единицы измерения
	//$machine = $machine . "_daily";
}

// several fields handling
$fieldString = '';
if (strpos($field, ',') > 0) {
	$field = str_replace(' ', '', $field);
	$fieldsArr = explode(',', $field);
	$scaleForField = 1;
	
	$fieldString = '`' . implode('`, `', $fieldsArr) . '`';
	
} else {
	$fieldString = '`' . $field . '`';
	$scaleForField = $scale[$machine][$field];
}

$result = mysql_query("(SELECT " . $fieldString . " FROM `" . $machine . "` 
		  WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "')
		  UNION ALL
		  (SELECT " . $fieldString . " FROM `" . $machine . "_corrections` 
		  WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "')
		  ", $connection_hardware);
		  
		  $checkCorrection = "SELECT " . $fieldString . " FROM `" . $machine . "_corrections` WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'";
		  if ( (mysql_num_rows(mysql_query($checkCorrection, $connection_hardware)) != 0) and (mysql_result(mysql_query($checkCorrection, $connection_hardware), 0) != 0) ) {
			  // значит, данные за этот период подвергались корректировке вручную
			  $wasCorrected = true;
		  }
	  
// machine data processing for different operations
$val_temp = 0; $n = 0;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$val_temp += $row[$field];
	$n++;
}
//для вычислений по нескольким полям нужно или суммировать значения для каждого поля, или брать среднее
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
	case 'avg_diameter':
		if ($n) $valTotal = $val_temp * 2 / $n;
		else $valTotal = 0;
	break;
	default:
		$valTotal = $val_temp;
}

$valTotal = round($valTotal/$scaleForField, 2);

mysql_free_result ($result);

if ($wasCorrected) $valTotal .= "*";

if (!isset($callback_position)) echo ($valTotal);
	else echo json_encode(array("payload" => $valTotal, "callback_position" => $callback_position));

?>