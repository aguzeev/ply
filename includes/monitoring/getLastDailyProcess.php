<?php

if ( true ) {
	ini_set('error_reporting', -1);
	ini_set('display_errors', 1);
	ini_set('html_errors', 1);
}

date_default_timezone_set('Europe/Moscow');

if ( filesize("last_daily_process.txt") > 3 ) {
	$dailyFile = fopen("last_daily_process.txt", "r");
	$lastDailyDate = fread($dailyFile, 50);
	
	if ( date("U", strtotime($lastDailyDate)) < date("U") - (8 + 24) * 60 * 60 ) {
		$newDate = date( "d.m.Y", date("U") - 8 * 60 * 60 );
		fclose($dailyFile);
		$dailyFile = fopen("last_daily_process.txt", "w");
		echo json_encode( array("calculate" => 1, "lastDate" => $lastDailyDate, "newDate" => $newDate) );
		fwrite($dailyFile, $newDate);
	} else {
		echo json_encode( array("calculate" => 0, "lastDate" => $lastDailyDate) );
	}
	fclose($dailyFile);
}

?>