<?php

include "init.php";

$tableName = "grinding"; // имя таблицы

$date1 = "2012-12-17 08:00";
$date2 = "2012-12-18 08:00";



if (isset($date1) and isset($date2)) {
	$sql = "SELECT `id`, `thickness` FROM `$tableName` WHERE `timestamp` BETWEEN '$date1' AND '$date2'";
}
echo "$sql <br>";
$result = mysql_query($sql);

$n = 0; $prevThickness = -1; $counter = 0; $countAll = 0;

$number = mysql_num_rows($result); $i = 1;
while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	if ($row['thickness'] !=  $prevThickness || $i == $number) {
		if ($prevThickness != -1) {
			if ($i == $number) $countAll++;
			$resultArray[] = $prevThickness . ' — ' . $counter;
			echo $prevThickness . ' — ' . $counter . '<br>';
			$countAll += $counter;
		}
		$counter = 1;
		$prevThickness = $row['thickness'];
	} else {
		$counter++;
	}
	$i++;
}
echo "Всего: $countAll";
// print_r($resultArray);

mysql_free_result($result);

?>