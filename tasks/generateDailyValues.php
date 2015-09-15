<?php


include "../includes/init.php";

$_DEBUG = true;

if ( $_DEBUG ) {
	ini_set('error_reporting', -1);
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);
}
 
// $startDateInit = intval(date('U', strtotime('01.03.2014 08:00')));
// $endDate = intval(date('U', strtotime('02.05.2014 08:00')));

$rewriteOld = true;
$machines = array(
	"raute_shell" => array(
		"r_init" => "avg",
		"r_cyl" => "avg",
		"r_core" => "avg",
		"percent_cyl" => "avg",
		"percent_core" => "avg",
		"percent_exit" => "avg",
		"curr_vol" => "sum",
		"core_vol" => "sum"
	),
	"raute_cutter" => array(
		"bin1_incr" => "sum",
		"bin2_incr" => "sum",
		"bin3_incr" => "sum",
		"bin1_quant" => "sum",
		"bin2_quant" => "sum",
		"bin3_quant" => "sum"
	),
	/*"boiler" => array(
		"temp_specified" => "avg",
		"temp_send" => "avg",
		"temp_receive" => "avg",
		"power" => "avg"
	),*/
	"warmer" => array(
		"packages" => "sum",
		"velocity" => "null",
		"sqLd" => "null",
		"totalSq" => "sum",
	),
	"warmer_out" => array(
		"usefull_square" => "sum",
		"usefull_quant" => "sum",
		"merger_square" => "sum",
		"merger_quant" => "sum",
		"useless_square" => "sum",
		"useless_quant" => "sum",
	),
	"lopping" => array(
		"length" => "null",
		"width" => "null",
		"velocity_short" => "null",
		"velocity_long" => "null",
		"value" => "sum",
		"thickness" => "null",
	),
	"grinding" => array(
		"length" => "null",
		"width" => "null",
		"thickness" => "null",
		"value_bin1" => "sum",
		"value_bin2" => "sum",
		"value_bin3" => "sum",
		"velocity" => "null",
	),
	"merger" => array(
		"count" => "sum",
		"length" => "null",
		"width" => "null",
		"press1_merg_count" => "null",
		"press1_cut_count" => "null",
		"press1_length" => "null",
		"press2_merg_count" => "null",
		"press2_cut_count" => "null",
		"press2_length" => "null",
		"press3_merg_count" => "null",
		"press3_cut_count" => "null",
		"press3_length" => "null",
		"press1_merg_value" => "sum",
		"press2_merg_value" => "sum",
		"press3_merg_value" => "sum",
		"merg_total_value" => "sum",
	),
	"merger_old" => array(
		"count" => "sum",
		"length" => "null",
		"width" => "null",
		"press1_merg_count" => "null",
		"press1_cut_count" => "null",
		"press1_length" => "null",
		"press2_merg_count" => "null",
		"press2_cut_count" => "null",
		"press2_length" => "null",
		"press3_merg_count" => "null",
		"press3_cut_count" => "null",
		"press3_length" => "null",
		"press1_merg_value" => "sum",
		"press2_merg_value" => "sum",
		"press3_merg_value" => "sum",
		"merg_total_value" => "sum",
	),
	/*
	"saw" => array(
		"tree_format" => "sum",
		"velocity" => "null",
		"sqLd" => "null",
		"totalSq" => "sum",
	),*/
	"press" => array(
		"pressure" => "null",
		"temp" => "null",
		"width" => "null",
		"length" => "null",
		"thickness" => "null",
		"quant" => "sum",
		"value" => "sum",
		"time_1" => "null",
		"time_2" => "null",
	),
);

$scaleOrig = array(
	'raute_shell' => array(
		'curr_vol' => 1000000000,
		'percent_exit' => '10',
		'percent_core' => '10',
		'core_vol' => 1000000000,
		'r_init' => 10,
		'r_core' => 10
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
		"totalSq" => 100
	),
	'warmer_out' => array(
		"usefull_square" => 10,
		"usefull_quant" => 1,
		"merger_square" => 10,
		"merger_quant" => 1,
		"useless_square" => 10,
		"useless_quant" => 1,
	),
	'warmer' => array(
		'packages' => 1,
		"totalSq" => 100
	),
	"lopping" => array(
		"value" => "1000000000",
	),
	"grinding" => array(
		"value_bin1" => "1000000000",
		"value_bin2" => "1000000000",
		"value_bin3" => "1000000000",
	),
	"merger" => array(
		"count" => "1",
		"press1_merg_value" => "1000000000",
		"press2_merg_value" => "1000000000",
		"press3_merg_value" => "1000000000",
		"merg_total_value" => "1000000000",
	),
	"merger_old" => array(
		"count" => "1",
		"press1_merg_value" => "1000000000",
		"press2_merg_value" => "1000000000",
		"press3_merg_value" => "1000000000",
		"merg_total_value" => "1000000000",
	),
	"press" => array(
		"quant" => "1",
		"value" => "1000000000",
	),
);
$scalePerMonth = array(
	'raute_shell' => array(
		'curr_vol' => 100,
		'percent_exit' => '1',
		'percent_core' => '1',
		'core_vol' => 100,
		'r_init' => 1,
		'r_core' => 1
	),
	'raute_cutter' => array(
		'bin1_incr' => 100,
		'bin2_incr' => '100',
		'bin3_incr' => '100',
	),
	'boiler' => array(
		'temp_specified' => 1,
		'temp_send' => '1',
		'temp_receive' => '1',
		'power' => '1',
	),
	'warmer' => array(
		'packages' => 1,
		'totalSq' => 10
	),
	'warmer_out' => array(
		"usefull_square" => 10,
		"usefull_quant" => 1,
		"merger_square" => 10,
		"merger_quant" => 1,
		"useless_square" => 10,
		"useless_quant" => 1
	),
	"lopping" => array(
		"value" => "100",
	),
	"grinding" => array(
		"value_bin1" => "100",
		"value_bin2" => "100",
		"value_bin3" => "100",
	),
	"merger" => array(
		"count" => "1",
		"press1_merg_value" => "100",
		"press2_merg_value" => "100",
		"press3_merg_value" => "100",
		"merg_total_value" => "100",
	),
	"merger_old" => array(
		"count" => "1",
		"press1_merg_value" => "100",
		"press2_merg_value" => "100",
		"press3_merg_value" => "100",
		"merg_total_value" => "100",
	),
	"press" => array(
		"value" => "100",
		"quant" => "1",
	),
);


foreach ($machines as $machine => $fields) {
	// searching for previous calculations
	$sqlCheckDate = "SELECT `timestamp` FROM `" . $machine . "_daily` ORDER BY `timestamp` DESC LIMIT 1";
	$result = mysql_query($sqlCheckDate, $connection_hardware);
	
	if ( mysql_num_rows($result) > 0 ) {
		$startDateInit = date("U", strtotime(mysql_result( $result, 0 )));
	} else {
		echo "Using default date for" . $machine;
		$startDateInit = date("U", strtotime("2012-01-01 00:00"));
	}
	if ( $_DEBUG ) echo "startDateInit: " . date("Y-m-d G:i:s", $startDateInit) . "<br>";
	
	if ( $startDateInit < (date("U") - 86400) ) {
		$endDate = date("U") + 86400;
		if ( $_DEBUG ) echo "endDate: " . date("Y-m-d G:i:s", $endDate) . "<br>";
	
		$machineVariants = array($machine, $machine . "_corrections");
		foreach ($machineVariants as $machVariant) {
			if ($rewriteOld) {
				$startDate = $startDateInit;
				$queryDelete = "DELETE FROM `" . $machVariant . "_daily` WHERE `timestamp` BETWEEN '" . date("Y-m-d G:i:s", $startDateInit) . "' AND '" . date("Y-m-d G:i:s", $endDate + 60 * 60 * 4) . "'"; // endDate prolongs to 12:00
				$resultDelete = mysql_query($queryDelete) or die(mysql_error());
			} else {		
				$resultLastTimestamp = mysql_query("SELECT `timestamp` FROM `".$machVariant."_daily` ORDER BY `timestamp` DESC");
				if (mysql_num_rows($resultLastTimestamp)) $lastTimestamp = mysql_result($resultLastTimestamp, 0);
					else $lastTimestamp = '2012-01-01';
				$startDate = max(date('U', strtotime($lastTimestamp)), date('U', strtotime($startDateInit)));
				mysql_free_result($resultLastTimestamp);
			}
			
			
			//$startDate = $startDate + 86400; // если уверены в том, что за предыдущий день учтены ВСЕ показатели
			
			$currDay = $startDate;
			while ($currDay < $endDate) {
				$nextDay = min($currDay + 86400, $endDate);
				$activeField = array();
				$query = "SELECT ";
				foreach ($fields as $field => $operation) {
					if (isset($scaleOrig[str_replace("_corrections", "", $machVariant)][$field])) $dividor = $scaleOrig[str_replace("_corrections", "", $machine)][$field] / $scalePerMonth[str_replace("_corrections", "", $machVariant)][$field]; else $dividor = 1;
					switch ($operation) {
						case "avg":
							$query = $query . "AVG(" . $field . ") / " . $dividor . ", ";
							$activeField[] = $field; // поля для запроса
							break;
						case "sum":
							$query = $query . "SUM(" . $field . ") / " . $dividor . ", ";
							$activeField[] = $field; // поля для запроса
							break;
						case "null": // do nothing;
						default: // do nothing;
					}
				}
				$query = substr($query, 0, strlen($query) - 2); // убираем последнюю запятую
				$query = $query . " FROM `" . $machVariant . "` WHERE `timestamp` BETWEEN '" . date("Y-m-d G:i:s", $currDay) . "' AND '" . date("Y-m-d G:i:s", ($nextDay - 1)) . "'";
				//echo $query;
				$result = mysql_query($query) or die(mysql_error());
				$resValues = mysql_fetch_array($result, MYSQL_ASSOC);
				
				foreach ($resValues as $index => $value) {
					if ($_DEBUG) echo $resValues[$index] . ", after round: ";
					$resValues[$index] = round($value, 2);
					if ($_DEBUG) echo $resValues[$index] . "<br>";
				}
				
				$fieldsStr = implode(', ', $activeField);
				$valuesStr = implode(', ', $resValues);
				// Запись полученных значений
				
				$tstamp = date("Y-m-d", $currDay) . " 12:00";
				$queryInsert = "INSERT INTO `" . $machVariant . "_daily` (timestamp, " . $fieldsStr . ") VALUES ('" . $tstamp . "', " . $valuesStr . ")";
				echo $machVariant . " " . date("d-m-Y", $currDay) . ": ok<br>";
				mysql_query($queryInsert) or die(mysql_error());
				mysql_free_result($result);
				
				$currDay = $nextDay;
			}
		}
	} else {
		echo("Too early to generate for " . $machine . "<br>");
	}
	if ( $_DEBUG ) echo "end for $machine<br><br>";
}

/*$currDay = $startDate;
$i = 0;
while ($currDay <= $endDate) {
	// Вычисление дневных значений
	$nextDay = $currDay + 86400;
	$query = "SELECT SUM(`curr_vol` / 100000000) FROM `raute_shell` WHERE `timestamp` BETWEEN '" . date("Y-m-d G:i:s", $currDay) . "' AND '" . date("Y-m-d G:i:s", ($nextDay - 1)) . "'";
	$result = mysql_query($query) or die(mysql_error());
	$dailyVal[] = mysql_result($result, 0);
	mysql_free_result($result);
	echo $dailyVal[$i] . '<br>';
	
	// Запись полученых значений
	$query = "SELECT `id` FROM `raute_shell_daily` WHERE `timestamp` = '" . date("Y-m-d", $currDay) . "' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	if ( !mysql_num_rows($result) ) {
		// На этот день значение ещё не записано
		$query = "INSERT INTO `raute_shell_daily` (`id`, `curr_vol`, `timestamp`) VALUES (null, '" . $dailyVal[$i] . "', '" . date("Y-m-d", $currDay) . "')";
		echo $query  . '<br />';
		mysql_query($query) or die(mysql_error());
	} else {
		// На этот день значение уже есть
		$query = "UPDATE `raute_shell_daily` SET `curr_vol` = '" . $dailyVal[$i] . "' WHERE `id` = '" . mysql_result($result, 0) . "' LIMIT 1";
		mysql_query($query) or die(mysql_error());
	}
	mysql_free_result($result);
	
	$currDay = $nextDay;
	$i++;
}

echo "All done";
*/

?>