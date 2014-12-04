<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 7;
include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/module.timeboard.js"></script>

<div class="timeBoardNav">
  <div class="timeBoardNavInner">
        <span style="padding: 5px 5px 0 0; vertical-align: top; display: inline-block; font-size: 14px;">Переход на дату:</span>
		<input id="timeBoarNavDate" type="text" style="margin: 4px 0 0 0;" />
  </div>
  <div class="timeBoardNavInner">
        <span style="padding: 5px 5px 0 0; vertical-align: top; display: inline-block; font-size: 14px;">Фильтр операций:</span>
        <select multiple="multiple" id="timeBoarNavOperation" name="operation" style="width: 300px;" data-placeholder="Выберите одну или несколько операций">
<?php
	foreach ($allOperations as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
        <a href="javascript:applyOpFilter();" class="asButton" style="margin: 3px 0 0 0; vertical-align: top">применить</a>
        <a href="javascript:clearOpFilter();" class="asButton" style="margin: 3px 0 0 0; vertical-align: top">сбросить</a>
        
    </div>
</div>
<script language="javascript">
function make(somethng) {
	$(document).ready(function(e) {
        $('#timeBoarNavOperation').trigger(somethng);
    });
}
</script>



<?php
if ( isset($_GET['operations']) ) {
	 // задан фильтр операций
	$temp = mysql_real_escape_string($_GET['operations']);
	$temp = explode(",", $temp);
	foreach ($temp as $value) $selectedOperations[$value] = getOperationName($value);
} else $selectedOperations = $allOperations;

$lastDate = (isset($_GET['lastdate'])) ? mysql_real_escape_string($_GET['lastdate']) : date("Y-m-d");
$col4Date = date("Y-m-d", strtotime($lastDate) + 60*60*24);
$col3Date = $lastDate;
$col2Date = date("Y-m-d", strtotime($col3Date) - 60*60*24);
$col1Date = date("Y-m-d", strtotime($col2Date) - 60*60*24);
echo "
<script language='javascript'>
days = new Array();
days[0] = '" . $col1Date . "';
days[1] = '" . $col2Date . "';
days[2] = '" . $col3Date . "';
</script>";

echo "<div class='timeBoardHeaderContainer'>
	<div class='timeBoardHeaderDay'>&nbsp;</div>
	<div class='timeBoardHeaderDay'>
		<div class='timeBoardHeaderNavi' style='left: 25px;'>
			<a href='index.php?act=timeboard&lastdate=$col2Date' class='asDateNavButton'> << </a>
		</div>
		<p>$col1Date</p>
	</div>
	<div class='timeBoardHeaderDay'><p>$col2Date</p></div>
	<div class='timeBoardHeaderDay'>
		<div class='timeBoardHeaderNavi' style='right: 25px;'>
			<a href='index.php?act=timeboard&lastdate=$col4Date' class='asDateNavButton'> >> </a>
		</div>
		<p>$col3Date</p>
	</div>
</div>";



foreach ( $selectedOperations as $operIndex => $operValue ) {
	echo "<div class='timeBoardOperContainer'>";
	$sql  = "SELECT DISTINCT `time_begin`, `time_end` FROM `timeboard` WHERE `time_begin` BETWEEN '" . $col1Date . " 08:00' AND '" . $col2Date . " 07:59' AND `operation_id` = " . $operIndex . " ORDER BY `time_begin` ASC";
	$resultDay1 = mysql_query($sql, $connection_stat) or die(mysql_error());
	//echo mysql_num_rows($resultDay1) . '<br />';
	
	$sql  = "SELECT DISTINCT `time_begin`, `time_end` FROM `timeboard` WHERE `time_begin` BETWEEN '" . $col2Date . " 08:00' AND '" . $col3Date . " 07:59' AND `operation_id` = " . $operIndex . " ORDER BY `time_begin` ASC";
	$resultDay2 = mysql_query($sql, $connection_stat) or die(mysql_error());
	//echo mysql_num_rows($resultDay2) . '<br />';
	
	$sql  = "SELECT DISTINCT `time_begin`, `time_end` FROM `timeboard` WHERE `time_begin` BETWEEN '" . $col3Date . " 08:00' AND '" . $col4Date . " 07:59' AND `operation_id` = " . $operIndex . " ORDER BY `time_begin` ASC";
	$resultDay3 = mysql_query($sql, $connection_stat) or die(mysql_error());
	//echo mysql_num_rows($resultDay3) . '<br />';
	
	echo "<table width='100%' cellpadding='5' cellspacing='0'>
	<tr>
	<td class='timeBoardOperTitle'>" . $operValue . "</td>\n"; // название операции
	
	foreach (array($resultDay1, $resultDay2, $resultDay3) as $dayIndex => $resultDay) {
		// заполняем по каждому дню
		$odd = (date("d", strtotime( $col1Date )) + $dayIndex )% 2; // определяем, чётный ли день в колонке
		if($odd) {
			echo "<td class='timeBoardDay timeBoardDayOdd' id='day_$dayIndex"."_op_$operIndex'";
			if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) < 2 ) echo " onclick=javascript:void(0);'";
			echo ">\n";
		} else {
			echo "<td class='timeBoardDay' id='day_$dayIndex"."_op_$operIndex'";
			if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) < 2 ) echo " onclick=javascript:void(0);'";
			echo ">\n";
		}
	
		if (mysql_num_rows($resultDay) == 0) {
			echo "Нет данных";
		} else {	
			while ($rowTime = mysql_fetch_array($resultDay, MYSQL_NUM)) {
				echo "<div  class='timeBoardPeriod'>
				<div class='timeBoardTimeColumn'>\n";
				
				// вывод номера смены или времени
				$d1 = date("H:i", strtotime($rowTime[0]));
				$d2 = date("H:i", strtotime($rowTime[1]));
				if ($d1 == '08:00' & $d2 == '16:00') $timeMark = 'I смена';
				else if ($d1 == '16:00' & $d2 == '00:00') $timeMark = 'II смена';
				else if ($d1 == '00:00' & $d2 == '08:00') $timeMark = 'III смена';
				else $timeMark = $d1 . '—<br />' . $d2;
				echo  "$timeMark</div>
				<div class='timeBoardNamesColumn'>\n";
				
				// далее получаем все id сотрудников в данной ячейке и заменяем их на ФИО
				$sqlNames = "SELECT `name_family`, `name_first`, `name_middle` FROM `workers` WHERE `id` IN 
					(
					SELECT `worker_id` FROM `timeboard` WHERE `time_begin` = '" . $rowTime[0] . "' AND `time_end` = '" . $rowTime[1] . "' AND `operation_id` = " . $operIndex . "
					)";
				$resultNames = mysql_query($sqlNames, $connection_stat);
				
				while ($rowNames = mysql_fetch_array($resultNames, MYSQL_NUM)) {
					$nameF = ( $rowNames[1] != '' ) ? mb_substr($rowNames[1], 0, 1, 'utf8') . '.' : '';
					$nameM = ( $rowNames[2] != '' ) ? mb_substr($rowNames[2], 0, 1, 'utf8') . '.' : '';
					
					echo $rowNames[0] . ' ' . $nameF . ' ' . $nameM . "<br />\n";
				}
				echo "</div>\n
				</div>\n";
			}
		}
		echo "</td>"; // .timeBoardDay
	}
	echo "</tr></table>\n";
	echo "</div>\n"; // .timeBoardOperContainer
}
?>

<div id="editTimeboardCellDialog" title="Редактирование ячейки табеля">
	
    Дата: <span id="editTimeboardCellDialog_date"></span><br />
    Операция: <span id="editTimeboardCellDialog_operation"></span>
	<hr color="#CFCFCF" />
    
    <p>Работавшие сотрудники:</p>
	<form id="currentTimeboard">   
        <input type="hidden" id="editTimeboardCellDialog_dateInput" />
        <input type="hidden" id="editTimeboardCellDialog_operationInput" />
        
        <div id="timeboardList">
            <p>Нет данных</p>
        </div>
    </form>
      <p align="center"><a href="javascript:addNewPeriod();"><span class="asButton">Добавить новую смену</span></a></p>
      
      <!--
      <div id="timeboardListNewPeriods">
          <p>Добавить новую смену:</p>
        <div class="editTimeboardCellDialogLabel">Время:</div>
          <input type="text" id="timeboardNewPeriod_dateBegin" class="timeBoarDate" value="начало работы" style="width: 140px;" /> — 
          <input type="text" id="timeboardNewPeriod_dateEnd" class="timeBoarDate" value="окончание работы" style="width: 140px;" />
          <p align="center">
              <a href="javascript:periodPreset(1)"><span class="asButton">I смена</span></a>&nbsp;<a href="javascript:periodPreset(2)"><span class="asButton">II смена</span></a>&nbsp;<a href="javascript:periodPreset(3)"><span class="asButton">III смена</span></a>
          </p>
          
          <p><div class="editTimeboardCellDialogLabel">&nbsp;</div>
          <select id="timeboardNewPeriod_workers" class="editTimeboardCellDialogInput" multiple="multiple" data-placeholder="Выберите одного или нескольких сотрудников"></select></p>
      </div>
      -->
      
      <hr color="#CFCFCF" />
    <center>
      <a href="#"><span class="asButtonRed">Очистить все смены за этот день</span></a>
    </center>
	
</div>