<?php

// © Александр Гузеев (Alexander Guzeev), 2012

define('_EXEC', 1);
session_start();
require_once('includes/init.php');

// обновляем куки. чтобы не делать это слишком часто, ждём окончания сессии
if ( isset($_COOKIE['login']) && isset($_COOKIE['password']) && !$_SESSION['v2_user_id'] ) {
	$login = mysql_real_escape_string($_COOKIE['login']);
	$password = mysql_real_escape_string($_COOKIE['password']);
	setcookie('login', $login, time() + $cookieTime, "/");
	setcookie('password', $password, time() + $cookieTime, "/");
}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Система статистики Хардвуд трейдинг</title>
<link rel="stylesheet" href="styles.css" />
<link rel="stylesheet" href="stylesStat.css" />
<link rel="stylesheet" href="css/style-monitoring.css" />
<!--[if IE]><link rel="stylesheet" href="styles_ie.css" /> <![endif]-->
<link rel="stylesheet" href="js/css/jquery-ui-1.10.3.custom.css" />
<link rel="stylesheet" href="js/css/chosen.css" />
<link rel="icon" href="img/favicon.ico" type="image/x-icon">
<link rel="stylesheet" href="js/css/jquery.pnotify.default.css" media="all" type="text/css" />
<link rel="stylesheet" href="js/css/jquery.pnotify.default.icons.css" media="all" type="text/css" />

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="js/common.js?v1"></script>
<script language="javascript" src="js/chosen2.jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker-ru.js"></script>
<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="js/jquery.json-2.3.min.js"></script>
<script type="text/javascript" src="js/moment.min.js?v1"></script>
<script type="text/javascript" src="js/jquery.pnotify.min.js"></script>

</head>

<body>

<?php
if ($_SERVER['HTTP_HOST'] == 'auth') {
	echo'<div style="width: 100%; border: #F00 1 px solid; background-color: #FFAC59; font-family: Tahoma, Geneva, sans-serif; font-size: 14px; text-align: center; font-weight: bold; padding: 5px 0 5px 0;">Работа на локальном сервере.</div>';
}


/* ===== Объявления ===== */
//echo '<div style="width: 100%; border: #F00 1 px solid; background-color: #FFAC59; font-family: Tahoma, Geneva, sans-serif; font-size: 14px; text-align: center; font-weight: bold; padding: 5px 0 5px 0;">В связи с отменой перехода на летнее время, данные за 10:00—11:00 26 октября могут наложиться друг на друга.</div>';



// если пользователь не авторизован
if ( !isset($_SESSION['v2_user_id']) ) {
	include('login.php');
	if (isset($_GET['act'])): ?>
    
<script language="javascript">
	$('#loginMessage').show();
	$('#loginMessageP').html('Пожалуйста, авторизуйтесь для доступа к этой старинце');
 </script>
 
<?php endif;
}
if (isset($_SESSION['v2_user_id'])) {
	
	if (isset($_GET['act'])) {
		$act = strtolower(mysql_real_escape_string($_GET['act']));
	} else { // перенаправление на страницу по умолчанию для пользователя
		$act = strtolower(mysql_real_escape_string($_SESSION['v2_redirectUrl']));
	}
	
	/*$urls = array(
		'index.php?act=users' => 'Пользователи системы',
		'index.php?act=workers' => 'Штатное расписание',
		'index.php?act=operations' => 'Операции',
		'index.php?act=appointments' => 'Должности',
		'index.php?act=rates' => 'Тарифы',
		'index.php?act=fixedprice' => 'Базовые части з/п',
		'index.php?act=timeboard' => 'Табель',
		'index.php?act=production' => 'Выполненные объёмы',
		'index.php?act=accural' => 'Начисления з/п',
		'login.php?logout' => 'Выход'
	);
	
	echo '<div id="naviContainer">
    <div id="billNavigation">';
	foreach ($urls as $url => $text) {
		echo '<a href="' . $url . '"';
		if ($url == ('index.php?act=' . $act)) echo ' class="active"';
		echo '>' . $text .'</a>';
	}
	echo '</div>
</div>';*/

?>
<div id="naviContainer">
    <div id="billNavigation">
    	<ul>
        	<li<?php if (strpos($act, 'users')>-1) echo(" class='activeLi'");?>><a href="index.php?act=users">Пользователи</a>
            	<ul>
                	<li><a href="index.php?act=users.activity">Журнал активности</a></li>
                </ul>
            </li>
            <li<?php if (strpos($act, 'monitoring')>-1) echo(" class='activeLi'");?>><a href="index.php?act=monitoring.cp">Мониторинг</a>
                <table cellpadding="0" cellspacing="0" border="0">
                	<tr><td valign="top">
                    	<ul style="position: static;">
                            <li><a href="index.php?act=monitoring.cp">Персональный</a></li>
                            <li><a href="index.php?act=monitoring.current">Текущий</a></li>
                            <li><a href="index.php?act=monitoring.period">Детальный за период</a></li>
                            <li><a href="index.php?act=monitoring.month">За месяц</a></li>
                            <li><a href="index.php?act=monitoring.year">За год</a></li>
                        </ul>
                    </td>
                    <!--<td valign="top">
                    	<ul id="monitoring" style="position: static;">
                            <li data-mach="0" class="updatable"><a href="index.php?act=monitoring&mach=0">Лущение</a></li>
                            <li data-mach="1" class="updatable"><a href="index.php?act=monitoring&mach=1">Ножницы</a></li>
                            <li data-mach="3" class="updatable"><a href="index.php?act=monitoring&mach=3">Сушилка</a></li>
                            <li><a href="index.php">Уч. 1: Лущение + сушка</a></li>
                            
                            <li data-mach="2" class="updatable"><a href="index.php?act=monitoring&mach=2">Котельная</a></li>
                            <li data-mach="4" class="updatable"><a href="index.php?act=monitoring&mach=4">Опиловка</a></li>
                        </ul>
                    	
                    </td>-->
                </tr></table>
            </li>
            <li<?php if ($act=='workers'||$act=='appointments'||$act=='operations') echo(" class='activeLi'");?>><a href="index.php?act=workers">Персонал</a>
            	<ul>
                	<li><a href="index.php?act=workers">Штатное расписание</a></li>
                	<li><a href="index.php?act=appointments">Должности</a></li>
                    <li><a href="index.php?act=operations">Перечень&nbsp;операций</a></li>
                </ul>
            </li>
            <li<?php if ($act=='rates'||$act=='fixedprice') echo(" class='activeLi'");?>><a href="index.php?act=rates">Тарифы</a>
            	<ul>
                	<li><a href="index.php?act=rates">Тарифы&nbsp;на&nbsp;производство</a></li>
                    <li><a href="index.php?act=fixedprice">Базовые&nbsp;части<br />
сдельной&nbsp;з/п</a></li>
                    <li style="cursor: not-allowed;"><a href="#" style="cursor: not-allowed;">Оклады</a></li>
                </ul>
            </li>
            <li<?php if ($act=='timeboard') echo(" class='activeLi'");?>><a href="index.php?act=timeboard">Табель</a>
            	<ul>
                	<li><a href="index.php?act=timeboard">Выходы&nbsp;на&nbsp;работу</a></li>
                	<li style="cursor: not-allowed;"><a href="#" style="cursor: not-allowed;">Отпуска</a></li>
                    <li style="cursor: not-allowed;"><a href="#" style="cursor: not-allowed;">Больничные</a></li>
                    <li style="cursor: not-allowed;"><a href="#" style="cursor: not-allowed;">Прогулы</a></li>
                    <li style="cursor: not-allowed;"><a href="#" style="cursor: not-allowed;">Премии&nbsp;и&nbsp;штрафы</a></li>
                </ul>
            </li>
            <li<?php if ($act=='production') echo(" class='activeLi'");?>><a href="index.php?act=production">Выполненные объёмы</a></li>
            <li<?php if ($act=='accural') echo(" class='activeLi'");?>><a href="index.php?act=accural">Начисления з/п</a></li>
            <li><a href="login.php?logout">Выход</a></li>
            
        </ul>
    	<!--
        
        -->
        <div class="updates">
        	<?php require_once("updates.php"); ?>
        </div>
       </div>
        
    </div>
</div>
<!--<p align="center" style="background-color: #FFDADA; padding: 5px 0;">Проводятся работы по обновлению компонентов системы.</p>-->

<div id="showError" title="Ошибка" style="display: none;">
<table cellpadding="5" style="display: none;">
<tr>
<td valign="middle"><img src="img/alert_icon.png" /></td>
<td valign="middle">
	<p id="errorSource"></p>
	<p><span id="errorText"></span> Попробуйте обновить страницу или сбросить значения фильтров.</p>
</td>
</tr>
</table></div>

<?php

if ( file_exists('module.' . $act . '.php') ) include('module.' . $act . '.php');
}
?>

<p class="supportEmail">О замеченных на сайте ошибках, пожалуйста, сообщайте по адресу <a href="mailto:admin@ply-stat.ru">admin@ply-stat.ru</a>.</p>

<div id="debug" style="width: 100%; height: 100px;"></div>
<div id="debug2" style="width: 100%; height: 100px;"></div>

<?php
	if ($_SERVER['HTTP_HOST'] == 'ply-stat.ru') {
		require_once('includes/metrika.php');
	}
?>
</body>
</html>