<?php
	
$connection_stat = mysql_connect("localhost","plystatr_manager","EZQe^vC7iUEPly", true) or die (mysql_error());
mysql_select_db("plystatr_auth_zp", $connection_stat) or die (mysql_error());

mysql_query("set character_set_client	='utf8'", $connection_stat);
mysql_query("set character_set_results	='utf8'", $connection_stat);
mysql_query("set collation_connection	='utf8_general_ci'", $connection_stat);
//mysql_query("SET time_zone = 'Europe/Moscow'", $connection_stat);

?>