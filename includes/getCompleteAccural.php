<?php

require_once('init.php');

$ACCESSED_MODULE = 9;
$_LOGGING = false;
include('cerber.php');

if (isset($_GET['showMonth'])) $showMonth = mysql_real_escape_string($_GET['showMonth']);
	else {echo json_encode(array("result"=>"error","text"=>"Неверно указана дата (showMonth).","source"=>"getCompleteAccural.php")); die();}
if (isset($_GET['showNextMonth'])) $showNextMonth = mysql_real_escape_string($_GET['showNextMonth']);
	else {echo json_encode(array("result"=>"error","text"=>"Неверно указана дата (showNextMonth).","source"=>"getCompleteAccural.php")); die();}
	
?>


<div style="width: auto; overflow: scroll; overflow-x: auto; overflow-y: hidden;" />
<table border="0" cellspacing="0" cellpadding="0" id="statCommonTable" class="hoverable accural">
  <thead>
<?php


	// выбираем нужных сотрудников
	if (isset($_GET['workers']) & $_GET['workers'] != "") {
		// указан список сотрудников
		$workers = mysql_real_escape_string($_GET['workers']);
		
		$sqlWorkers = "SELECT `id`, `name_family`, `name_first`, `name_middle` from `workers` WHERE `id` IN (" . $workers . ")";
		$resultWorkers = mysql_query($sqlWorkers, $connection_stat);
		while ($row = mysql_fetch_array($resultWorkers, MYSQL_ASSOC)) {
			$workersToDisplay[] = array('id' => $row['id'], 'name_family' => $row['name_family'], 'name_first' => $row['name_first'], 'name_middle' => $row['name_middle']);
		}
		mysql_free_result($resultWorkers);
	} else if (isset($_GET['sectors']) & $_GET['sectors'] != "") {
		// указано подразделение
		$sectors = mysql_real_escape_string($_GET['sectors']);
	
		$sqlWorkers = "SELECT `id`, `name_family`, `name_first`, `name_middle` from `workers` WHERE `sector` IN (" . $sectors . ")";
		$resultWorkers = mysql_query($sqlWorkers, $connection_stat);
		while ($row = mysql_fetch_array($resultWorkers, MYSQL_ASSOC)) {
			$workersToDisplay[] = array('id' => $row['id'], 'name_family' => $row['name_family'], 'name_first' => $row['name_first'], 'name_middle' => $row['name_middle']);
		}
		mysql_free_result($resultWorkers);
	} else {
		echo ("<p align='center'>Выберите подразделение или сотрудника</p>");
		die();
	}
	
	
	
	$d1 = 1;
	$d2 = date("t", $showMonth); // количество дней в месяце
	
	echo "<tr>
	<th width='150' class='accuralHeader leftColumn'>&nbsp;</th>";
	for ($d = $d1; $d <= $d2; $d++) {
		echo ("<th>$d</th>");
	}
	echo "<th>Сумма</th>";
	echo "</tr>
	</thead>";
	
	// для каждого проводим выборку работ по дням
	foreach ($workersToDisplay as $key => $worker) {
		echo "<tr>
		<td class='leftColumn'>" . $worker['name_family'] . " " .
		mb_substr($worker['name_first'], 0, 1, 'utf8') . ". " . 
		mb_substr($worker['name_middle'], 0, 1, 'utf8') . ".</td>";
		
		// дальше проходим по каждому дню за установленный диапазон
		$workerTotalPerPeriod = 0;
		for ($d = $d1; $d <= $d2; $d++) {
			if ($d < 10) $d = '0' . $d;
			$sum = 0;
			$begin_start = $showMonth . '-' . $d . ' 08:00';
			$begin_stop = ($d < $d2) ? $showMonth . '-' . ($d + 01) . ' 08:00' : $showNextMonth . '-1' . ' 08:00'; // проверка в последнем дне месяца
			$end_start = $showMonth . '-' . $d . ' 08:00';
			$end_stop = ($d < $d2) ? $showMonth . '-' . ($d + 01) . ' 08:00' : $showNextMonth . '-1' . ' 08:00'; // проверка в последнем дне месяца
			
			//echo $begin_start . " — " . $begin_stop . "<br />";
			
			$sqlAccural = "SELECT production.value, rates.rate_1, rates.rate_2, rates.cond_2, rates.rate_3, rates.cond_3
			
			FROM `workers` AS workers
			INNER JOIN `timeboard` AS timeboard
			INNER JOIN `production` AS production
			INNER JOIN `rates` AS rates
			
			ON workers.id = timeboard.`worker_id`
			AND timeboard.time_begin = production.time_begin
			AND timeboard.time_end = production.time_end
			AND timeboard.operation_id = production.operation_id
			AND timeboard.operation_id = rates.operation_id
			AND workers.appointment = rates.app_id
			
			WHERE workers.id = " . $worker['id'] . " AND 
			timeboard.time_begin BETWEEN '" . $begin_start . "' AND '" . $begin_stop . "' AND
			timeboard.time_end BETWEEN '" . $end_start . "' AND '" . $end_stop . "' AND
			ADDTIME(rates.start_date, '8:0:0') < '" . $begin_start . "' 
			ORDER BY rates.start_date DESC LIMIT 1";
			
			
			
			$sum = 0;
			$resultAccural = mysql_query($sqlAccural, $connection_stat);
			while ($rowAccural = mysql_fetch_array($resultAccural, MYSQL_ASSOC)) {
				if (isset($rowAccural['cond_3']) & $rowAccural['value'] >= $rowAccural['cond_3']) $rate = $rowAccural['rate_3'];
					else if (isset($rowAccural['cond_2']) & $rowAccural['value'] >= $rowAccural['cond_2']) $rate = $rowAccural['rate_2'];
						else $rate = $rowAccural['rate_1'];
				$sum = $sum + $rate * $rowAccural['value'];
			}
			mysql_free_result($resultAccural);
			
			echo ("<td onclick='javascript:showDetails(" . $worker['id'] . ", \"" . substr($begin_start, 0, 10) . "\")'>" . (round($sum)) . "</td>");
			$workerTotalPerPeriod += $sum;
		}
		echo "<td style='background-color: #CFCFFF;'>$workerTotalPerPeriod</td>";
		echo "</tr>";
	}
?>
</table>
</div>