<?php

require_once('../init.php');
$ACCESSED_MODULE = 0; // everyone has access
$_LOGGING = false;
include('../cerber.php');

if ( isset($_GET['database_id']) ) $database_id = mysql_real_escape_string($_GET['database_id']);
else { echo json_encode(array("result"=>"error","text"=>"Не указан ID виджета")); die(); };
if ( isset($_GET['order']) ) $order = mysql_real_escape_string($_GET['order']);
else { $order = 'last'; };

if ($order != 'remove') {
	// если не собираемся удалять виджет, то нужны дополнительные параметры
	if ( isset($_GET['machine']) ) $machine = intval(mysql_real_escape_string($_GET['machine']));
	else { echo json_encode(array("result"=>"error","text"=>"Не указан станок для виджета")); die(); };
	if ( isset($_GET['variant']) ) $variant = intval(mysql_real_escape_string($_GET['variant']));
	else { echo json_encode(array("result"=>"error","text"=>"Не указан параметр станка для виджета")); die(); };
	if ( isset($_GET['period']) ) $period = mysql_real_escape_string($_GET['period']);
	else { echo json_encode(array("result"=>"error","text"=>"Не указан период")); die(); };
}

if ($database_id == 'new') {
	// добавляем новый виджет
		// echo "<br> SELECT `order` FROM `control_pannel_presets` WHERE `user_id` = '" . $_SESSION['v2_user_id'] . "' ORDER BY `order` DESC LIMIT 1<br>";
		if (mysql_result(mysql_query("SELECT COUNT(`order`) FROM `control_pannel_presets` WHERE `user_id` = '" . $_SESSION['v2_user_id'] . "' ORDER BY `order` DESC LIMIT 1", $connection_stat), 0) == 0) $order = 0;
		else $order = mysql_result(mysql_query("SELECT `order` FROM `control_pannel_presets` WHERE `user_id` = '" . $_SESSION['v2_user_id'] . "' ORDER BY `order` DESC LIMIT 1", $connection_stat), 0) + 1;
		// echo "order = " . $order . "<br>";
		
		$sqlCP = "INSERT INTO `control_pannel_presets` (`id`, `user_id`, `order`, `machine`, `variant`, `period`) VALUES (NULL, '" . $_SESSION['v2_user_id'] . "', '" . $order . "', '" . $machine . "', '" . $variant . "', '" . $period . "')";
		// echo '<br>' . $sqlCP . '<br>';
		$result = mysql_query($sqlCP, $connection_stat) or die(mysql_error());
} else {
	if ($order != 'remove') {
		$sqlCP = "UPDATE `control_pannel_presets` SET 
			`machine` = '" . $machine . "',
			`variant` = '" . $variant . "',
			`order` = '" . $order . "',
			`period` = '" . $period . "'
			WHERE `id` = '" . $database_id . "' AND `user_id` = '" . $_SESSION['v2_user_id'] . "'";
		$result = mysql_query($sqlCP, $connection_stat) or die(mysql_error());
	} else {
		$sqlCP = "DELETE FROM `control_pannel_presets` WHERE `id` = '" . $database_id . "'";
		$result = mysql_query($sqlCP, $connection_stat) or die(mysql_error());
	}
}


?>