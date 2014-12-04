<?php
	require_once('../init.php');
	
	// машины в порядке следования на сайте и поля для проверки на ненулевые значения (можно оставить пустым)
	$machineNames = array(
		'raute_shell' => 'curr_vol',
		'raute_cutter' => 'bin1_incr',
		'boiler' => 'temp_specified',
		'warmer' => 'packages',
		'lopping' => 'value'
	);
	
	$aliveBatch = array();
	foreach ($machineNames as $mach => $field) {
		$alive = false;
		
		// проверяем на последний timestamp
		$resultTimestamp = mysql_result(mysql_query("SELECT `timestamp` FROM `" . $mach . "` ORDER BY `timestamp` DESC LIMIT 1", $connection_hardware), 0);
		$lastTimestamp = date("U", strtotime( $resultTimestamp ));
		$currTimestamp = date("U");
		
		// если timestamp недавний, проверяем значения (чтобы не были равны нулю)
		if ($currTimestamp - $lastTimestamp < 60*15) {
			if ($field == '') {
				$alive = true; // на нулевые значения не проверяем
			} else {
				$resultValues = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $mach . "` WHERE `" . $field . "` != 0 AND `timestamp` BETWEEN DATE_SUB(CURRENT_TIMESTAMP(), INTERVAL 30 MINUTE) AND CURRENT_TIMESTAMP()", $connection_hardware), 0);
				if ($resultValues > 0) $alive = true;
			}
		}
		$aliveBatch[] = $alive;
	}
	echo(json_encode($aliveBatch));

?>