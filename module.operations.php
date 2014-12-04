<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 4;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/module.operations.js"></script>

<div style="width: 98%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
    <input type="button" class="addButton" value="Добавить операцию" onclick="javascript:addOperation();" />
    <?php endif; ?>
</div>


<table width="30%" style="margin:0 35% 0 35%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable">
	<thead>
      <tr>
      	<th align="left" valign="bottom" class="columnIndent borderB">Название операции</th>
      </tr>
  </thead>
  <tfoot></tfoot>
  <tbody>
  <?php
	$query = "SELECT `id`, `operation` FROM `operations`";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<tr id='rowID_" . $row['id'] . "'><td class='columnIndent'>" . $row['operation'] . "\n";
			if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ) {
				echo "<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $row['id'] . "'>
			<tr>
			  <td><a href='javascript:editOperation(" . $row['id'] . ")'><img src='img/edit.png' width='16' height='16' alt='Редактировать' /></a></td>
			  <td><a href='javascript:removeOperation(" . $row['id'] . ")'><img src='img/remove.png' width='16' height='16' alt='Удалить' /></a></td>
			</tr>
			</table>";
			}
		echo "</td>
		</tr>\n";
	}
	?>
    </tbody>
</table>


<div id="editOperationDialog" title="Редактирование операции">
  <form id="editOperationDialogForm" action="">
	<input type="hidden" id="editOperationDialog_id" name="operationId" />
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="editOperationDialog_op">Операция: </label></td>
    <td><input id="editOperationDialog_op" name="operation" type="text" /></td>
  </tr>
</table>
</form>
</div>

<div id="addOperationDialog" title="Добавление операции">
  <form id="addOperationDialogForm" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="addOperationDialog_op">Операция: </label></td>
    <td><input id="addOperationDialog_op" name="operation" type="text" /></td>
  </tr>
</table>
</form>
</div>

<div id="removeOperationDialog" title="Удаление операции">
<input type="hidden" id="removeOperationDialog_id" name="operationId" />
<p>Вы действительно хотите удалить операцию <span style="color: #F00;"></span> из списка?</p>
</div>