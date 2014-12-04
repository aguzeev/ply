<?php

// © Александр Гузеев (Alexander Guzeev), 2012

session_start();
require_once('init.php');

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Хардвуд HTTPS</title>

</head>

<body>

<?php

if ( !isset($_SESSION['s_user_id']) ) {
	include('login.php');
	if (isset($_GET['act'])): ?>
    
<p>Пожалуйста, авторизуйтесь для доступа к этой старинце</p>
 
<?php endif;
}
if (isset($_SESSION['s_user_id'])) {
	include('closed.php');
}
?>


</body>
</html>