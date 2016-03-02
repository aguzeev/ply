<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Склад';

include('includes/cerber.php');
require_once('includes/init.php');
require_once("includes/warehouse/warehouse_dictionary.php");

$activeComponent = 'warehouse';

?>

<script language="javascript" src="js/jquery.validate.min.js"></script>
<script language="javascript" src="js/messages_ru.min.js"></script>
<script language="javascript" src="js/module.warehouse.catalog.js"></script>
<style type="text/css">
.addToCatalogDialog { position: relative; }
.errorContainer { color: red; font-size: 10px; display: inline-block; width: 75px; margin: 0 0 0 10px; }
</style>

<table class="whTasks" style="width: 60%;">
  <thead>
    <tr>
      <td class="catId">ID</td>
      <td class="catType">Марка</td>
      <td class="catLength">Длина</td>
      <td class="catWidth">Ширина</td>
      <td class="catThickness">Толщина</td>
    </tr>
  </thead>
  <tbody>
  <?php
  	$sqlSelect = "SELECT * FROM `wh_formats` ORDER BY `type`, `length`, `width`, `thickness`";
	$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
	if ( mysql_num_rows($resultSelect) > 0 ) {
		while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
			echo '
		<tr id="row_' . $row["id"] . '">
			<td class="catId">' . $row["id"] . '</td>
			<td class="catType">' . $wh_ply_types[ $row["type"] ] . '</td>
			<td class="catLength">' . $row["length"] . '</td>
			<td class="catWidth">' . $row["width"] . '</td>
			<td class="catThickness">' . ($row["thickness"] / 10) . '</td>
		</tr>
			';
		}
	} else {
		echo '<tr><td colspan="5" align="center">В справочнике нет ни одного формата</td></tr>';
	}
  ?>
  </tbody>
</table>

<p align="right"><input type="button" class="addButton" value="Добавить формат" id="addFormatButton"></p>


<div id="addToCatalogDialog" title="Добавление формата в справочник">
  <form id="addToCatalogForm" action="">
  	<fieldset>
    <table border="0" cellspacing="0" cellpadding="3">
      <tr>
        <td width="70" align="right">Марка</td>
        <td width="200">
        	<select name="plyType" id="plyType">
<?php
	foreach( $wh_ply_types as $ply_type_key => $ply_type_value ) {
		echo '<option value="' . $ply_type_key . '">' . $ply_type_value . '</option>';
	}
?>
            </select>
        </td>
      <tr>
        <td align="right"><label for="plyLength">Длина</label></td>
        <td><input id="plyLength" name="plyLength" type="text" size="6" maxlength="4" required /> мм<div class="errorContainer"></div></td>
      
      <tr>
        <td align="right"><label for="plyWidth">Ширина</label></td>
        <td><input id="plyWidth" name="plyWidth" type="text" size="6" maxlength="4" required /> мм<div class="errorContainer"></div></td>
      
      <tr>
        <td align="right"><label for="plyThickness">Толщина</label></td>
        <td><input id="plyThickness" name="plyThickness" type="text" size="6" maxlength="4" required /> мм<div class="errorContainer"></div></td>
    </table>
    </fieldset>
  </form>
</div>