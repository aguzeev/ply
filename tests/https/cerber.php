<?php
// © Александр Гузеев (Alexander Guzeev), 2012

//defined('_EXEC') or die; нельзя использовать для обработчиков

// если пользователь не авторизован

$_DEBUG = false;
if ($_DEBUG) echo "session: user_id: " . $_SESSION['v2_user_id'] . "<br>";
if ($_DEBUG) echo "session: user_group: " . $_SESSION['v2_user_group'] . "<br>";

if (isset($_SESSION['v2_user_id'])) {
	$query = "SELECT `login`
				FROM `users`
				WHERE `id`='" . $_SESSION['v2_user_id'] . "'
				LIMIT 1";
	$sql = mysql_query($query, $connection_stat) or die(mysql_error());
	
	// если нету такой записи с пользователем (вдруг удалили его пока он лазил по сайту),
	// то надо убрать у него ID, установленный в сессии, чтобы он был гостем
	if (mysql_num_rows($sql) != 1) {
		header('Location: login.php?logout');
		exit;
	}
	$row = mysql_fetch_assoc($sql);
	$welcome = $row['login'];
} else {
	$welcome = 'гость';
}

/******* Проверка авторизации и прав пользователя *******/
session_start();
if (!isset($_SESSION['v2_user_id'])) {	die('Доступ закрыт, <a href="http://auth">авторизуйтесь</a>.'); }
if (!isset($ACCESSED_MODULE)) {	$ACCESSED_MODULE = 0; die("Stop because accessed module id isn't set"); } // Временно! Убрать!
if ( $_DEBUG ) echo "ACCESSED_MODULE: " . $ACCESSED_MODULE . "<br>";
if ( $_DEBUG ) echo "LOG_TITLE: " . $LOG_TITLE . "<br>";

$permition = checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE);

/******* Dump activity to DB *******/
if ( $_LOGGING ) {
	switch ($permition) {
		case 0: $action = 'Denied'; break;
		case 1: $action = 'Readonly access '; break;
		case 2: $action = 'Full access'; break;
	}
	
	$dumpTStamp = date("Y-m-d G:i:s", date("U") - $timeDifference);
	if ( isset($LOG_TITLE) ) $dumpModule = $LOG_TITLE;
	else $dumpModule = $allModules[$ACCESSED_MODULE];
	
	$sqlLog = "INSERT INTO `activitylog` (`id`, `timestamp`, `user_login`, `module_name`, `action`) VALUES (
		NULL,
		'" . $dumpTStamp . "',
		'" . $welcome . "', 
		'" . $dumpModule . "',
		'" . $action . "')";
	$resultLog = mysql_query($sqlLog, $connection_stat);
	if ( $_DEBUG && $resultLog ) echo "Log record for " . $welcome  . " added";
}

if ( $permition == 0 ) die("<p align='center'>У вас нет доступа к этому разделу</p>");
else if ( $permition == 1 ) echo "<p align='center'>Вы можете только просматривать этот раздел</p>";

/******* Конец проверки авторизации и прав пользователя *******/
?>