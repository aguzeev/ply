<?php

require_once('init.php');

$ACCESSED_MODULE = 1;
$_LOGGING = false;
include('cerber.php');

$query = "SELECT `id`, `group_name` FROM `groups`";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
	echo '<option value="' . $row['id'] . '">' . $row['group_name'] . '</option';
}

?>