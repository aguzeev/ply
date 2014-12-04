<?php

include "../includes/init.php";
include "../includes/monitoring/plotVariants.php";
date_default_timezone_set('Europe/Moscow');
$_DEBUG = false;
if ( $_DEBUG ) {
	ini_set('error_reporting', -1);
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);
}

?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Поиск самых эффективных смен</title>
</head>

<body>-->

<?php

//foreach ( $leaderShiftsVariants as $monthVar ) {
	if ( isset($_GET['monthVar']) ) $monthVar = $_GET['monthVar'];
	
	$_DEBUG = true;
	// each iteration needs a flush
	$fieldsArrayMysql = "";
	$total_result = array();
	$total_result['maximum'] = 0;
	$prevMax = 0;
	$maxValue = 0;
	$maxValueAt = 0;
	$value = 0;
	$currEpochValues = array();


	$monthVarArray = explode("-", $monthVar);
	$machineN = $monthVarArray[0];
	$variant = $monthVarArray[1];
	
	$machine = $plotVariants[$machineN][$variant]['machine'];
	if ( $_DEBUG ) echo "Searching for prev max for " . $machine . "<br>";
	
	$sqlCheckDate = "SELECT `value`, `checktime`, `timestamp` FROM `maxes` WHERE `machine` = '" . $machine . "' ORDER BY `checktime` DESC LIMIT 1";
	$result = mysql_query($sqlCheckDate, $connection_hardware);
	
	if ( mysql_num_rows($result) > 0 ) {
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$prevMax = $row['value'];
		$prevMaxAt = $row['timestamp'];
		$prevChecktime = $row['checktime'];
		if ( strtotime($prevChecktime) < strtotime("2012-01-01 00:00") ) $prevChecktime = "2012-01-01 00:00";
	} else {
		$prevChecktime = "2012-01-01 00:00";
		$prevMax = 0;
	}

	$hour = date( "G", strtotime($prevChecktime) );
	while ( $hour % 8 != 0 ) {
		$hour--;
		if ( $hour < 0 ) { $return['result']  = "error"; $return['text']  = "Stop while adjusting previous shift's end."; die(); }
	}
	$prevCheckedShift = date( "Y-m-d", strtotime($prevChecktime) ) . " " . $hour . ":00";
	
	
	if ( date("U") - date("U", strtotime($prevCheckedShift)) > 60 * 60 * 24 * 30 ) {
		$periodEnd = date("Y-m-d G:i:s", date("U", strtotime($prevCheckedShift)) + 60 * 60 * 24 * 30 );
	} else $periodEnd = date("Y-m-d G:i:s");
	

	$hour = date( "G", strtotime($periodEnd) );
	while ( $hour % 8 != 0 ) {
		$hour--;
		if ( $hour < 0 ) { $return['result']  = "error"; $return['text']  = "Stop while adjusting current shift's end."; die(); }
	}
	$periodEnd = date( "Y-m-d", strtotime($periodEnd) ) . " " . $hour . ":00";
	/* ===== prevCheckedShift is the last finifhed shift end ===== */
	/* ===== */ if ($_DEBUG) echo $machine . ": " . $prevCheckedShift . " – " . $periodEnd . "<br>";
	
	
	$plot_variant = $monthVar;
	echo "<br>plot variant: " . $monthVar . "<br>";
	$date_begin = date("U", strtotime($prevCheckedShift));
	$date_end = date("U", strtotime($periodEnd));
	$divide = 1;
	$mx = 1;
	$interval = 60 * 60 * 8;
	$use_corrections = 0;
	$use_daily_flag = false;
	$_IS_INCLUDED = true;

	$_DEBUG = false;
	include("../includes/monitoring/getData2.php");
	
	echo "maximum for " . $machine . ": " . $total_result['maximum'] . " at " . $total_result['maximumAt'] . "<br>";
	echo "previous maximum – " . $prevMax . " at " . $prevMaxAt . "<br><br>";
	
	if ( $total_result['maximum'] > $prevMax ) {
		$sqlUpdate = "UPDATE `maxes` SET
		`value` =  '" . $total_result['maximum'] . "', 
		`timestamp` =  '" . $total_result['maximumAt'] . "', 
		`checktime` =  '" . $periodEnd . "'
		WHERE  `machine` =  '" . $machine . "' LIMIT 1";
		mysql_query($sqlUpdate);
	} else {
		$sqlUpdate = "UPDATE `maxes` SET
		`checktime` =  '" . $periodEnd . "'
		WHERE  `machine` =  '" . $machine . "' LIMIT 1";
		mysql_query($sqlUpdate);
	}
//}

?>
<!--</body>
</html>-->