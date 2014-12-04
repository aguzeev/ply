<?php
	
$connection_stat = mysql_connect("127.0.0.1:3306", "root", "", true) or die (mysql_error());
mysql_select_db("auth", $connection_stat) or die (mysql_error());

//$connection_stat = mysql_connect("mysql.baze.ply-stat.ru:62965", "root", "w3hiohmciw", true) or die (mysql_error());
//mysql_select_db("auth", $connection_stat) or die (mysql_error());


//$connection_hardware = mysql_connect("127.0.0.1:3306", "root", "", true) or die (mysql_error());
//mysql_select_db("happy_2_local", $connection_hardware) or die (mysql_error());

$connection_hardware = mysql_connect("mysql.baze.ply-stat.ru:62965","localuser","plyLocalUserSQLPass", true) or die (mysql_error());
mysql_select_db("happy_hosting", $connection_hardware);



mysql_query("set character_set_client	='utf8'", $connection_hardware);
mysql_query("set character_set_results	='utf8'", $connection_hardware);
mysql_query("set collation_connection	='utf8_general_ci'", $connection_hardware);

mysql_query("set character_set_client	='utf8'", $connection_stat);
mysql_query("set character_set_results	='utf8'", $connection_stat);
mysql_query("set collation_connection	='utf8_general_ci'", $connection_stat);




?>