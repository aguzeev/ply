<?php
	
$connection_stat = mysql_connect("127.0.0.1:3306", "root", "", true) or die (mysql_error());
mysql_select_db("auth", $connection_stat) or die (mysql_error());

//$connection_stat = mysql_connect("localhost","plystatr_manager","EZQe^vC7iUEPly", true) or die (mysql_error());
//mysql_select_db("plystatr_auth_zp", $connection_stat) or die (mysql_error());


$connection_hardware = mysql_connect("127.0.0.1:3306", "root", "", true) or die (mysql_error());
mysql_select_db("happy_2_local", $connection_hardware) or die (mysql_error());

//$connection_hardware = mysql_connect("localhost","plystatr_manager","EZQe^vC7iUEPly", true) or die (mysql_error());
//mysql_select_db("plystatr_machinesdata", $connection_hardware);



mysql_query("set character_set_client	='utf8'", $connection_hardware);
mysql_query("set character_set_results	='utf8'", $connection_hardware);
mysql_query("set collation_connection	='utf8_general_ci'", $connection_hardware);
//mysql_query("SET time_zone = '+04:00'", $connection_hardware);

mysql_query("set character_set_client	='utf8'", $connection_stat);
mysql_query("set character_set_results	='utf8'", $connection_stat);
mysql_query("set collation_connection	='utf8_general_ci'", $connection_stat);
//mysql_query("SET time_zone = '+04:00'", $connection_stat);

date_default_timezone_set('Europe/Moscow');


include('constants.php');
include('functions.php');

if ( isset($LOG_TITLE) ) unset($LOG_TITLE);
if ( isset($ACCESSED_MODULE) ) unset($ACCESSED_MODULE);
$_LOGGING = true;

?>