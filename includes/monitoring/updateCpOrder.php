<?php

require_once('../init.php');

$ACCESSED_MODULE = 0; // everyone has access
$_LOGGING = false;
include('../cerber.php');

if ( isset($_GET['order']) ) {
	$order = explode("-", mysql_real_escape_string($_GET['order']) );
	$user = $_SESSION['v2_user_id'];

	// выбираем все значения id записей из таблицы виджетов
	$query = "SELECT `id` FROM `control_pannel_presets` WHERE `user_id` = " . $user . " ORDER BY `order`";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$ids = array();
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) { $ids[] = $row[0]; }
	mysql_free_result($result);
	
	// обновляем последовательно все виджеты этого пользователя
	for ($key = 0; $key < sizeof($order); $key++) {
		$query = "UPDATE `control_pannel_presets` SET `order` = " . $key . " WHERE `user_id` = $user AND `id` = " . $ids[$order[$key]];
		//echo "<br>" . $query;
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
	
	echo json_encode(array("result" => "ok"));
} else {
	echo json_encode(array("result" => "alert", "text" => "Nothing changed"));
}

?>