<?php

require_once('init.php');

$ACCESSED_MODULE = 5;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['act']) ){ $act = mysql_real_escape_string($_GET['act']); }
if ( isset($_GET['rateId']) ) { $rateId = mysql_real_escape_string($_GET['rateId']); } else { die('Не выбран такриф для редактирования.'); }

if ($act == 'remove') {
	$queryDelete = "DELETE FROM `rates` WHERE `id` = " . $rateId . " LIMIT 1"; 
	$result = mysql_query($queryDelete, $connection_stat) or die(mysql_error());
} else {
	// Редактирование или добавление тарифа
	if ( isset($_GET['op']) ) { $op = mysql_real_escape_string($_GET['op']); } else { die('Не указана операция.'); }
	if ( isset($_GET['app']) ) { $app = mysql_real_escape_string($_GET['app']); } else { die('Не указана должность.'); }
	if ( isset($_GET['dateStart']) ) { $dateStart = mysql_real_escape_string($_GET['dateStart']); } else { die('Не указана дата установления такрифа.'); }
	if ( isset($_GET['rate1']) ) { $rate1 = mysql_real_escape_string($_GET['rate1']); }
		else echo json_encode(array("result"=>"error","text"=>"Ошибка в тарифе 1.","source"=>"editRate"));
	if ( isset($_GET['rate2']) ) {
		$rate2 = mysql_real_escape_string($_GET['rate2']);
		if ( isset($_GET['cond2']) ) $cond2 = mysql_real_escape_string($_GET['cond2']);
			else echo json_encode(array("result"=>"error","text"=>"Не указано условие для тарифа 2.","source"=>"editRate"));
	} else { $rate2 = 0; $cond2 = 0; }
	if ( isset($_GET['rate3']) ) {
		$rate3 = mysql_real_escape_string($_GET['rate3']);
		if ( isset($_GET['cond3']) ) $cond3 = mysql_real_escape_string($_GET['cond3']);
			else echo json_encode(array("result"=>"error","text"=>"Не указано условие для тарифа 3.","source"=>"editRate"));
	} else { $rate3 = 0; $cond3 = 0; }

	
	if ($rateId == 'new') {
		// Добавляем новый тариф
		
		$query = "INSERT INTO `rates` 
			(`start_date`, `operation_id`, `app_id`, `rate_1`, `rate_2`, `cond_2`, `rate_3`, `cond_3`) VALUES ('" . 
			$dateStart . "', '" . 
			$op . "', '" .
			$app . "', '" .
			$rate1 . "', '" .
			$rate2 . "', '" .
			$cond2 . "', '" .
			$rate3 . "', '" .
			$cond3  . "')";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующий тариф
		$query = "UPDATE `rates` SET 
			`start_date` = '" . $dateStart ."', 
			`operation_id` = '" . $op ."', 
			`app_id` = '" . $app ."', 
			`rate_1` = '" . $rate1 ."', 
			`rate_2` = '" . $rate2 ."', 
			`cond_2` = '" . $cond2 ."', 
			`rate_3` = '" . $rate3 ."', 
			`cond_3` = '" . $cond3 ."' 
			WHERE `id` = " . $rateId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}

?>