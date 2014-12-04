<?php

// для начальной регистрации установить значение secureLevel в 0 или закомментировать строки 8—9

/******* Проверка авторизации и прав пользователя *******/
$secureLevel = array(5, 6, 10);
session_start();
if (!isset($_SESSION['user_id'])) {	die('Доступ закрыт, <a href="http://auth/login.php">авторизуйтесь</a>.'); }
else if ( !in_array($_SESSION['user_group'], $secureLevel) ) { die('Не хватает прав доступа.'); }
/******* Конец проверки авторизации и прав пользователя *******/

require_once('includes/init.php');

if (empty($_POST)) { ?>
	<h3>Добавление нового пользователя</h3>
	
	<form action="register.php" method="post">
		<table>
			<tr>
				<td>Логин:</td>
				<td><input type="text" name="login" /></td>
			</tr>
			<tr>
				<td>Пароль:</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
			  <td>Группа:</td>
			  <td><input type="text" name="group" /></td>
		  </tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Зарегистрировать" /></td>
			</tr>
		</table>
</form>

<?php
} else {
	// обрабатывае пришедшие данные функцией mysql_real_escape_string перед вставкой в таблицу БД
	
	$login = (isset($_POST['login'])) ? mysql_real_escape_string($_POST['login']) : '';
	$password = (isset($_POST['password'])) ? mysql_real_escape_string($_POST['password']) : '';
	
	
	// проверяем на наличие ошибок (например, длина логина и пароля)
	
	$error = false;
	$errort = '';
	
	/*if (strlen($login) < 2)
	{
		$error = true;
		$errort .= 'Длина логина должна быть не менее 2х символов.<br />';
	}
	if (strlen($password) < 6)
	{
		$error = true;
		$errort .= 'Длина пароля должна быть не менее 6 символов.<br />';
	}*/
	
	// проверяем, если юзер в таблице с таким же логином
	$query = "SELECT `id`
				FROM `users`
				WHERE `login`='{$login}'
				LIMIT 1";
	$sql = mysql_query($query, $connection_stat) or die(mysql_error());
	if (mysql_num_rows($sql)==1)
	{
		$error = true;
		$errort .= 'Пользователь с таким логином уже существует в базе данных, введите другой.<br />';
	}
	
	
	// если ошибок нет, то добавляем юзаре в таблицу
	
	if (!$error)
	{
		// генерируем соль и пароль
		
		$pepper = Generatepepper();
		$hashed_password = md5(md5($pepper . $password));
		
		$query = "INSERT
					INTO `users`
					SET
						`login`='{$login}',
						`password`='{$hashed_password}',
						`pepper`='{$pepper}'";
		$sql = mysql_query($query, $connection_stat) or die(mysql_error());
		
		
		print '<h4>Поздравляем, Вы успешно зарегистрированы!</h4><a href="login.php">Авторизоваться</a>';
	}
	else
	{
		print '<h4>Возникли следующие ошибки</h4>' . $errort;
	}
}

?>