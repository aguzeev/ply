<?php

require_once('init.php');

$ACCESSED_MODULE = 1;
$_LOGGING = false;
include('cerber.php');

if (isset($_GET['userId'])) {$userId = mysql_real_escape_string($_GET['userId']); }
else { die('Не указан пользователь'); }

if (isset($_GET['login'])) { // Редактирование или добавление полей профиля без пароля
	$login = mysql_real_escape_string($_GET['login']);
	if(isset($_GET['name'])) $name = mysql_real_escape_string($_GET['name']);
	if(isset($_GET['group'])) $group = intval(mysql_real_escape_string($_GET['group']));
	if(isset($_GET['hasAccess'])) $hasAccess = mysql_real_escape_string($_GET['hasAccess']);
	if(isset($_GET['url'])) $url = mysql_real_escape_string($_GET['url']);
	
	if ($userId == 'new') {
		// Добавляем нового пользователя
		$password = mysql_real_escape_string($_GET['password']);
		if (strlen($password) < 3) die('Слишком короткий пароль');
		$pepper = Generatepepper();
		$hashed_password = md5(md5($pepper . $password));
	
		$query = "INSERT INTO `users`
			(`login`, `name`, `group`, `redirect_url`, `pepper`, `password`, `hasAccess`) VALUES
			('" . $login ."', '" . $name ."', '" . $group ."', '" . $url ."', '" . $pepper ."', '" . $hashed_password ."', '" . $hasAccess . "')";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующего пользователя
		$query = "UPDATE `users` SET 
			`login` = '" . $login ."',
			`name` = '" . $name ."',
			`group` = " . $group .",
			`hasAccess` = '" . $hasAccess ."',
			`redirect_url` = '" . $url ."' WHERE `id` = " . $userId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}
else if (isset($_GET['password'])) { // Редактирование только пароля
	$password = mysql_real_escape_string($_GET['password']);
	if (strlen($password) < 3) die('Слишком короткий пароль');
	$pepper = Generatepepper();
	$hashed_password = md5(md5($pepper . $password));
	$query = "UPDATE `users` SET 
		`password` = '" . $hashed_password . "',
		`pepper` = '" . $pepper . "' WHERE `id` = " . $userId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
}


?>