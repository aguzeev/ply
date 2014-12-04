<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 9;
include('includes/cerber.php');


if (isset($_GET['showmonth'])) {
	$dateparts = explode('-', $_GET['showmonth']);
	if( strlen($dateparts[0]) == 4 & strlen($dateparts[1]) <= 2) $showMonth = $_GET['showmonth'];
		else {echo "<script language='javascript'>showError('Неверно указана дата.', 'accural')</script>";}
} else {
	$showMonth = date("Y-m");
}

$dateparts = explode('-', $showMonth);
if ($dateparts[1] < 9) $showNextMonth = $dateparts[0] . '-0' . ($dateparts[1] + 1);
	else if ($dateparts[1] < 12) $showNextMonth = $dateparts[0] . '-' . ($dateparts[1] + 1);
		else $showNextMonth = ($dateparts[1] + 1) . '-01';
	
// echo 'showMonth: '. $showMonth . '<br />';
// echo 'showNextMonth: '. $showNextMonth;

?>


<script type="text/javascript" src="js/module.accural.js"></script>
<!-- http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js -->
<script src="js/jquery.tmpl.min.js"></script>

<div class="timeBoardNav">
  <div class="timeBoardNavInner">
        Период:<br />
        <div style="text-align: left; margin: 7px 0 0 0;">
        <select id="timeBoarNavMonth" style="width: 150px;">
			<?php
				$monthes = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
				$cm = date("m");
				foreach ($monthes as $key => $month) {
					echo "<option value='" . ($key + 1) . "'";
					if ($key == $cm - 1) echo " selected='selected'";
					echo ">$month</option>";
				}
            ?>
        </select>
        <select id="timeBoarNavYear" style="width: 80px;">
        	<?php
				for ($y = 0; $y < date("Y") - 2011; $y++) {
					echo "<option value='" . (2012 + $y) . "'>" . (2012 + $y) . "</option>";
				}
			?>
        </select>
        </div>
  </div>
  <div class="timeBoardNavInner">
        Подразделение:<br />
        <div style="text-align: left; margin: 5px 0 0 0;">
        <select id="timeBoarNavSector" name="sector" style="width: 300px;" multiple="multiple" data-placeholder="Выберите подразделение">
<?php
	foreach ($dept as $key => $value) {
		echo "<option value='" . $key . "'>" . $value . "</option>";
	}
?>
        </select>
        </div>
  </div>
  <!--<div class="timeBoardNavInner">
        Должность:<br />
        <div style="text-align: left; margin: 5px 0 0 0;">
        <select id="timeBoarNavAppointment" style="width: 200px;">
        </select>
        </div>
  </div>-->
    <div class="timeBoardNavInner">
        Сотрудник:<br />
        <div style="text-align: left; margin: 5px 0 0 0;">
        <select id="timeBoarNavNameApp" style="width: 400px;" multiple="multiple" data-placeholder="Выберите сотрудников или подразделение целиком">
        </select>
        <script id="workerAppTemplate" type="text/x-jquery-tmpl">
			<option value=${id}>${name_family} ${name_first}. ${name_middle}. — ${appointment}</option>
		</script>
        </div>
    </div>
  <div class="timeBoardNavInner">
    	<br />
	  <a class="asButton" href="javascript:applyFilter();">показать</a>
      <a class="asButton" href="javascript:clearFilter();">сбросить</a>
    </div>
</div>

<div id="completeAccural"></div> <!-- контейнер для вставки данных о начислениях -->


<?php
	if (isset($_GET['workers'])) $workers = mysql_real_escape_string($_GET['workers']);// указан список сотрудников
		else $workers = ""; 
	if (isset($_GET['sectors'])) $sectors = mysql_real_escape_string($_GET['sectors']); // указано подразделение
		else $sectors = ""; 

	echo "
<script language='javascript'>
	getCompleteAccural('" . $showMonth . "', '" . $showNextMonth . "', " . json_encode($sectors) . ", " . json_encode($workers) . ");
</script>";
?>


<div id="accuralDetails" title="">
	<script id="detailTemplate" type="text/x-jquery-tmpl">
		<table border="0" cellspacing="0" cellpadding="2" style="margin: 0 0 15px 0;">
		<tr>
			<td width="90">Операция:</td>
			<td><strong>«${operation}»</strong></td>
		</tr>
		<tr>
			<td width="90">Выработано:</td>
			<td><strong>${value}  м³</strong></td>
		</tr>
		<tr>
			<td width="90">Тариф:</td>
			<td><strong>${rate} руб./м³</strong></td>
		</tr>
		<tr>
			<td width="90">Начислено:</td>
			<td><strong>${partSum} руб.</strong></td>
		</tr>
		</table>
	</script>
	<div id="accuralDetailsCont"></div>
</div>