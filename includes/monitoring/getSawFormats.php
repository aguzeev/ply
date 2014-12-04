<?php

require_once('../init.php');

$total_result = array();

if(isset($_GET['start'])) $startDate = $_GET['start'];
if(isset($_GET['end'])) $endDate = $_GET['end'];

//$startDate = '2013-02-21 00:00:00';
//$endDate = '2013-02-23 00:00:00';

$beg = strtotime($startDate);
$end = strtotime($endDate);
$beg = date("Y-m-d G:i:s", $beg); //echo ("Begin: ".$beg."<br />");
$end = date("Y-m-d G:i:s", $end); //echo ("End: ".$end."<br /><br />");

$diff = strtotime($endDate) - strtotime($startDate);
if ($diff >= 60 * 60 * 24 * 60) { // берём общие дневные значения
	$total_result['result'] = 'alert';
	$total_result['text'] = 'Слишком большой период для подсчёта точных сведений о форматах. Данные о форматах доступны для периодов не длиннее 60 дней.';
	die();
}

	  
$resultFormats = mysql_query("SELECT tree_format FROM saw 
		  WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'");

$formatsArr = array();
while ($row = mysql_fetch_array($resultFormats, MYSQL_ASSOC)) {
	$curFormat = (string)$row['tree_format'];
	if ( !isset($formatsArr[$curFormat]) ) $formatsArr[$curFormat] = 0;
	$formatsArr[$curFormat]++;
}
//print_r($formatsArr);
ksort($formatsArr);

mysql_free_result($resultFormats);

$total_result = array();

if (count($formatsArr)) {
	$totalSmallTrees = 0;
	$total_result['result'] = "ok";
	foreach ($formatsArr as $key => $value) {
		switch ($key) {
			case 49:
				$total_result['tree49'] = $value;
				$totalSmallTrees += 3 * $value;
			break;
			case 54:
				$total_result['tree54'] = $value;
				$totalSmallTrees += 4 * $value;
			break;
			case 66:
				$total_result['tree66'] = $value;
				$totalSmallTrees += 5 * $value;
			break;
	  }
	}
	
	$total_result['total'] = $totalSmallTrees;
}
echo json_encode($total_result);

?>