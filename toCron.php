<?php
	require_once('includes/init.php');
	
	$sql = "INSERT INTO `cron` (`id`) VALUES ('null')";
	$result = mysql_query($sql, $connection_stat) or die (mysql_error());
	if ($result) echo ("Inserted successfully!");
?>