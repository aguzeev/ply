<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 6;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/module.fixedprice.js"></script>
<script src="js/jquery.tmpl.min.js"></script>


<div style="width: 98%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
	<input type="button" class="addButton" value="Добавить должность" onclick="javascript:addAppointment();" />
    <?php endif; ?>
</div>




<table width="70%" style="margin: 10px 15% 0 15%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable hoverable">
	<thead>
      <tr>
        <th width="15%" align="left" valign="bottom" class="columnIndent borderB">Статус</th>
        <th width="20%" align="left" valign="bottom" class="borderB">Подразделение</th>
        <th width="25%" align="left" valign="bottom" class="borderB">Должность</th>
        <th width="15%" align="left" valign="bottom" class="borderB">Базовая часть з. п. </th>
        <th colspan="2" align="left" valign="bottom" class="borderB">Дата установления</th>
      </tr>
  </thead>
<?php


	// показываем все тарифы
	$sqlRates = "SELECT * FROM `fixedprice` ORDER BY `app_id` ASC, `date_start` DESC";

	$resultRates = mysql_query($sqlRates, $connection_stat);
	if (mysql_num_rows($resultRates) == 0) {
		echo "Нет данных";
		die();
	}
	$prevApp_id = 0;
	while ($rowRates = mysql_fetch_array($resultRates, MYSQL_ASSOC)) {
		
		echo "";
		// признак новой должности
		$newRatesGroup =	( $rowRates['app_id'] != $prevApp_id )  &
							(strtotime($rowRates['date_start']) <= date("U"));
		
		if ( $newRatesGroup ) { // началось перечисление тарифов по новой должности
			// первый поступивший тариф с датой, меньшей или равное текущей, — активный. он используется для расчёта начислений в данный момент
			$dept_temp = getDeptByApp($rowRates['app_id']);
			echo "<tr id='rowID_" . $rowRates['id'] . "' class='activeRates'>
			<td class='rateStatus columnIndent'>Используется</td>
			<td>" . $dept_temp['title'] . "</td>";
			$prevApp_id = $rowRates['app_id'];
		} else {
			// все остальные тарифы в этой группе
			// 1 — т.е. устаревшие
			if (strtotime($rowRates['date_start']) < date("U")) {
				$dept_temp = getDeptByApp($rowRates['app_id']);
				echo "<tr class='alreadyUsedRates' id='rowID_" . $rowRates['id'] . "'>
				<td class='rateStatus columnIndent'>В архиве</td>
				<td>" . $dept_temp['title'] . "</td>";
			} else {
				// ещё не активные
				$dept_temp = getDeptByApp($rowRates['app_id']);
				echo "<tr class='futureRates' id='rowID_" . $rowRates['id'] . "'>
				<td class='rateStatus columnIndent'>Будущий</td>
				<td>" . $dept_temp['title'] . "</td>";
			}
		}
		echo "<td>" . getAppName($rowRates['app_id']) . "</td>\n
		<td>" . $rowRates['price'] . "</td>\n
			<td>" . date("d.m.Y", strtotime($rowRates['date_start'])) . "</td>\n
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
	}
?>
</table>





<div id="ratesDialog" title="Редактирование базовой части зарплаты">
  <form id="ratesDialogForm" action="">
        <input type="hidden" id="ratesDialog_id" name="rateId" />
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
        <td width="150" align="right"><label for="ratesDialog_sector">Подразделение: </label></td>
        <td>
        	<select id="ratesDialog_sector" name="sector" type="text" style="width: 250px;">
            <?php
				foreach ($dept as $key => $value) {
					echo "<option value='" . $key . "'>" . $value . "</option>";
				}
			?>
            </select>
            </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_app">Должность: </label></td>
        <td>
        	<script id="ratesTemplate" type="text/x-jquery-tmpl">
				<option value=${id}>${appointment}</option>
			</script>
        	<select data-placeholder="Можете сначала выбрать подразделение" id="ratesDialog_app" name="app" type="text" style="width: 250px;">
            </select>
        </td>
        </tr>
        
        <tr>
        <td align="right"><label for="ratesDialog_price">Базовая часть выплат: </label></td>
        <td><input id="ratesDialog_price" name="price" type="text" />
        </td>
        
        <tr>
        <td align="right"><label for="ratesDialog_date_start">Дата установления: </label></td>
        <td valign="top"><input id="ratesDialog_date_start" name="date_start" type="text" />
        </td>
        </tr>
      </table>
    </form>
</div>
<div id="deleteRateDialog" title="Удаление тарифа">
	<input type="hidden" id="deleteRatesDialog_id" name="rateId" />
	<p>Вы действительно хотите удалить этот тариф?</p>
</div>