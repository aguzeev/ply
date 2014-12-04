<?php

require_once('init.php');

$ACCESSED_MODULE = 6;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['act']) ){ $act = mysql_real_escape_string($_GET['act']); }
if ( isset($_GET['fixedpriceId']) ) { $fixedpriceId = mysql_real_escape_string($_GET['fixedpriceId']); } else { 
	echo json_encode(array("result"=>"error","text"=>"Не выбран тариф для редактирования.","source"=>"editFixedprice"));
	die();
}

if ($act == 'remove') { // удаление тарифа
	$queryDelete = "DELETE FROM `fixedprice` WHERE `id` = " . $fixedpriceId . " LIMIT 1"; 
	$result = mysql_query($queryDelete, $connection_stat) or die(mysql_error());
} else {
	// Редактирование или добавление тарифа
	if ( isset($_GET['app']) ) { $app = mysql_real_escape_string($_GET['app']); } else { die('Не указана должность.'); }
	if ( isset($_GET['dateStart']) ) { $dateStart = mysql_real_escape_string($_GET['dateStart']) . ' 08:00'; } else echo json_encode(array("result"=>"error","text"=>"Ошибка в дате.","source"=>"editFixedprice"));
	if ( isset($_GET['price']) ) { $price = mysql_real_escape_string($_GET['price']); }
		else echo json_encode(array("result"=>"error","text"=>"Ошибка в тарифе.","source"=>"editFixedprice"));

	
	if ($fixedpriceId == 'new') {
		// Добавляем новый тариф
		$query = "INSERT INTO `fixedprice` 
			(`app_id`, `price`, `date_start`) VALUES ('" . 
			$app . "', '" . 
			$price . "', '" .
			$dateStart . "')";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующий тариф
		echo $dateStart . "\n";
		$query = "UPDATE `fixedprice` SET 
			`app_id` = '" . $app ."', 
			`price` = '" . $price ."', 
			`date_start` = '" . $dateStart ."'
			WHERE `id` = " . $fixedpriceId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}

?>