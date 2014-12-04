<?php

require_once('init.php');

$ACCESSED_MODULE = 1;
$_LOGGING = false;
include('cerber.php');

// Удаление пользователя
if(isset($_GET['userId'])) {
	$userId = intval(mysql_real_escape_string($_GET['userId']));
	$query = "DELETE FROM `users` WHERE `id` = " . $userId . " LIMIT 1"; 
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
}

?>