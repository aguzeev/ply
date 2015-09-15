<?php

if ( isset($_GET['debug']) ) $_DEBUG = true;

if ($interval == 0) {
	// determining accuracy
	$diff = $date_end - $date_begin;
	
	if ($diff >= 60 * 60 * 24 * 60) { // >= 60 days
		$use_daily_flag = true;
		$machine = $machine . "_daily"; // switching to daily table
		$interval = 60 * 60 * 24;
		// round dates to nearest prev day beginning
		//$date_begin = $date_begin - ($date_begin / 86400); // 86400 = 1 day
		//$date_end = $date_end - ($date_end / 86400); // 86400 = 1 day
		$value_scale = $valueDailyScale; if ($_DEBUG) { echo "valueDailyScale: " . $valueDailyScale . ", value_scale: " . $value_scale . "<br>"; }
		
		$total_result['daily'] = 'true'; if ($_DEBUG) echo "Using daily values<br>";
	} else if ($diff >= 60 * 60 * 24 * 15) { // >= 15 days
		$interval = $diff / 15 / 6;
	} else if ($diff >= 60 * 60 * 24 * 7) { // >= 7 days
		$interval = $diff / 12 / 7;
	} else if ($diff >= 60 * 60 * 24) { // >= 1 day
		$interval = $diff / 4 / 24;
	} else if ($diff >= 60 * 60 * 12) { // >=12 hours
		$interval = 60 * 10;
	} else if ($diff >= 60 * 60 * 6) { // >=6 hours
		$interval = 60 * 5;
	} else if ($diff >= 60 * 60 * 3) { // >=3 hours
		$interval = 60 * 3;
	} else { // < 3 hours
		$interval = 60;
	}
	$interval = $interval / $mx;
}

//$time_value_arr[] = array("2013-04-20 8:00:00", 1);

foreach ($fieldsArray as $value) $fieldsArrayMysql[] = '`' . $value . '`';
$fieldsString = implode(', ', $fieldsArrayMysql);
//$mainQuery = "SELECT `timestamp`, " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN STR_TO_DATE('" . date("Y-m-d G:i:s", $date_begin) . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date("Y-m-d G:i:s", $date_end) . "', '%Y-%m-%d %H:%i:%s')";

$mainQuery = "SELECT `timestamp`, " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', '+03:00') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', '+03:00')";

if ($use_corrections) {
	$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
	if ($use_daily_flag) {
		$machine_corrections .= "_daily";
	}
	
	$mainQuery .= " UNION ALL (SELECT `timestamp`, " . $fieldsString . " FROM `" . $machine_corrections . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', '+03:00') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', '+03:00'))";
}
$mainQuery .= " ORDER BY `timestamp`";
if ($_DEBUG) { echo "mainQuery: " . $mainQuery . "<br>"; }

if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
else if(($num_rows = @mysql_num_rows($mainResult)) == 0) {  }
else {
	$MYSQLresult_arr = array();
	while( $row = mysql_fetch_assoc($mainResult) ) {
		$resultFieldsArray = array();
		$resultFieldsArray[] = date("U", strtotime($row['timestamp']));
		if ($_DEBUG) { echo "row[timestamp]: " . $row['timestamp'] . ", to Unixtime: " . date("U", strtotime($row['timestamp'])) . "<br>";  }
		foreach ($fieldsArray as $value) {
			$resultFieldsArray[] = $row[$value];
			//echo $fieldsArray[1] . "<br>";
		}
		$MYSQLresult_arr[] = $resultFieldsArray;
	}
}
//print_r($MYSQLresult_arr); echo "<br>";

if ($operation == "powerFromSquare" or $operation == "valueFromSquare") {
	$resultThickness = mysql_query("SELECT AVG(`thickness`) / 100000 FROM `raute_cutter` WHERE `timestamp` BETWEEN DATE_SUB('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR) AND DATE_ADD('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR)");
	$thickness = mysql_result($resultThickness, 0);
	if ( $thickness == 0 ) $thickness = 0.0016; // default value
	mysql_free_result($resultThickness);
}



$index = 0; $delta = 0; $returnValue = array(); $arrCount = count($MYSQLresult_arr); $epochValues = array(); $prevValue = 0; $exactValsCounter = 0; $isLast = false; $currReturnIndex = 0;
$fieldsNum = sizeof($MYSQLresult_arr[0]); // count of valuable fields in mysql result


/* ===== exactVals processing ===== */
if ($operation == 'exactVals') {
	if ( sizeof($fieldsArray) != 1 ) { // may be only 1 field
		echo "Wrong number of fields for 'exactVals' operation (" . sizeof($fieldsArray) . ", but must be 1)"; die();
	}
	$maxCount = $fieldsNum = sizeof($MYSQLresult_arr);
	$returnValueExact = array();
	$diffArray = array();
	$prevValue = 0; $lastTimestamp = $date_begin;
	
	$prevValue = $MYSQLresult_arr[$exactValsCounter][1];
	$returnValueExact[] = array(date("Y-m-d G:i:s", $MYSQLresult_arr[$exactValsCounter][0]), round($prevValue / $value_scale, 2));
	
	// calculating difference array
	for ($i = 1; $i < $fieldsNum - 1; $i++) {
		if ( $MYSQLresult_arr[$i][1] != $prevValue ) {
			$diffArray[] = array($MYSQLresult_arr[$i][0], $MYSQLresult_arr[$i][1] - $prevValue);
			$prevValue = $MYSQLresult_arr[$i][1];
		}
	}
	if ($_DEBUG) { print_r($diffArray); echo "<br>"; }
	
	if ( sizeof($diffArray) > 0 ) {
		foreach ( $diffArray as $diff ) {
			$prevReturnValue = $returnValueExact[sizeof($returnValueExact)-1][1];
			$returnValueExact[] = array(date("Y-m-d G:i:s", $diff[0] - $interval/4), $prevReturnValue);
			$returnValueExact[] = array(date("Y-m-d G:i:s", $diff[0] + $interval/4), $prevReturnValue + round($diff[1]) / $value_scale, 2);
		}
	}
	
	$returnValueExact[] = array(date("Y-m-d G:i:s", $MYSQLresult_arr[$fieldsNum - 1][0]), round($MYSQLresult_arr[$fieldsNum - 1][1] / $value_scale, 2));
	if ($_DEBUG) { print_r($returnValueExact); echo "<br>"; }
}

/* ===== asIs processing ===== */
else if ($operation == 'asIs') {
	if ( sizeof($fieldsArray) != 1 ) { // may be only 1 field
		echo "Wrong number of fields for 'asIs' operation (" . sizeof($fieldsArray) . ", but must be 1)"; die();
	}
	//$returnValueAsIs = $MYSQLresult_arr;
	
	if ( sizeof($MYSQLresult_arr) == 0 ) {
		$returnValueAsIs[] = array(date("Y-m-d G:i:s", $date_begin), 0);
		$returnValueAsIs[] = array(date("Y-m-d G:i:s", $date_end), 0);
	} else {
		foreach ($MYSQLresult_arr as $value) {
			if ( $value[1] > 0 )
				$returnValueAsIs[] = array( date("Y-m-d G:i:s", $value[0]), round($value[1] / $value_scale, 2) );
		}
	}
}

/* ===== Ususal processing ===== */
else {
	for ($begin = $date_begin; $begin < $date_end; $begin += $interval) {
		$end = min($begin + $interval - 1, $date_end);
		$tstamp = date("Y-m-d G:i", $begin + $interval);
		
		$returnValue[$currReturnIndex] = array($tstamp, 0);
		
		$currEpochValues = array();
		while ( ($index < $arrCount) && ($MYSQLresult_arr[$index][0] < $end) ) {
			$valuableFields = array();
			for ($i = 1; $i < $fieldsNum; $i++) { $valuableFields[] = $MYSQLresult_arr[$index][$i]; }
			if ($operation != 'exactVals' && $operation != 'active_time' && $operation != 'asIs') { $currEpochValues[] = $valuableFields; }
			else { $currEpochValues[] = array( date("Y-m-d G:i:s", $MYSQLresult_arr[$index][0]) => $valuableFields); } // need to keep the timestamp
			$index++;
		}
		$epochValue = 0;
		switch ($operation) {
			case "sum": // may be several fields
				$returnValue[$currReturnIndex] = array( $tstamp, round(array_recirsive_sum($currEpochValues) / $value_scale, 2) );
			break;
			case "avg": // may be several fields
				$sum = array_recirsive_sum($currEpochValues);
				$count = sizeof($currEpochValues) * ($fieldsNum - 1); // 1 is for timestamp
				if ($count) $returnValue[$currReturnIndex] = array( $tstamp, round($sum / $count / $value_scale, 2) );
					else $returnValue[$currReturnIndex] = array( $tstamp, 0 );
			break;
			case "avg_diameter": // may be several fields
				$sum = array_recirsive_sum($currEpochValues) * 2;
				$count = sizeof($currEpochValues) * ($fieldsNum - 1); // 1 is for timestamp
				if ($count) $returnValue[$currReturnIndex] = array( $tstamp, round($sum / $count / $value_scale, 2) );
					else $returnValue[$currReturnIndex] = array( $tstamp, 0 );
			break;
			case "avg_notzero": // may be several fields
				$sum = array_recirsive_sum($currEpochValues);
				$count = array_recirsive_count_notzero($currEpochValues);
				if ($count) $returnValue[$currReturnIndex] = array( $tstamp, round($sum / $count / $value_scale, 2) );
					else $returnValue[$currReturnIndex] = array( $tstamp, 0 );
			break;
			case "avg_percent": // may be only 2 fields
				if ( sizeof($fieldsArray) != 2 ) { echo "Wrong number of fields for 'avg_percent' operation"; die(); }
				$sum_1 = 0; $sum_2 = 0;
				foreach ($currEpochValues as $value) {
					$sum_1 += $value[0]; $sum_2 += $value[1];
				}
				if ($sum_1) $returnValue[$currReturnIndex] = array( $tstamp, round($sum_2 / $sum_1 / $value_scale, 2) );
					else $returnValue[$currReturnIndex] = array( $tstamp, 0 );
			break;
			case "exactVals":
				$returnValue = array(); // see beginning of the file
			break;
			case "asIs":
				$returnValue = array(); // see beginning of the file
			break;
			case "count": // may be several fields
				$count = sizeof($currEpochValues) * ($fieldsNum - 1); // 1 is for timestamp
				$returnValue[$currReturnIndex] = array( $tstamp, round($count / $value_scale, 2) );
			break;
			case "countNotZero": // may be several fields
				$count = array_recirsive_count_notzero($currEpochValues);
				$returnValue[$currReturnIndex] = array( $tstamp, round($count / $value_scale, 2) );
			break;
			case "powerFromSquare": // may be several fields
				$returnValue[$currReturnIndex] = array( $tstamp, round(array_recirsive_sum($currEpochValues) / $value_scale * $thickness / $interval*60*60, 2) );
			break;
			case "valueFromSquare": // may be several fields
				$returnValue[$currReturnIndex] = array( $tstamp, round(array_recirsive_sum($currEpochValues) / $value_scale * $thickness, 2) );
			break;
			case "powerFromValue": // may be several fields
				$returnValue[$currReturnIndex] = array( $tstamp, round(array_recirsive_sum($currEpochValues) / $value_scale / $interval*60*60, 2) );
			break;
			case "piecesPerMinute": // may be several fields
				$returnValue[$currReturnIndex] = array( $tstamp, round(sizeof($currEpochValues) / $value_scale / $interval*60, 2) );
			break;
			case 'active_time': // may be several fields
				$minuteActivities = array();
				foreach ($currEpochValues as $key => $singleValue) {
					foreach ($singleValue as $time => $value) {
						$time = date("Y-m-d G:i:s", strtotime($time));
						$minuteActivities[$time] = 1;
					}
				}
				$returnValue[$currReturnIndex] = array( $tstamp, sizeof($minuteActivities) );
			break;
		}
		
		$currReturnIndex++;
		
		//$epochValues[$tstamp] = $currEpochValues;
		if ($_DEBUG) { echo "<br>currEpochValues: "; print_r($currEpochValues); echo "<br>"; }
		if ($_DEBUG) { echo "epochValue: $epochValue<br>"; }
		if ($_DEBUG) { echo "returnValue: "; print_r($returnValue); echo "<br>"; }
	}
}
if ($_DEBUG) { echo "epochValues: "; print_r($epochValues); echo "<br>"; }
if ($operation == 'exactVals') { $returnValue = $returnValueExact; }
if ($operation == 'asIs') { $returnValue = $returnValueAsIs; }

$total_result['payload'] = $returnValue;


// determining maxinim
$maxValue = -999; $maxValueAt = "0000-00-00 00:00";
foreach( $returnValue as $value ) {
	if ( $maxValue < $value[1] ) {
		$maxValueAt = $value[0];
		$maxValue = $value[1];
	}
}
$total_result['maximum'] = $maxValue;
$total_result['maximumAt'] = $maxValueAt;

mysql_free_result($mainResult);

?>