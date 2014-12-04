<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 5;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/module.rates.js"></script>
<script type="text/javascript" src="js/jquery.json-2.3.min.js"></script>


<div class="timeBoardNav">
  <div class="timeBoardNavInner">
        <div style="text-align: left; margin: 5px 0 0 0;">
        <span style="padding: 5px 5px 0 0; vertical-align: top; display: inline-block; font-size: 14px;">Фильтр операций:</span>
        <select multiple="multiple" id="timeBoarNavOperation" name="operation" style="width: 300px;" data-placeholder="Выберите одну или несколько операций">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
        <a href="javascript:applyFilter()" class="asButton" style="margin: 2px 0 0 3px; vertical-align: top;">применить</a>
        <a href="javascript:clearFilter()" class="asButton" style="margin: 2px 0 0 0; vertical-align: top">сбросить</a>
        </div>
        
        
  </div>
</div>

<div style="width: 90%; margin: 0 5%;">
</div>
<div style="width: 90%; margin: 0 5%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
	<input type="button" class="addButton" value="Добавить тариф" onclick="javascript:addRate();" />
    <?php endif; ?>
</div>

<table width="90%" style="margin: 10px 5% 0 5%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable hoverable">
	<thead>
      <tr>
        <th width="11%" align="left" valign="bottom" class="columnIndent borderB">Статус</th>
        <th width="17%" align="left" valign="bottom" class="borderB">Операция</th>
        <th width="20%" align="left" valign="bottom" class="borderB">Должность</th>
        <th width="12%" align="left" valign="bottom" class="borderB">Дата установления</th>
        <th width="7%" align="center" valign="bottom" class="borderB">Тариф 1</th>
        <th width="7%" align="center" valign="bottom" class="borderB bordL">Тариф 2</th>
        <th width="7%" align="center" valign="bottom" class="borderB bordR">Условие</th>
        <th width="7%" align="center" valign="bottom" class="borderB">Тариф 3</th>
        <th width="7%" align="center" valign="bottom" class="borderB">Условие</th>
        <th width="5%" align="center" valign="bottom" class="borderB">&nbsp;</th>
      </tr>
  </thead>
<?php

	if (isset($_GET['operations'])) {
		// задан фильтр операций
		$operations = mysql_real_escape_string($_GET['operations']);
		$sqlRates = "SELECT * FROM `rates` WHERE `operation_id` IN (" . $operations . ") ORDER BY `operation_id`, `app_id`, `start_date` DESC";
	} else {
		// показываем тарифы по всем операциям
		$sqlRates = "SELECT * FROM `rates` ORDER BY `operation_id`, `app_id`, `start_date` DESC";
	}

	$resultRates = mysql_query($sqlRates, $connection_stat);
	if (mysql_num_rows($resultRates) == 0) {
		echo "Нет данных";
		die();
	}
	$prevOperation_id = 0; $prevApp_id = 0;
	while ($rowRates = mysql_fetch_array($resultRates, MYSQL_ASSOC)) {
		
		echo "";
		// признак новой операции или новой должности в операции
		$newRatesGroup = 	( ($rowRates['operation_id'] != $prevOperation_id) or ($rowRates['app_id'] != $prevApp_id) ) &
							(strtotime($rowRates['start_date']) <= date("U"));
		
		if ( $newRatesGroup ) { // началось перечисление тарифов по новой операции
			// первый поступивший тариф с датой, меньшей или равное текущей, — активный. он используется для расчёта начислений в данный момент
			echo "<tr id='rowID_" . $rowRates['id'] . "' class='activeRates'>
			<td class='rateStatus columnIndent'>Используется</td>
			<td>" . getOperationName($rowRates['operation_id']) . "</td>";
			$prevOperation_id = $rowRates['operation_id'];
			$prevApp_id = $rowRates['app_id'];
		} else {
			// все остальные тарифы в этой группе
			// 1 — т.е. устаревшие
			if (strtotime($rowRates['start_date']) < date("U")) {
				echo "<tr class='alreadyUsedRates' id='rowID_" . $rowRates['id'] . "'>
				<td class='rateStatus columnIndent'>В архиве</td>
				<td>" . getOperationName($rowRates['operation_id']) . "</td>";
			} else {
				// ещё не активные
				echo "<tr class='futureRates' id='rowID_" . $rowRates['id'] . "'>
				<td class='rateStatus columnIndent'>Будущий</td>
				<td>" . getOperationName($rowRates['operation_id']) . "</td>";
			}
		}
    echo "
    <td>" . getAppName($rowRates['app_id']) . "</td>
    <td>" . date("d.m.Y", strtotime($rowRates['start_date'])) . "</td>
    <td align='center'>" . $rowRates['rate_1'] . "</td>
    <td align='center' class='bordL'>" . $rowRates['rate_2'] . "</td>
    <td class='bordR'><span style='margin-left: 5px'>≥ " . $rowRates['cond_2'] . " м³</span></td>
    <td align='center'>" . $rowRates['rate_3'] . "</td>
    <td><span style='margin-left: 5px'>≥ " . $rowRates['cond_3'] . "м³</span></td>
    <td align='right'>";
	if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ) {
		echo "<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $rowRates['id'] . "'>
			<tr>
			<td><a href='javascript:editRate(" . $rowRates['id'] . ")'><img src='img/edit.png' width='16' height='16' title='Редактировать' /></a></td>
			<td><a href='javascript:deleteRate(" . $rowRates['id'] . ")'><img src='img/remove.png' width='16' height='16' title='Удалить' /></a></td>
			</tr>
		</table>";
	}
	echo "</td>
</tr>
  ";
	
	
	/*
	if ( strtotime($rowRates['start_date']) > date("U") ) echo "<tr id='rowID_" . $rowRates['id'] . "'>";
		else echo "<tr class='alreadyUsed' id='rowID_" . $rowRates['id'] . "'>"; // уже используемые тарифы помечены серым
    echo"
	<td class='columnIndent'>" . getOperationName($rowRates['operation_id']) . "</td>
    <td>" . getAppName($rowRates['app_id']) . "</td>
    <td>" . date("d.m.Y", strtotime($rowRates['start_date'])) . "</td>
    <td align='center'>" . $rowRates['rate_1'] . "</td>
    <td align='center' class='bordL'>" . $rowRates['rate_2'] . "</td>
    <td class='bordR'><span style='margin-left: 5px'>≥ " . $rowRates['cond_2'] . " м³</span></td>
    <td align='center'>" . $rowRates['rate_3'] . "</td>
    <td><span style='margin-left: 5px'>≥ " . $rowRates['cond_3'] . "м³</span></td>
    <td align='right'>";
	
	if ( strtotime($rowRates['start_date']) > date("U") ) { // уже используемые тарифы нельзя редактировать
		echo "
		<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $rowRates['id'] . "'>
		<tr>
		<td><a href='javascript:editRate(" . $rowRates['id'] . ")'><img src='img/edit.png' width='16' height='16' title='Редактировать' /></a></td>
		<td><a href='javascript:deleteRate(" . $rowRates['id'] . ")'><img src='img/remove.png' width='16' height='16' title='Удалить' /></a></td>
		</tr>
		</table>";
	} else {
		echo "<table width='40' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $rowRates['id'] . "'>
		<tr><td><img src='img/not-available.png' title='Редактирование невозможно, поскольку этот тариф уже используется' />
		</td></tr></table>";
	}
	
	echo "</td>
</tr>
  ";
  */
	}
?>
</table>


<div id="ratesDialog" title="Редактирование тарифа">
  <form id="ratesDialogForm" action="editRate.php">
        <input type="hidden" id="ratesDialog_id" name="rateId" />
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
        <td width="110" align="right"><label for="ratesDialog_op">Операция: </label></td>
        <td>
        	<select id="ratesDialog_op" name="op" type="text" style="width: 250px;">
            <?php
				foreach ($allOperations as $key => $value) {
					echo "<option value='" . $key . "'>" . $value . "</option>";
				}
			?>
            </select>
            </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_app">Должность: </label></td>
        <td>
        	<select data-placeholder="Сначала выберите операцию" id="ratesDialog_app" name="app" type="text" style="width: 250px;">
            </select>
        </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_date_start">Дата
          установления: </label></td>
        <td valign="top"><input id="ratesDialog_date_start" name="date_start" type="text" />
        </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_rate1">Тариф 1: </label></td>
        <td><input id="ratesDialog_rate1" name="rate1" type="text" />
        
        </td>
        </tr>
        
        <tr><td>&nbsp;</td><td></td></tr>
        
        <tr>
        <td align="right">
        <label for="ratesDialog_rate2">Тариф 2: </label></td>
        <td><input id="ratesDialog_rate2" name="rate2" type="text" />
        </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_cond2">Условие: ≥</label></td>
        <td><input id="ratesDialog_cond2" name="cond2" type="text" />
        </td>
        </tr>
        
        <tr><td>&nbsp;</td><td></td></tr>
        
        <tr>
        <td align="right">
        <label for="ratesDialog_rate3">Тариф 3: </label></td>
        <td><input id="ratesDialog_rate3" name="rate3" type="text" />
        </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_cond3">Условие: ≥</label></td>
        <td><input id="ratesDialog_cond3" name="cond3" type="text" />
        </td>
        </tr>
      </table>
    </form>
</div>
<div id="deleteRateDialog" title="Удаление тарифа">
	<input type="hidden" id="deleteRatesDialog_id" name="rateId" />
	<p>Вы действительно хотите удалить этот тариф?</p>
</div>