<?php
	require_once('includes/init.php');
	
	if(isset($_GET['machine'])) {
		$machine = $_GET['machine'];
	} else {
		die('Не выбран станок');
	};
	if(isset($_GET['unixtime'])) {
		$inUnixtime = true;
	}
	
	if ( intval($machine) != '' || $machine == '0') {
		// если передаётся id станка
		$machineId = intval($machine) + 1;
		$machineName = mysql_result(mysql_query("SELECT `table_name` FROM `machines` WHERE `id` = '" . $machineId . "' LIMIT 1"), 0);
		
	} else if ( (string) $machine != '' ) {
		// если передаётся название таблицы станка
		$machineId = mysql_result(mysql_query("SELECT `id` FROM `machines` WHERE `table_name` = '" . mysql_real_escape_string($machine) . "' LIMIT 1"), 0);
		$machineName = $machine;
	} else {
		die('Не удаётся определить станок');
	}
	//echo('$machineId = ' . $machineId . '<br>');
	//echo('$machineName = ' . $machineName . '<br>');
	
	//$lastTimestamp = mysql_result(mysql_query("SELECT `timestamp` FROM `" . $machineName . "` ORDER BY `timestamp` DESC LIMIT 1"), 0);
	$lastTimestamp = mysql_result(mysql_query("SELECT `timestamp` FROM `machine_state` WHERE `machine_id` = '" . $machineId . "' ORDER BY `timestamp` DESC LIMIT 1"), 0);
	if ($inUnixtime) {
		echo(date("U", strtotime($lastTimestamp)));
	} else {
		echo(date("d.m.y в G:i", strtotime($lastTimestamp)));
	}

?>