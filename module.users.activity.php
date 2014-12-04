<?php

defined('_EXEC') or die;
$category = 5;
$ACCESSED_MODULE = 11;
$LOG_TITLE = 'Журнал активности';
$_LOGGING = false;
require_once('includes/init.php');
include('includes/cerber.php');

?>

<table width="90%" style="margin: 0 5% 0 5%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable hoverable">
  <thead>
  <tr>
    <th width="15%" align="left" class="columnIndent borderB">Время</th>
    <th width="10%" align="left" class="borderB">Логин</th>
    <th width="25%" align="left" class="borderB">Модуль</th>
    <th width="15%" align="left" class="borderB">Права доступа</th>
    <th align="left" class="borderB">Браузер</th>
    <?php if ( $_SESSION['v2_user_group'] == 20 ) echo '<th width="10%" align="left" class="borderB">IP</th>'; ?>
  </tr>
  </thead>
<?php
	$query = "SELECT * FROM `activitylog` ORDER BY `timestamp` DESC LIMIT 100";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<tr><td class='columnIndent'>" . $row['timestamp'] . "</td>\n";
		
		echo "<td>";
		if ( $row['user_login'] == 'admin' ) echo "<span style='color: #A0A0A0'>" . $row['user_login'] . "</span>";
		else echo $row['user_login'];
		echo "</td>\n";
		
		echo "<td>" . $row['module_name'] . "</td>\n";
		echo "<td>" . $row['action'] . "</td>\n";
		
		if ( strpos($row['browser'], 'iPhone') > -1 ) $browser = 'iPhone ' . substr($row['browser'], 32, 8);
		else if ( strpos($row['browser'], 'iPad') > -1 ) $browser = 'iPad ' . substr($row['browser'], 23, 8);
		else $browser = $row['browser'];
		echo "<td style='font-size: 10px'>" . $browser . "</td>\n";
		if ( $_SESSION['v2_user_group'] == 20 ) echo "<td>" . $row['ip'] . "</td>\n";
		echo "</tr>\n";
	}
?>
</table>