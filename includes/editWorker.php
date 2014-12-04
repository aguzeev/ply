<?php

require_once('init.php');

$ACCESSED_MODULE = 2;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['act']) ){ $act = mysql_real_escape_string($_GET['act']); }
if ( isset($_GET['workerId']) ) { $workerId = mysql_real_escape_string($_GET['workerId']); } else { die('Не указан сотрудник'); }

if ($act == 'remove') {
	// Для полного удаления:
	//$query = "DELETE FROM `workers` WHERE `id` = " . $workerId . " LIMIT 1"; 
	
	// Для перевода в неактивный режим:
	$query = "UPDATE `workers` SET `isActive` = '0' WHERE `id` = " . $workerId . " LIMIT 1";
	
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
} else {
	// Редактирование или добавление сотрудника
	if ( isset($_GET['nFamily']) ) { $nFamily = mysql_real_escape_string($_GET['nFamily']); } else { echo json_encode(array("result"=>"error","text"=>"Не указана фамилия")); die(); }
	if ( isset($_GET['nFirst']) ) { $nFirst = mysql_real_escape_string($_GET['nFirst']); } else { echo json_encode(array("result"=>"error","text"=>"Не указано имя")); die(); }
	if ( isset($_GET['nMiddle']) ) { $nMiddle = mysql_real_escape_string($_GET['nMiddle']); } else { echo json_encode(array("result"=>"error","text"=>"Не указано отчество")); die(); }
	if ( isset($_GET['sector']) ) { $sector = mysql_real_escape_string($_GET['sector']); } else { echo json_encode(array("result"=>"error","text"=>"Не указано подразделение")); die(); }
	if ( isset($_GET['app']) ) { $app = mysql_real_escape_string($_GET['app']); } else { echo json_encode(array("result"=>"error","text"=>"Не указана должность")); die(); }
	if ( isset($_GET['op']) ) {
		if ( $_GET['op'] != 'null') { $op = serialize( $_GET['op'] ); } else { $op = ''; };
	} else {
		$op = '';
	}
	if ( isset($_GET['isActive']) ) $isActive = intval(mysql_real_escape_string($_GET['isActive']));
		else $isActive = 1;
	if ( isset($_GET['dateStart']) ) {
		$_GET['dateStart'] != '0'	? $dateStart = ("'" .  mysql_real_escape_string($_GET['dateStart']) . "'") : $dateStart = 'NULL';
	} else $dateStart = 'NULL';
	if ( isset($_GET['dateFinish']) ) {
		$_GET['dateFinish'] != '0'	? $dateFinish = ("'" .  mysql_real_escape_string($_GET['dateFinish']) . "'") : $dateFinish = 'NULL';
	} else $dateFinish = 'NULL';
	
	//http://auth/includes/editWorker.php?workerId=new&nFamily=nFamily&nFirst=nFirst&nMiddle=nMiddle&sector=sector&app=app&op=op
	
	
	if ($workerId == 'new') {
		// Добавляем нового сотрудника
		
		// Проверяем, нет ли такого уже
		$query = "SELECT COUNT(`id`) FROM `workers` WHERE `name_family` = '". $nFamily . "' AND `name_first` = '". $nFirst . "' AND `name_middle` = '". $nMiddle . "' AND `sector` = '". $sector . "' AND `isActive` = 1";
		  
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
		if ( mysql_result($result, 0) ) {
			echo json_encode(array("result"=>"error","text"=>"Error 1: Сотрудник с таким ФИО уже существует в этом подразделении"));
			die();
		}
		  $query = "INSERT INTO `workers` 
			  (`name_family`, `name_first`, `name_middle`, `sector`, `appointment`, `additionalOperations`, `date_start_work`, `date_finish_work`) VALUES ('" . 
			  $nFamily . "', '" . 
			  $nFirst . "', '" .
			  $nMiddle . "', '" .
			  $sector . "', '" .
			  $app . "', '" .
			  $op . "', " .
			  $dateStart . ", " .
			  $dateFinish  . ")";
		  $result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующуего сотрудника
		$query = "UPDATE `workers` SET 
			`name_family` = '" . $nFamily ."', 
			`name_first` = '" . $nFirst ."', 
			`name_middle` = '" . $nMiddle ."', 
			`sector` = '" . $sector ."', 
			`appointment` = '" . $app ."', 
			`additionalOperations` = '" . $op ."',
			`isActive` = '" . $isActive ."',
			`date_start_work` = " . $dateStart .", 
			`date_finish_work` = " . $dateFinish ." 
			WHERE `id` = " . $workerId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}

?>