﻿<?php

/******* Проверка авторизации и прав пользователя *******/
$secureLevel = array(5, 6, 10);
session_start();
if (!isset($_SESSION['user_id'])) {	die('Доступ закрыт, авторизуйтесь.'); }
else if ( !in_array($_SESSION['user_group'], $secureLevel) ) { die('Не хватает прав доступа.'); }
/******* Конец проверки авторизации и прав пользователя *******/
	
	print '<h1>Здрасте!</h1>
	<p>Это закрытая страница.</p>
	<p><a href="index.php">Перейти на главную</a></p>';


?>