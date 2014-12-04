<?php

if (!defined('_EXEC')) header('Location: index.php');
session_start();

$_DEBUG = false;
if ($_DEBUG) echo "session: v2_user_id: " . $_SESSION['v2_user_id'] . "<br>";

require_once ('includes/init.php');

// Logout
if (isset($_GET['logout'])) {
	if (isset($_SESSION['v2_user_id'])) unset($_SESSION['v2_user_id']);
	if (isset($_SESSION['v2_user_group'])) unset($_SESSION['v2_user_group']);
	if (isset($_SESSION['v2_name'])) unset($_SESSION['v2_name']);
	
	setcookie('login', '', 0, "/");
	setcookie('password', '', 0, "/");

	header('Location: index.php');
	exit;
}

if (!isset($_SESSION['v2_user_id'])) {
	if ($_DEBUG) echo "session:user_id doesn't set<br>";
	// то проверяем его куки, вдруг там есть логин и пароль к нашему скрипту
	if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
		// если же такие имеются, то пробуем авторизовать пользователя по этим логину и паролю
		$login = mysql_real_escape_string($_COOKIE['login']);
		$password = mysql_real_escape_string($_COOKIE['password']);
		$query = "SELECT *
					FROM `users`
					WHERE `login`='{$login}' AND `password`='{$password}'
					LIMIT 1";
		$sql = mysql_query($query, $connection_stat) or die(mysql_error());
		// если такой пользователь нашелся
		if (mysql_num_rows($sql) == 1) {
			$row = mysql_fetch_assoc($sql);
			$_SESSION['v2_user_id'] = $row['id'];
			$_SESSION['v2_user_group'] = $row['group'];
			$_SESSION['v2_name'] = $row['name'];
			$_SESSION['v2_redirectUrl'] = $row['redirect_url'];
		}
	}
}

if (!isset($_SESSION['v2_user_id'])) { // если и после проверки кукис пользователя не удалось авторизовать
	if (!empty($_POST)) {
		$login = (isset($_POST['login'])) ? mysql_real_escape_string($_POST['login']) : '';
		
		$query = "SELECT `pepper`
					FROM `users`
					WHERE `login`='{$login}'
					LIMIT 1";
		$sql = mysql_query($query, $connection_stat) or die(mysql_error());
		
		if (mysql_num_rows($sql) == 1)
		{
			$row = mysql_fetch_assoc($sql);
			
			// итак, вот она соль, соответствующая этому логину:
			$pepper = $row['pepper'];
			
			// теперь хешируем введенный пароль как надо и повторям шаги, которые были описаны выше:
			$password = md5(md5($pepper . $_POST['password']));
			
			// и пошло поехало...
	
			// делаем запрос к БД
			// и ищем юзера с таким логином и паролем
	
			$query = "SELECT `id`, `name`, `group`, `redirect_url`
						FROM `users`
						WHERE `login`='{$login}' AND `password`='{$password}'
						LIMIT 1";
			$sql = mysql_query($query, $connection_stat) or die(mysql_error());
	
			// если такой пользователь нашелся
			if (mysql_num_rows($sql) == 1)
			{
				// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)
	
				$row = mysql_fetch_assoc($sql);
				$_SESSION['v2_user_id'] = $row['id'];
				$_SESSION['v2_user_group'] = $row['group'];
				$_SESSION['v2_name'] = $row['name'];
				
				$_SESSION['v2_redirectUrl'] = $row['redirect_url'];
				$act = $row['redirect_url'];
				
				
				
				// если пользователь решил "запомнить себя"
				// то ставим ему в куку логин с хешем пароля
				
				
				if (isset($_POST['remember']))
				{
					setcookie('login', $login, time() + $cookieTime, "/");
					setcookie('password', $password, time() + $cookieTime, "/");
				}
				
				/*if ($row['redirect_url']) {
					header('Location: ' . $row['redirect_url']);
				} else {
					header('Location: index.php');
				}*/
				header('Location: index.php?act=' . $act);
				exit;
	
				// не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
			}
			else
			{
				header('Location: index.php?wrongpass');
				die('Неверно введены имя пользователя или пароль — <a href="login.php">Авторизоваться</a>');
			}
		}
		else
		{ // Нет такого пользователя
			header('Location: index.php?wronguser');
			die('Такой пользователь не найден на сервере — <a href="login.php">Авторизоваться</a>');
		}
	}
	print '
	<meta name=viewport content="width=device-width, initial-scale=1">
	<div id="loginFormContainer" class="curl">
	<form action="login.php" method="post">
		<div id="loginMessage" class="curl"><p id="loginMessageP"></p></div>
		<table align="center">
			<tr>
				<td>Логин:</td>
				<td><input type="text" name="login" /></td>
			</tr>
			<tr>
				<td>Пароль:</td>
				<td><input type="password" name="password" /></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="checkbox" name="remember" style="display: inline-block; vertical-align: top;" />
					<p style="display:inline-block; vertical-align: top; padding: 0; margin: 0; font-size: 12px; text-align: left;">запомнить меня<br />
					на этом компьютере</p>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" value="Войти" class="btnSubmit" /></td>
			</tr>
		</table>
	</form>
	</div>
	';
}

if(isset($_GET['wronguser'])): ?>
<script language="javascript">
	$('#loginMessage').show();
	$('#loginMessageP').html('Такой пользователь не найден на сервере');
</script>
<?php endif;
if(isset($_GET['wrongpass'])): ?>
<script language="javascript">
	$('#loginMessage').show();
	$('#loginMessageP').html('Проверьте имя пользователя или пароль');
</script>
<?php endif; ?>
