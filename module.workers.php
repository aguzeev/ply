<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 2;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script language="javascript" src="js/chosen.jquery.js"></script>
<script type="text/javascript" src="js/module.workers.js"></script>



<div class="timeBoardNav">
  <div class="timeBoardNavInner">
  		<div style="text-align: left; margin: 5px 0 0 0;">
        <span style="padding: 5px 5px 0 0; vertical-align: top; display: inline-block; font-size: 14px;">Подразделение:</span>
        <select id="timeBoarNavSector" name="sector" style="width: 300px;" data-placeholder="Выберите подразделение">
        	<option value="0">Все подразделения</option>
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
        </div>
  </div>
    <div class="timeBoardNavInner">
        <div style="text-align: left; margin: 5px 0 0 0;">
        <span style="padding: 5px 5px 0 0; vertical-align: top; display: inline-block; font-size: 14px;">Должность:</span>
        <select id="timeBoarNavApp" style="width: 400px;" multiple="multiple" data-placeholder="Укажите должность в выбранном подразделении">
        </select><br />
        <input name="showinactive" id="showinactive" type="checkbox" value="" title="Показать в списке сотрудников, которые были отключены" />
        <label for="showinactive" title="Показать в списке сотрудников, которые были отключены">&nbsp;Показать устаревшие позиции</label>
        </div>
    </div>
  <div class="timeBoardNavInner">
	  <a class="asButton" href="javascript:applyFilter();">применить</a>
      <a class="asButton" href="javascript:clearFilter();">сбросить</a>
    </div>
</div>

<div style="width: 98%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
	<input type="button" class="addButton" value="Добавить сотрудника" onclick="javascript:addWorker();" />
    <?php endif; ?>
</div>


<table width="96%" style="margin:0 2% 0 2%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable hoverable">
	<thead>
      <tr>
        <th width="22%" align="left" valign="bottom" class="columnIndent borderB">Фамилия, Имя, Отчество</th>
        <th width="15%" align="left" valign="bottom" class="borderB">Отдел</th>
        <th width="17%" align="left" valign="bottom" class="borderB">Должность</th>
        <th width="26%" colspan="2" align="left" valign="bottom" class="borderB">Допустимые операции</th>
        <th width="10%" align="left" valign="bottom" class="borderB">Приём на работу</th>
        <th width="10%" align="left" valign="bottom" class="borderB">Уход с работы</th>
      </tr>
  </thead>
<tbody>
<?php
	
	$query = "SELECT * FROM `workers`";
	if  (isset($_GET['showinactive']) ) $query = $query . " WHERE `isActive` IN (0, 1)";
		else  $query = $query . " WHERE `isActive` = 1";
	if  (isset($_GET['appoints']) ) $query = $query . " AND `appointment` IN (" . mysql_real_escape_string($_GET['appoints']) . ")";
		else if (isset($_GET['sectors']) ) {
			if ($_GET['sectors'] != 0) $query = $query . " AND `sector` IN (" . mysql_real_escape_string($_GET['sectors']) . ")";
		}
	
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<tr id='rowID_" . $row['id'] . "'";
		if ($row['isActive'] == 0) echo " class='notActive'";
		echo "><td class='columnIndent'>" . $row['name_family'] . " "
		. $row['name_first'] . " "
		. $row['name_middle'];
		if ($row['isActive'] == 0) echo " (в архиве)";
		echo "</td><td>" . $dept[ $row['sector'] ] . "</td>
		<td>" . $apps[ $row['appointment'] ] . "</td>
		<td valign='top' width='13%'>";
		
		// Вывод общих для должности операций
		echo '<span class="allowedOperationsDef"><strong>Общие по должности:</strong></span>';
		if ( $defaultOp[ $row['appointment'] ] != 'null' and $defaultOp[ $row['appointment'] ] != ''  ) {
			foreach ( $defaultOp[ $row['appointment'] ] as $index => $value ) {
				// в value — id разрешённых должжностей
				echo '<br /><span class="allowedOperationsDef" style="margin-left: 10px;">' . $allOperations[$value] . '</span>';
			}
		} else {
			echo '<br /><span class="allowedOperationsDef" style="margin-left: 10px;">Не заданы</span>';
		}
		echo "</td>
		<td valign='top' width='13%'>";
		
		echo '<span class="allowedOperationsDef"><strong>Персональные:</strong></span>';
		
		if ( ($row['additionalOperations']) ) { // проверка персональных допустимых операций у сотрудника
			$addOp = unserialize( $row['additionalOperations'] );	
			foreach ( $addOp as $value ) {
				// в value — id разрешённых должжностей
				echo '<br /><span class="allowedOperationsDef" style="margin-left: 10px;">' . $allOperations[$value] . '</span>';
			}
		} else {
			echo '<br /><span class="allowedOperationsDef" style="margin-left: 10px;">Не заданы</span>';
		}
		
		$dateStart = (isset($row['date_start_work'])) ? date("d.m.Y", strtotime($row['date_start_work'])) : "Не указано";
		$dateFinish = (isset($row['date_finish_work'])) ? date("d.m.Y", strtotime($row['date_finish_work'])) : "Не указано";
		
		echo "
		</td>
		<td valign='top'><span class='allowedOperationsDef'>" . $dateStart . "</span></td>
		<td valign='top'><span class='allowedOperationsDef'>" . $dateFinish . "</span>";
		
		// editBar
		if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ) {
			echo "<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $row['id'] . "'>
			<tr>
				<td><a href='javascript:editWorker(" . $row['id'] . ")'><img src='img/edit.png' width='16' height='16' title='Редактировать' /></a></td><td>";
				if ($row['isActive'] == 1) echo "<a href='javascript:removeWorker(" . $row['id'] . ")'><img src='img/remove.png' width='16' height='16' title='Удалить' /></a>";
			echo "</td></tr>
			</table>";
		}
		echo "</td>
		</tr>\n";
	}
	?>
</tbody>
</table>


<div id="editWorkerDialog" title="Редактирование данных сотрудника">
  <form id="editWorkerDialogForm" action="editWorker.php">
	<input type="hidden" id="editWorkerDialog_id" name="workerId" />
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="editWorkerDialog_nFamily">Фамилия: </label></td>
    <td><input id="editWorkerDialog_nFamily" name="name_family" type="text" /> <span class="dialogTip">(обязательно)</span></td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_nFirst">Имя: </label></td>
    <td><input id="editWorkerDialog_nFirst" name="name_first" type="text" /> <span class="dialogTip">(обязательно)</span></td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_nMid">Отчество: </label></td>
    <td><input id="editWorkerDialog_nMid" name="name_middle" type="text" /> <span class="dialogTip">(обязательно)</span></td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_sector">Отдел: </label></td>
    <td>
    <select name="sector" id="editWorkerDialog_sector" style="width: 250px;" onchange="javascript:renewApps('editWorkerDialog_app', this.value, 0)">
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select> <span class="dialogTip">(обязательно)</span>
    </td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_app">Должность: </label></td>
    <td>
    <select data-placeholder="Должность" name="appointment" id="editWorkerDialog_app" style="width: 250px;">
    </select> <span class="dialogTip">(обязательно)</span>
    </td>
  </tr>
  <tr>
  	<td colspan="2" align="center" style="padding-top: 15px;">Разрешённые операции:</td>
  </tr>
  <tr>
    <td>Определённые должностью:</td>
    <td>
    	<span class="allowedOperationsDef" id="editWorkerDialog_defOp"></span>
    </td>
  </tr>
  <tr>
  	<td><label for="editWorkerDialog_op">Персональные:</label></td>
    <td>
    <select data-placeholder="Доступные операции" style="width: 250px;" name="op" id="editWorkerDialog_op" size="1" multiple="multiple">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
  	<td>&nbsp;</td>
    <td><span class="dialogTip">Оставьте это поле пустым, чтобы разрешить только те операции, которые определены должностью.</span></td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_status">Статус</label></td>
    <td>
    <select id="editWorkerDialog_status" name="status" type="text" style="width: 160px;">
    	<option value="1">Активен (в штате)</option>
        <option value="0">Не активен (уволен)</option>
    </select> <span class="dialogTip">(обязательно)</span>
    </td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_dStart">Приём на работу: </label></td>
    <td><input data-type="datepick" id="editWorkerDialog_dStart" name="date_start" type="text" /> <span class="dialogTip">(можно оставить пустым)</span></td>
  </tr>
  <tr>
    <td><label for="editWorkerDialog_dFinish">Уход с работы: </label></td>
    <td><input data-type="datepick" id="editWorkerDialog_dFinish" name="date_finish" type="text" /> <span class="dialogTip">(можно оставить пустым)</span></td>
  </tr>
</table>
</form>
</div>

<div id="addWorkerDialog" title="Добавление сотрудника">
  <form id="addWorkerDialogForm" action="addWorker.php">
	<input type="hidden" id="addWorkerDialog_id" name="workerId" />
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="addWorkerDialog_nFamily">Фамилия: </label></td>
    <td><input id="addWorkerDialog_nFamily" name="name_family" type="text" size="33"/></td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_nFirst">Имя: </label></td>
    <td><input id="addWorkerDialog_nFirst" name="name_first" type="text" size="33" /></td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_nMid">Отчество: </label></td>
    <td><input id="addWorkerDialog_nMid" name="name_middle" type="text" size="33" /></td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_sector">Отдел: </label></td>
    <td>
    <select name="sector" id="addWorkerDialog_sector" style="width: 250px;" onchange="javascript:renewApps('addWorkerDialog_app', this.value, 0)">
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_app">Должность: </label></td>
    <td>
    <select data-placeholder="Должность" name="appointment" id="addWorkerDialog_app" style="width: 250px;">
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_op">Разрешённые операции: </label></td>
    <td>
    <select data-placeholder="Доступные операции" style="width: 250px;" name="operations" id="addWorkerDialog_op" size="1" multiple="multiple">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
    <td colspan="2">Оставьте это поле пустым, чтобы разрешить операции, доступные по умолчанию в должности</td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_dStart">Приём на работу: </label></td>
    <td><input data-type="datepick" id="addWorkerDialog_dStart" name="date_start" type="text" /></td>
  </tr>
  <tr>
    <td><label for="addWorkerDialog_dFinish">Уход с работы: </label></td>
    <td><input data-type="datepick" id="addWorkerDialog_dFinish" name="date_finish" type="text" /></td>
  </tr>
</table>
</form>
</div>


<div id="removeWorkerDialog" title="Удаление сотрудников">
<input type="hidden" id="removeWorkerDialog_id" name="workerId" />
<p>Вы действительно хотите удалить сотрудника <span style="color: #F00;"></span> из списка?</p>
</div>