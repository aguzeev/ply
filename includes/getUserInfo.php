<?php

require_once('init.php');

$ACCESSED_MODULE = 1;
$_LOGGING = false;
include('cerber.php');

if(isset($_GET['userId'])) $userId = intval(mysql_real_escape_string($_GET['userId']));

$query = "SELECT * FROM `users` WHERE `id` = " . $userId . " LIMIT 1";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
$row = mysql_fetch_array($result, MYSQL_ASSOC);

$userInfo = json_encode(array('login' => $row['login'], 'name' => $row['name'], 'group' => $row['group'], 'redirect_url' => $row['redirect_url'], 'hasAccess' => $row['hasAccess'] ));

mysql_free_result($result);

print_r($userInfo);


?>