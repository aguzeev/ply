<?php

require_once('init.php');

$ACCESSED_MODULE = 3;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['act']) ){ $act = mysql_real_escape_string($_GET['act']); }
if ( isset($_GET['appId']) ) { $appId = mysql_real_escape_string($_GET['appId']); } else { die('Не указана должность'); }

if ($act == 'remove') {
	$query = "DELETE FROM `appointments` WHERE `id` = " . $appId . " LIMIT 1"; 
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
} else {
	// Редактирование или добавление должности
	if ( isset($_GET['appointment']) ) { $appointment = mysql_real_escape_string($_GET['appointment']);
	} else { echo json_encode(array("result"=>"error","text"=>"Не указана должность")); die(); }
	if ( isset($_GET['department']) ) { $department = mysql_real_escape_string($_GET['department']);
	} else { echo json_encode(array("result"=>"error","text"=>"Не указано подразделение")); die(); }
	
	if ( isset($_GET['allowedOpDef']) ) {
		$allowedOpDef = serialize( $_GET['allowedOpDef'] );
	} else { $allowedOpDef = ''; }
	
	// a:3:{i:0;i:9;i:1;i:16;i:2;i:22;}
		
	// Проверяем, нет ли такой уже
	  $query = "SELECT COUNT(`id`) FROM `appointments` WHERE `appointment` = '". $appointment . "'";
	  $result = mysql_query($query, $connection_stat) or die(mysql_error());
	  $row = mysql_fetch_array($result, MYSQL_ASSOC);
	  
	  if ( $row['COUNT(id)'] && $row['id'] != $appId ) {
		  echo json_encode(array("result"=>"error","text"=>"Такая должность уже существует"));
		  die();
	  }
		
	if ($appId == 'new') {
		// Добавляем новую должность
		  $query = "INSERT INTO `appointments` 
			  (`appointment`, `allowedOperationsDefault`, `department`) VALUES 
			  ('" . $appointment ."', '" . $allowedOpDef . "', '" . $department . "')";
		  $result = mysql_query($query, $connection_stat) or die(mysql_error());
	} else {
		// Редактируем существующую должность
		$query = "UPDATE `appointments` SET 
			`appointment` = '" . $appointment ."',
			`allowedOperationsDefault` = '" . $allowedOpDef ."',
			`department` = '" . $department . "' 
			WHERE `id` = " . $appId . " LIMIT 1";
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
	}
}

?>