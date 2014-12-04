<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 3;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/module.appointments.js"></script>


<div style="width: 98%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
	<input type="button" class="addButton" value="Добавить должность" onclick="javascript:addAppointment();" />
    <?php endif; ?>
</div>


<table width="70%" style="margin: 0 15% 0 15%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="hoverable editable">
	<thead>
      <tr>
        <th width="25%" align="left" valign="bottom" class="columnIndent borderB">Подразделение</th>
        <th width="30%" align="left" valign="bottom" class="columnIndent borderB">Должность</th>
        <th colspan="2" align="left" valign="bottom" class="columnIndent borderB">Доступные операции</th>
      </tr>
  </thead>
  <tfoot></tfoot>
  <tbody>
  <?php
	$query = "SELECT * FROM `appointments` ORDER BY `department`";
	$resultOp = mysql_query($query, $connection_stat) or die(mysql_error());
	while ($row = mysql_fetch_array($resultOp, MYSQL_ASSOC)) {
		echo "<tr id='rowID_" . $row['id'] . "'>
		<td class='columnIndent'>" . getDeptName( $row['department'] ) . "</td>\n
		<td>" . $row['appointment'] . "</td>\n
			<td>";
			$allowedOp = unserialize( $row['allowedOperationsDefault'] );
			
			/* echo "<select disabled='disabled' data-placeholder='Выберите операции' style='width: 400px;' name='operations_" . $row['id'] . "' id='editAppointment_op" . $row['id'] . "' size='1' multiple='multiple' class='selectOp'>";
			foreach ($allOperations as $key => $value) {
				echo "<option value='" . $key . "'";
				// отмечаем выбранными те операции, которые уже есть
				if ( in_array($key, $allowedOp) ) echo " selected='selected'";
				echo ">" . $value . "</option>";
			}
			echo "</select>*/
			
			if ($allowedOp != "null") {
				foreach ($allowedOp as $value) {
					echo '<span class="asSmallButton" style="cursor: default;">' . getOperationName($value) . "</span>";
					// отмечаем выбранными те операции, которые уже есть
				}
			} else {
				echo '<span class="allowedOperationsDef">Не заданы</span>';
			}
			
			echo "</td>";
			echo "<td>";
			if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ) {
			echo "<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $row['id'] . "'>
				<tr>
				  <td><a href='javascript:editAppointment(" . $row['id'] . ")'><img src='img/edit.png' width='16' height='16' title='Редактировать' /></a></td>
				  <td><a href='javascript:removeAppointment(" . $row['id'] . ")'><img src='img/remove.png' width='16' height='16' title='Удалить' /></a></td>
				</tr>
			</table>";
			}
			echo "</td>
		</tr>\n";
	}
	?>
    </tbody>
</table>


<div id="editAppointmentDialog" title="Редактирование должости">
  <form id="editAppointmentDialogForm" action="">
	<input type="hidden" id="editAppointmentDialog_id" name="id" />
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="editAppointmentDialog_dept">Подразделение: </label></td>
    <td>
    <select name="sector" id="editAppointmentDialog_dept" style="width: 300px;">
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="editAppointmentDialog_app">Должность: </label></td>
    <td><input type="text" id="editAppointmentDialog_app" style="width: 295px;" /></td>
  </tr>
  <tr>
    <td><label for="editAppointmentDialog_op">Доступные операции: </label></td>
    <td>
    	<select data-placeholder="Доступные операции" style="width: 300px;" name="operations" id="editAppointmentDialog_op" size="1" multiple="multiple">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
    </td>
  </tr>
</table>
</form>
</div>

<div id="addAppointmentDialog" title="Добавление должности">
  <form id="addAppointmentDialogForm" action="">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="addAppointmentDialog_dept">Подразделение: </label></td>
    <td>
    <select name="sector" id="addAppointmentDialog_dept" style="width: 300px;">
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="addAppointmentDialog_app">Должность: </label></td>
    <td><input id="addAppointmentDialog_app" name="appointment" type="text" /></td>
  </tr>
  <tr>
    <td>Доступные операции: </td>
    <td>
    	<select data-placeholder="Доступные операции" style="width: 300px;" name="operations" id="addAppointmentDialog_op" size="1" multiple="multiple">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
    </td>
  </tr>
</table>
</form>
</div>

<div id="removeAppointmentDialog" title="Удаление должности">
<input type="hidden" id="removeAppointmentDialog_id" name="id" />
<p>Вы действительно хотите удалить должность <span style="color: #F00;"></span> из списка?</p>
</div>