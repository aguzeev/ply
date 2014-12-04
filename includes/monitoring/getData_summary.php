<?php

$_DEBUG = false;

if ($_DEBUG) echo "Operation: " . $operation . "<br>";


/* ===== Operations replace ===== */
	// Some operations are applicable only for getting divided values.
	// Let's change them to some others.
	//if ( $operation == 'exactVals' )	$operation = 'avg';
	//if ( $operation == 'asIs' )			$operation = 'avg';
/* ===== End of operations replace ===== */


switch ($operation) {
	case "sum":
		foreach ($fieldsArray as $value) $fieldsArrayMysql[] = 'SUM(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArrayMysql);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // wasn't corrected
			$returnValue = mysql_result($mainResult, 0);
		} else { // was corrected
			$returnValue = mysql_result($mainResult, 0);
			$returnValue += mysql_result($mainResult, 1);
		}
	break;
	case "avg": // may be several fields
	case "exactVals":
	case "asIs":
		$fieldsArraySum = array(); $fieldsArrayCount = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		foreach ($fieldsArray as $value) $fieldsArrayCount[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum) . ', ' . implode(' + ', $fieldsArrayCount);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if(($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // wasn't corrected
			list($sum, $count) = mysql_fetch_row($mainResult);
			$count > 0 ? $returnValue = $sum / $count : $returnValue = 0;
		} else { // was corrected
			list($sum1, $count1) = mysql_fetch_row($mainResult);
			list($sum2, $count2) = mysql_fetch_row($mainResult);
			($count1 + $count2) > 0 ? $returnValue = ($sum1 + $sum2) / ($count1 + $count2) : $returnValue = 0;
		}
	break;
	case "avg_diameter": // may be several fields
		$fieldsArraySum = array(); $fieldsArrayCount = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		foreach ($fieldsArray as $value) $fieldsArrayCount[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum) . ', ' . implode(' + ', $fieldsArrayCount);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			list($sum, $count) = mysql_fetch_row($mainResult);
			$sum = $sum * 2;
			$count > 0 ? $returnValue = $sum / $count : $returnValue = 0;
		} else { // use corrections
			list($sum1, $count1) = mysql_fetch_row($mainResult);
			list($sum2, $count2) = mysql_fetch_row($mainResult);
			$sum = ($sum1 + $sum2) * 2;
			($count1 + $count2) > 0 ? $returnValue = $sum / ($count1 + $count2) : $returnValue = 0;
		}
	break;
	case "avg_notzero": // may be several fields
		$fieldsArraySum = array(); $fieldsArrayCount = array(); $notNull = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		foreach ($fieldsArray as $value) $fieldsArrayCount[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum) . ', ' . implode(' + ', $fieldsArrayCount);
		foreach ($fieldsArray as $value) $notNull[] = '`' . $value . '` > 0';
		$notNullString = implode(' AND ', $notNull);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			list($sum, $count) = mysql_fetch_row($mainResult);
			if ($count > 0) $returnValue = $sum / $count;
				else $returnValue = 0;
		} else { // use corrections
			list($sum1, $count1) = mysql_fetch_row($mainResult);
			list($sum2, $count2) = mysql_fetch_row($mainResult);
			($count1 + $count2) > 0 ? $returnValue = ($sum1 + $sum2) / ($count1 + $count2) : $returnValue = 0;
		}
	break;
	case "avg_percent": // may be only 2 fields
		$fieldsArraySum = array();
		if ( sizeof($fieldsArray) != 2 ) { echo "Wrong number of fields for 'avg_percent' operation"; die(); }
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		$fieldsString = implode(', ', $fieldsArraySum);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			list($field_1, $field_2) = mysql_fetch_row($mainResult);
			if ($field_1 != 0) $returnValue = $field_2 / $field_1;
				else $returnValue = 0;
		} else { // use corrections
			list($field_11, $field_12) = mysql_fetch_row($mainResult);
			list($field_21, $field_22) = mysql_fetch_row($mainResult);
			$field_1 = $field_11 + $field_21;
			$field_2 = $field_12 + $field_22;
			
			if ($field_1 != 0) $returnValue = $field_2 / $field_1;
				else $returnValue = 0;
		}
	break;
	case "count": // may be several fields
		$fieldsArrayCount = array();
		foreach ($fieldsArray as $value) $fieldsArrayCount[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArrayCount);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0);
		} else { // use corrections
			$returnValue = mysql_result($mainResult, 0) + mysql_result($mainResult, 1);
		}
	break;
	case "countNotZero": // may be several fields
		$fieldsArrayCount = array(); $whereCondition = array();
		foreach ($fieldsArray as $value) $fieldsArrayCount[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArrayCount);
		
		foreach ($fieldsArray as $value) $whereCondition[] = '`' . $value . '` != 0';
		$whereString = ' AND ' . implode(' AND ', $whereCondition);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')" . $whereString;
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')" . $whereString . ")";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if (($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0);
		} else { // use corrections
			$returnValue = mysql_result($mainResult, 0) + mysql_result($mainResult, 1);
		}
	break;
	case "powerFromSquare": // may be several fields
		$fieldsArraySum = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		
		$thickness = 1.55; // default value
		$resultThickness = mysql_query("SELECT AVG(`thickness`) / 100000 FROM `raute_cutter`
			WHERE `timestamp` BETWEEN DATE_SUB('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR) AND DATE_ADD('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR)");
		$thickness = mysql_result($resultThickness, 0);
		mysql_free_result($resultThickness);
		
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if(($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0) * $thickness / ($date_end - $date_begin) * 60 * 60;
		} else { // use corrections
			$returnValue = (mysql_result($mainResult, 0) + mysql_result($mainResult, 1)) * $thickness / ($date_end - $date_begin) * 60 * 60;
		}
	break;
	case "valueFromSquare": // may be several fields
		$fieldsArraySum = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		$thickness = 1.55; // default value
		$resultThickness = mysql_query("SELECT AVG(`thickness`) / 100000 FROM `raute_cutter`
			WHERE `timestamp` BETWEEN DATE_SUB('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR) AND DATE_ADD('" . date("Y-m-d G:i:s", $date_end) . "', INTERVAL 6 HOUR)");
		$thickness = mysql_result($resultThickness, 0);
		if ( $thickness == 0 ) $thickness = 0.0016; // default value
		mysql_free_result($resultThickness);
		
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if(($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0) * $thickness;
		} else { // use corrections
			$returnValue = (mysql_result($mainResult, 0) + mysql_result($mainResult, 1)) * $thickness;
		}
	break;
	case "powerFromValue": // may be several fields
		$fieldsArraySum = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'SUM(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if(($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0) / ($date_end - $date_begin) * 60 * 60;
		} else { // use corrections
			$returnValue = (mysql_result($mainResult, 0) + mysql_result($mainResult, 1)) / ($date_end - $date_begin) * 60 * 60;
		}
	break;
	case "piecesPerMinute": // may be several fields
		$fieldsArraySum = array();
		foreach ($fieldsArray as $value) $fieldsArraySum[] = 'COUNT(`' . $value . '`)';
		$fieldsString = implode(' + ', $fieldsArraySum);
		
		$mainQuery = "SELECT " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow')";
		
		if ($use_corrections) {
			$machine_corrections = str_replace("_daily", "", $machine) . "_corrections";
			if ($use_daily_flag) {
				$machine_corrections .= "_daily";
			}
			$mainQuery .= " UNION ALL (SELECT " . $fieldsString . " FROM `" . $machine_corrections . "`WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow'))";
		}
		
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		else if(($num_rows = @mysql_num_rows($mainResult)) == 0) { $returnValue = 0; } // empty result
		else if (($num_rows = @mysql_num_rows($mainResult)) == 1) { // don't use corrections
			$returnValue = mysql_result($mainResult, 0) / ($date_end - $date_begin) * 60 * 60;
		} else { // use corrections
			$returnValue = (mysql_result($mainResult, 0) + mysql_result($mainResult, 1)) / ($date_end - $date_begin) * 60;
		}
	break;
	case 'active_time': // may be several fields
		foreach ($fieldsArray as $value) $fieldsArraySum[] = '`' . $value . '`';
		$fieldsString = implode(' + ', $fieldsArraySum);
		$mainQuery = "SELECT `timestamp`, " . $fieldsString . " FROM `" . $machine . "` WHERE `timestamp` BETWEEN CONVERT_TZ(FROM_UNIXTIME(" . $date_begin. "), 'SYSTEM', 'Europe/Moscow') AND CONVERT_TZ(FROM_UNIXTIME(" . $date_end . "), 'SYSTEM', 'Europe/Moscow') AND (". $fieldsString . ") > 0";
		if ($_DEBUG) echo "mainQuery: " . $mainQuery . "<br>";
		if ( ($mainResult = mysql_query($mainQuery, $connection_hardware)) == 0 ) {	die(mysql_error()); }
		
		$tstampArray = array();
		while ($row = mysql_fetch_assoc($mainResult)) {
			$tstamp = strtotime($row['timestamp']);
			$seconds = $tstamp % 60; // got the seconds
			$tstamp = $tstamp - $seconds; // set seconds to :00
			$tstampArray[(string)$tstamp] = 1;
		}
		if ($_DEBUG) echo count($tstampArray);
		$returnValue = round(count($tstampArray) / round(($date_end - $date_begin) / 60, 0) * 100, 0);
	break;
}
$returnValue = round( $returnValue / $value_scale, 2 );

// adding "wasCorrected" flag if data corrections were used
if ( ($num_rows = @mysql_num_rows($mainResult)) == 2 ) {
	if ( mysql_result($mainResult, 1) > 0 )
	$total_result['wasCorrected'] = true;
}

$total_result['payload'] = $returnValue;

//$total_result['query'] = $mainQuery;
//echo "$mainQuery<br>";

@mysql_free_result($mainResult);

?>