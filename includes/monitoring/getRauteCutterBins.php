<?php

require_once('../init.php');

if(isset($_GET['start'])) $startDate = $_GET['start'];
if(isset($_GET['end'])) $endDate = $_GET['end'];

//$startDate = '2012-01-16 22:50:55';
//$endDate = '2012-01-16 22:50:55';

$beg = strtotime($startDate);
$end = strtotime($endDate);
$beg = date("Y-m-d G:i:s", $beg); //echo ("Begin: ".$beg."<br />");
$end = date("Y-m-d G:i:s", $end); //echo ("End: ".$end."<br /><br />");

$diff = strtotime($endDate) - strtotime($startDate);
if ($diff >= 60 * 60 * 24 * 60) { // берём общие дневные значения
	echo('<span class="summaryLists" style="margin-top: 3px;">Слишком большой период для подсчёта точного количества листов. Данные о количестве листов доступны для периодов не длиннее 60 дней.</span>');
	die();
}

	  
$resultThickness = mysql_query("SELECT thickness, length, bin1_width, bin2_width, bin3_width, bin1_quant, bin2_quant, bin3_quant FROM raute_cutter 
		  WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'");

$thicknessArr = array();
$lengthArr = array(array());
$widthArr = array();
while ($row = mysql_fetch_array($resultThickness, MYSQL_ASSOC)) {
	$curThickness = $row['thickness'];
	if (!in_array($curThickness, $thicknessArr)) {
		$thicknessArr[] = $curThickness;
	}
}
//print_r($thicknessArr);
//$quant_t = count($thicknessArr);
//echo('<br><br>');
mysql_free_result($resultThickness);

foreach ($thicknessArr as $index => $value) {
	$resultLength = mysql_query("SELECT length FROM raute_cutter 
	WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
	AND thickness = " . $value);
	
	$lengthArr[$index] = array();
	while ($row = mysql_fetch_array($resultLength, MYSQL_ASSOC)) {
		$curLength = $row['length'];
		if (!in_array($curLength, $lengthArr[$index])) {
			$lengthArr[$index][] = $curLength;
		}
	}
	//echo($index.': ');
	//print_r($lengthArr[$index]);
	//echo('<br>');
}
if (isset($resultLength)) mysql_free_result($resultLength);


for ($lenIndex = 0; $lenIndex < count($lengthArr); $lenIndex++) {
	foreach ($lengthArr[$lenIndex] as $index => $value) {
		$resultWidth = mysql_query("SELECT bin1_width, bin2_width, bin3_width FROM raute_cutter 
		WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
		AND thickness = " . $thicknessArr[$lenIndex] . " AND length = " . $value);
		
		$widthArr[$lenIndex][$index] = array();
		while ($row = mysql_fetch_array($resultWidth, MYSQL_ASSOC)) {
			$curWidth1 = $row['bin1_width'];
			$curWidth2 = $row['bin2_width'];
			$curWidth3 = $row['bin3_width'];
			if (!in_array($curWidth1, $widthArr[$lenIndex][$index])) {
				$widthArr[$lenIndex][$index][] = $curWidth1;
			}
			if (!in_array($curWidth2, $widthArr[$lenIndex][$index])) {
				$widthArr[$lenIndex][$index][] = $curWidth2;
			}
			if (!in_array($curWidth3, $widthArr[$lenIndex][$index])) {
				$widthArr[$lenIndex][$index][] = $curWidth3;
			}
		}
		//echo($lenIndex . '.' . $index.': ');
		//print_r($widthArr[$lenIndex][$index]);
		//echo('<br>');
	}
}

//echo('<br>');
//print_r($widthArr);
//echo('<br><br>');
if (isset($resultWidth)) mysql_free_result($resultWidth);


echo('<p class="summaryListsHeader">Нарезанные листы:</p>');

if (count($widthArr)) {
echo('<table width="400" border="0" cellspacing="0" cellpadding="3" class="summaryLists">
');
  
$lists = array(array(array()));
for ($i = 0; $i < count($widthArr); $i++) {
	for ($j = 0; $j < count($widthArr[$i]); $j++) {
		for ($k = 0; $k < count($widthArr[$i][$j]); $k++) {
			$lists[$i][$j][$k] = 0;
			
			$resultWidthBin1 = mysql_query("SELECT bin1_quant FROM raute_cutter 
			WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
			AND thickness = " . $thicknessArr[$i] . " AND length = " . $lengthArr[$i][$j] . " AND bin1_width = " . $widthArr[$i][$j][$k]);
			while ($row = mysql_fetch_array($resultWidthBin1, MYSQL_ASSOC)) { $lists[$i][$j][$k] += $row['bin1_quant']; }
			
			$resultWidthBin2 = mysql_query("SELECT bin2_quant FROM raute_cutter 
			WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
			AND thickness = " . $thicknessArr[$i] . " AND length = " . $lengthArr[$i][$j] . " AND bin2_width = " . $widthArr[$i][$j][$k]);
			while ($row = mysql_fetch_array($resultWidthBin2, MYSQL_ASSOC)) { $lists[$i][$j][$k] += $row['bin2_quant']; }
			
			$resultWidthBin3 = mysql_query("SELECT bin3_quant FROM raute_cutter 
			WHERE `timestamp` BETWEEN '" . $beg . "' AND '" . $end . "'
			AND thickness = " . $thicknessArr[$i] . " AND length = " . $lengthArr[$i][$j] . " AND bin3_width = " . $widthArr[$i][$j][$k]);
			while ($row = mysql_fetch_array($resultWidthBin3, MYSQL_ASSOC)) { $lists[$i][$j][$k] += $row['bin3_quant']; }
			
			//echo('Листов с толщиной ' . $thicknessArr[$i] . ', длиной ' . $lengthArr[$i][$j] . ' и шириной ' . $widthArr[$i][$j][$k] . ' — ' . $lists[$i][$j][$k]);
			//echo('<br>');
			
			
  			echo('<tr>
    <td>' . $thicknessArr[$i] / 100 . ' x ' . $lengthArr[$i][$j] . ' x ' . $widthArr[$i][$j][$k] . ' мм — <strong>' . $lists[$i][$j][$k] . ' шт</strong></td>
  </tr>');
			mysql_free_result ($resultWidthBin1); mysql_free_result ($resultWidthBin2); mysql_free_result ($resultWidthBin3);
		}
	}
}
echo('
</table>');

// если пустой результат
} else {
	echo('<span class="summaryLists" style="margin-top: 3px;">Нарезанных листов за выбранный период нет.</span>');
}

?>