<?php

defined('_EXEC') or die;
$category = 5;
$ACCESSED_MODULE = 11;
$_LOGGING = false;
require_once('includes/init.php');
include('includes/cerber.php');

?>

<script type="text/javascript" src="js/module.monitoring.maintenance.js"></script>

<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="css/style-monitoring.css" />
<link rel="stylesheet" href="js/jqplot/jquery.jqplot.css" />

<h2 align="center">Журнал технических работ с оборудованием</h2>

<table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
  <tr>
    <td width="20%" align="left"><p><strong>Время начала</strong></p></td>
    <td width="20%" align="left"><p><strong>Время окончания</strong></p></td>
    <td width="15%" align="left"><p><strong>Станок</strong></p></td>
    <td width="45%" align="left"><p><strong>Описание</strong></p></td>
  </tr>
<?php
	
	$sql = "SELECT * FROM `maintenance` ORDER BY `start` DESC";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "
  <tr id='row_" . $row['id'] . "'>
    <td>" . $row['start'] . "</td>
	<td>" . $row['finish'] . "</td>
	<td>" . $row['machine'] . "</td>
	<td>" . $row['description'];
	echo "</tr>";
	}
?>
</table>

<!-- Служебные сообщения -->
<div id="loading">
	<!--<img align="middle" style="position: absolute; top: 40%; left: 48%; z-index: 999" src="img/loading.gif" />-->
	<div class="semiopacity"></div>
	
</div>