<?php
$_LOGIN = 'admin';
$_PASSWORD = 'admin';

session_start();

$_DEBUG = true;
if ($_DEBUG) echo "session: s_user_id: " . $_SESSION['s_user_id'] . "<br>";

require_once ('init.php');

// Logout
if (isset($_GET['logout'])) {
	if (isset($_SESSION['s_user_id'])) unset($_SESSION['s_user_id']);
	
	setcookie('login', '', 0, "/");
	setcookie('password', '', 0, "/");

	header('Location: index.php');
	exit;
}

if (!isset($_SESSION['s_user_id'])) {
	if ($_DEBUG) echo "session:user_id doesn't set<br>";
	// то проверяем его куки, вдруг там есть логин и пароль к нашему скрипту
	if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
		// если же такие имеются, то пробуем авторизовать пользователя по этим логину и паролю
		$login = mysql_real_escape_string($_COOKIE['login']);
		$password = mysql_real_escape_string($_COOKIE['password']);

		if ( $login == $_LOGIN && $password = $_PASSWORD ) {
			$_SESSION['s_user_id'] = 1;
		}
	}
}

if (!isset($_SESSION['s_user_id'])) { // если и после проверки кукис пользователя не удалось авторизовать
	if ( !empty($_POST) ) {
		$login = (isset($_POST['login'])) ? mysql_real_escape_string($_POST['login']) : '';
		$password = (isset($_POST['password'])) ? mysql_real_escape_string($_POST['password']) : '';
		
		if ( $login == $_LOGIN && $password = $_PASSWORD ) {
			$_SESSION['s_user_id'] = '1';
			
			if (isset($_POST['remember'])) {
				$time = 604800;
				setcookie('login', $login, time()+$time, "/");
				setcookie('password', $password, time()+$time, "/");
			}
			
			header('Location: index.php');
			exit;
		}
	} else {
		print '
		<div id="loginFormContainer" class="curl">
		<form action="https://ply-stat.ru/v2/tests/https/login.php" method="post">
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
} 

if(isset($_GET['wronguser'])) echo "<p>Такой пользователь не найден на сервере</p>";
if(isset($_GET['wrongpass'])) echo "<p>Проверьте имя пользователя или пароль</p>";

?>
