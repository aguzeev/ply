<?php

require_once('../init.php');

if(isset($_GET['start'])) $startDate = $_GET['start'];
if(isset($_GET['end'])) $endDate = $_GET['end'];

$_DEBUG = false;

//$startDate = '2013-12-01 08:00';
//$endDate = '2013-12-10 08:00';

$beg = strtotime($startDate);
$end = strtotime($endDate);
$beg = date("Y-m-d G:i:s", $beg); //echo ("Begin: ".$beg."<br />");
$end = date("Y-m-d G:i:s", $end); //echo ("End: ".$end."<br /><br />");

$diff = strtotime($endDate) - strtotime($startDate);
if ($diff >= 60 * 60 * 24 * 60) { // берём общие дневные значения
	echo('<span class="summaryLists" style="margin-top: 3px;">Слишком большой период для вывода информации о форматах. Распределение по форматам и толщинам доступно для периодов не длиннее 60 дней.</span>');
	die();
}


// 1. unique thicknesses
$thicknessArr = array();
$resultThickness = mysql_query("SELECT DISTINCT `thickness` FROM `lopping` WHERE `thickness` > 0 AND `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'", $connection_hardware);
while ( $row = mysql_fetch_array($resultThickness, MYSQL_NUM) ) $thicknessArr[] = $row[0];
if ( $_DEBUG ) { print_r($thicknessArr); echo "<br><br>Length arrays:<br>"; }
if ( isset($resultThickness) ) mysql_free_result($resultThickness);


// 2. unique length (&width) for each thickness
$lengthArr = array();
foreach ( $thicknessArr as $index => $thickness ) {
	$resultLength = mysql_query("SELECT DISTINCT `length` FROM `lopping` WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
	AND `thickness` = " . $thickness, $connection_hardware);
	
	$lengthArr[$index] = array();
	while ( $row = mysql_fetch_array($resultLength, MYSQL_NUM) ) $lengthArr[$index][] = $row[0];
	
	if ( $_DEBUG ) { echo "thickness " . $thickness . ": "; print_r($lengthArr[$index]); echo "<br>"; }
}
if ( isset($resultLength) ) mysql_free_result($resultLength);



// 3. counting
$quant = array();
$difficulty = 0; $tooDifficultFlag = false;

foreach ( $thicknessArr as $index => $thickness ) {
	foreach ( $lengthArr[$index] as $index2 => $length ) {
		$difficulty += sizeof($lengthArr[$index]);
		if ( $difficulty <= 12 ) {
			$sqlQuant = "SELECT COUNT(`id`) FROM `lopping` WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "' AND `thickness` = " . $thickness . " AND `length` = " . $length;
			if ( $_DEBUG ) echo $sqlQuant . "<br>";
			$resultQuant = mysql_query($sqlQuant, $connection_hardware);
			$quant[] = array($length, $thickness, mysql_result($resultQuant, 0));
			
			if ( $_DEBUG ) { echo "thickness: " . $thickness . ", length: " . $length . ", quant " . $quant . "<br>"; }
		} else { $tooDifficultFlag = true; }
	}
}
if ( isset($resultQuant) ) mysql_free_result($resultQuant);



echo('<p class="summaryListsHeader">Опиленные листы:</p>');

if ( count($quant) ) {
	echo ("<table width='400' border='0' cellspacing='0' cellpadding='3' class='summaryLists'>\n");
	foreach ( $quant as $value ) {
		echo "<tr><td>";
		
		if ( $value[0] == 2440 ) echo "1220 x 2440";
		else if ( $value[0] == 2500 ) echo "1250 x 2500";
		else echo $value[0] . "Ширина не определена";
		
		echo " x " . $value[1] . " мм &mdash;&nbsp;";
		echo "<strong>" . $value[2] . "&nbsp;шт</strong>\n";
		
		echo "</td></tr>\n";
	}
	if ( $tooDifficultFlag ) echo "<tr><td>
	...<br>
	За выбранный период опиливалось<br>
	слишком много разных форматов/толщин<br>
	фанеры, не все они могут быть отражены.</td></tr>";
	echo "</table>";
// если пустой результат
} else {
	echo('<span class="summaryLists" style="margin-top: 3px;">Опиленных листов за выбранный период нет.</span>');
}

?>