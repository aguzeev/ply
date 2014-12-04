<?php

require_once('init.php');

$ACCESSED_MODULE = 4;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['act']) ){ $act = mysql_real_escape_string($_GET['act']); }
if ( isset($_GET['operationId']) ) { $operationId = mysql_real_escape_string($_GET['operationId']); } else { die('Не указана операция'); }

if ($act == 'remove') {
	$query = "DELETE FROM `operations` WHERE `id` = " . $operationId . " LIMIT 1"; 
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
} else {
	// Редактирование или добавление операции
	if ( isset($_GET['operation']) ) { $operation = mysql_real_escape_string($_GET['operation']); } else { die('Не указана операция'); }
	
	// Проверяем, нет ли такой уже
	  $query = "SELECT COUNT(`id`) FROM `operations` WHERE `operation` = '". $operation . "'";
	  $result = mysql_query($query, $connection_stat) or die(mysql_error());
	  if ( mysql_result($result, 0) ) {
		  echo json_encode(array("result"=>"error","text"=>"Такая операция уже существует"));
		  die();
	  }
		
	if ($operationId == 'new') {
		// Добавляем новую операцию
		  $query = "INSERT INTO `operations` 
			  (`operation`) VALUES 
			  ('" . $operation ."')";
		  $result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующую операцию
		$query = "UPDATE `operations` SET 
			`operation` = '" . $operation ."' 
			WHERE `id` = " . $operationId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}

?>