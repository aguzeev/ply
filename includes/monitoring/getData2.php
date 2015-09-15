<?php
// © Александр Гузеев (Alexander Guzeev), 2012—2013

if ( isset($_GET['debug']) ) $_DEBUG = true;

if ( isset($_IS_INCLUDED) ) {
	if ( $_IS_INCLUDED == true ) {
		// Nothing
	} else {
		require_once('../init.php');
		require_once('plotVariants.php');
		$use_daily_flag = false;
	}
} else {
	require_once('../init.php');
	require_once('plotVariants.php');
	$use_daily_flag = false;
}

$total_result = array();

if ( !isset( $divide ) ) {
	if ( isset($_GET['divide']) ) intval(mysql_real_escape_string($_GET['divide'])) == 0 ? $divide = false : $divide = true;
	else { $divide = false; };
}


if ( !isset($use_corrections) ) {
	if ( isset($_GET['use_corrections']) ) intval(mysql_real_escape_string($_GET['use_corrections'])) == 0 ? $use_corrections = false : $use_corrections = true;
	else { $divide == 1 ? $use_corrections = false : $use_corrections = true; };
}

if ( isset($plot_variant) or isset($_GET['plot_variant']) ) {
	if ( !isset($plot_variant) ) $plot_variant = mysql_real_escape_string($_GET['plot_variant']);
	
	
	$plot_variant = explode("-", $plot_variant);
	$machine_id = intval($plot_variant[0]);
	$machine = $plotVariants[$machine_id][$plot_variant[1]]['machine'];
	$fieldsArray = $plotVariants[$machine_id][$plot_variant[1]]['field'];
	$operation = $plotVariants[$machine_id][$plot_variant[1]]['operation'];
	
	if ( isset($plotVariants[$machine_id][$plot_variant[1]]['valueScale']) ) { $value_scale = $plotVariants[$machine_id][$plot_variant[1]]['valueScale']; }
		else { echo json_encode(array("result"=>"alert","text"=>"Не указан коэффициент пересчёта (value_scale), использовано значение 1","source"=>"getData (new)")); $value_scale = 1; }
	
	if ( isset($plotVariants[$machine_id][$plot_variant[1]]['valueDailyScale']) ) { $valueDailyScale = $plotVariants[$machine_id][$plot_variant[1]]['valueDailyScale']; }
		else { $valueDailyScale = 1; }
		
	$total_result['title'] = $plotVariants[ $plot_variant[0] ][ $plot_variant[1] ][ 'title' ];
	$total_result['machineTitle'] = $machineNames[ $plot_variant[0] ];
	
} else {
	
	if ( !isset($machine_id) ) {
		if ( isset($_GET['machine_id']) ) $machine_id = intval(mysql_real_escape_string($_GET['machine_id']));
		else { echo json_encode(array("result"=>"error","text"=>"Не указан ID станка (machine_id)","source"=>"getData (new)")); die(); };
	}
	$machine = $MACHINES_TABLENAMES[$machine_id + 1];
	
	
	if ( !isset($field_ids) ) {
		if ( isset($_GET['field_ids']) ) $field_ids = $_GET['field_ids'];
		else { echo json_encode(array("result"=>"error","text"=>"Не указаны ID полей (field_ids)","source"=>"getData (new)")); die(); };
	}
	$field_ids_array = explode(",", $field_ids);
	foreach ($field_ids_array as $value) $fieldsArray[] = $MACHINES_FIELDS[$machine][$value];
	
	
	if ( !isset($operation) ) {
		if ( isset($_GET['operation']) ) $operation = mysql_real_escape_string($_GET['operation']);
		else { echo json_encode(array("result"=>"error","text"=>"Не указана операция (operation)","source"=>"getData (new)")); die(); };
	}

	
	if ( !isset($value_scale) ) {
		if ( isset($_GET['value_scale']) ) $value_scale = intval(mysql_real_escape_string($_GET['value_scale']));
		else { echo json_encode(array("result"=>"alert","text"=>"Не указан коэффициент пересчёта (value_scale), использовано значение 1 1","source"=>"getData (new)")); $value_scale = 1; }
	}
	
	
	if ( !isset($value_daily_scale) ) {
		if ( isset($_GET['value_daily_scale']) ) $value_daily_scale = intval(mysql_real_escape_string($_GET['value_daily_scale']));
		else { $value_daily_scale = 1; }
	}
	
}


if ( !isset($date_begin) ) {
	if ( isset($_GET['date_begin']) ) {
		$date_begin = mysql_real_escape_string($_GET['date_begin']);
		if ( preg_match("/^\d\d?.\d\d?.\d\d\d\d\s\d\d?\:\d\d?(\:\d\d?)?$/", $date_begin, $matches_begin) ) {
			// got date in dd.mm.yyyy hh:mm format
			$date_begin = strtotime( $matches_begin[0] );
		} else {
			// got date in unixtime format
			$date_begin = intval( $date_begin );
		}
 	} else { echo json_encode(array("result"=>"error","text"=>"Не указана дата начала отсчёта (date_begin)","source"=>"getData (new)")); die(); };
}


if ( !isset($date_end) ) {
	if ( isset($_GET['date_end']) ) {
		$date_end = mysql_real_escape_string($_GET['date_end']);
		if ( preg_match("/^\d\d?.\d\d?.\d\d\d\d\s\d\d?\:\d\d?(\:\d\d?)?$/", $date_end, $matches_end) ) {
			// got date in dd.mm.yyyy hh:mm format
			$date_end = strtotime( $matches_end[0] );
		} else {
			// got date in unixtime format
			$date_end = intval( $date_end );
		}
 	} else { echo json_encode(array("result"=>"error","text"=>"Не указана дата окончания отсчёта (date_end)","source"=>"getData (new)")); die(); };
}


if ( $divide ) {
	if ( !isset($mx) ) {
		if ( isset($_GET['mx']) ) $mx = intval(mysql_real_escape_string($_GET['mx']) * 100) / 100;
		else { $total_result = array("result" => "alert", "text" => "Не указан коэффициент точности (mx), использовано значение 1", "source" => "getData (new)"); $mx = 1; };
	}
} else $mx = 1;


if ( !isset($callback_position) ) {
	if ( isset($_GET['callback_position']) ) {
		$callback_position = mysql_real_escape_string($_GET['callback_position']);
		$total_result['callback_position'] = $callback_position;
	}
}



if ( !isset($interval) ) {
	if ( isset($_GET['interval']) ) $interval = intval(mysql_real_escape_string($_GET['interval']));
	else { $interval = 0; };
}


// preparing dates
// see itit.php // $timezoneOffset = date("O") / 100 * 60 * 60;
//$date_begin = $date_begin - $timeDifference;
//$date_end = $date_end - $timeDifference;

/* =============== timezone adjustment begin ==================== */
//$date_begin = $date_begin + $timeDifference;
//$date_end = $date_end + $timeDifference;

//$timeDifferenceNew = intval(mysql_result(mysql_query("select timediff(now(), convert_tz(now(), @@session.time_zone, 'Europe/Moscow'))", $connection_stat), 0)) * 60 * 60;

date_default_timezone_set('Europe/Moscow');

if ( isset($_GET['debug']) ) $_DEBUG = true;
if ( $_DEBUG ) {
	echo "date_begin (original): " . date("Y-m-d G:i:s", $date_begin) . "<br>";
	echo "date_end (original): " . date("Y-m-d G:i:s", $date_end) . "<br>";
	echo "timeDifference: " . $timeDifference . "<br>";
	echo "Now: " . mysql_result(mysql_query("select now()"), 0) . "<br><br>";
	
	echo 'PHP: date_default_timezone_set: ' . date_default_timezone_get() . '<br><br>';
}

/* =============== timezone adjustment end ==================== */

$returnValue = -1;

if ( $divide ) include('getData_divided.php'); // summary values
else include('getData_summary.php'); // values for graph

$total_result["query"] = $mainQuery;

if ( $_IS_INCLUDED != true ) echo json_encode( $total_result );

?>