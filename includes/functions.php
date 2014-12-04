<?php

if (ini_get('magic_quotes_gpc')) {
	slashes($_GET);
	slashes($_POST);    
	slashes($_COOKIE);
}
	
function slashes(&$el) {
	if (is_array($el))
		foreach($el as $k=>$v)
			slashes($el[$k]);
	else $el = stripslashes($el); 
}

function Generatepepper($n=3) {
	$key = '';
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
	$counter = strlen($pattern)-1;
	for($i=0; $i<$n; $i++) {
		$key .= $pattern{rand(0,$counter)};
	}
	return $key;
}
function array_recirsive_sum($array) {
  $sum = 0;
  foreach($array as $key => $a){
    if (is_array($a)) {
       $sum += array_recirsive_sum($a);
    } else {
       $sum += $a;
    }
  }
  return $sum;
}
function array_recirsive_count_notzero($array) { // recirsively counts all the elements wich is not 0
  $count = 0;
  foreach($array as $key => $a){
    if (is_array($a)) {
       $count += array_recirsive_count_notzero($a);
    } else if ($a != 0) {
       $count++;
    }
  }
  return $count;
}

function permited($usergroup, $area) {
	if ($area == 0) { // Общедоступная категория
		$perm = 1;
	} else if ($usergroup == 20) {
		$perm = 2; // Админу можно всё
	} else {
		// Проверяем, если ли такая категория
		global $connection_stat;
		$query = 'SELECT `area` FROM `groups_permitions` GROUP BY `area`';
		$result = mysql_query($query, $connection_stat) or die(mysql_error());
		$areas = mysql_fetch_array($result, MYSQL_NUM);
		mysql_free_result($result);
		
		if ( in_array($area, $areas) ) {// И, если есть, делаем запрос на проверку прав доступа
			$query = 'SELECT `permition` FROM `groups_permitions` WHERE `group` = ' . $_SESSION['v2_user_group'] . ' AND `area` = ' . $area . ' LIMIT 1';
			$result = mysql_query($query, $connection_stat) or die(mysql_error());
			$perm = intval(mysql_result($result, 0));
			mysql_free_result($result);
		} else {
			// такой категории нет, поэтомуи доступ не открываем
			$perm = 0;
			echo('Неверно задана категория<br />');
		}
	}
	return $perm;
}
function checkPermition($user, $module) {
	
	global $connection_stat;
	$_DEBUG = false;
	
	$query = "SELECT `hasAccess` FROM `users` WHERE `id` = '" . $user . "'";
	if ($_DEBUG) { echo "SQL: " . $query . "<br>"; }
	$resultPerm = mysql_query($query, $connection_stat) or die(mysql_error());
	$allowedAccess = get_object_vars(json_decode(mysql_result($resultPerm, 0)));
	mysql_free_result($resultPerm);
	
	if ( isset($allowedAccess[$module]) ) $perm = $allowedAccess[$module];
			else $perm = 0;
	if ($module == 0) $perm = 2;
			
	if ($_DEBUG) {
		// вывод служебной информации
		echo "user ID = " . $user . "<br />";
		echo "module ID = " . $module . "<br />";
		echo "Права доступа: ";
		print_r($allowedAccess);
		echo "<br />";
		if ($perm == 0) echo 'Не хватает прав для доступа к запрашиваемой странице.<br />';
			else if ($perm == 1) echo 'Доступ на просмотр.<br />';
				else if ($perm == 2) echo 'Доступ на редактирование.<br />';
	}
	return $perm;
}
function getGroupName($groupId) {
	global $connection_stat;
	// Получаем название группы пользователей
	$query = "SELECT `group_name` FROM `groups` WHERE `id` = " . $groupId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$groupName = mysql_result($result, 0);
	mysql_free_result($result);
	return $groupName;
}
function getDeptName($deptId) {
	global $connection_stat;
	// Получаем название подразделения
	$query = "SELECT `departmentName` FROM `departments` WHERE `id` = " . $deptId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$deptName = mysql_result($result, 0);
	mysql_free_result($result);
	return $deptName;
}
function getDeptByApp($appId) {
	global $connection_stat;
	// Получаем подразделение по id должности
	$query = "SELECT `id`, `departmentName` FROM `departments` WHERE `id` = (
	SELECT `department` FROM `appointments` WHERE `id` = " . $appId . "
	) LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$deptByApp = array('id' => $row['id'], 'title' => $row['departmentName']);
	mysql_free_result($result);
	return $deptByApp;
}
function getAppName($appId) {
	global $connection_stat;
	// Получаем название должности
	$query = "SELECT `appointment` FROM `appointments` WHERE `id` = " . $appId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$appName = mysql_result($result, 0);
	mysql_free_result($result);
	return $appName;
}
function getOperationName($opId) {
	global $connection_stat;
	// Получаем название операции
	$query = "SELECT `operation` FROM `operations` WHERE `id` = " . $opId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	$opName = mysql_result($result, 0);
	mysql_free_result($result);
	return $opName;
}

function getWorkerName($workerId, $abbr = false) { // $abbr показывает, выдавать ли ФИО в виде Фамилия И. О.
	global $connection_stat;
	 
	// Получаем ФИО сотрудника
	$query = "SELECT `name_family`, `name_first`, `name_middle` FROM `workers` WHERE `id` = " . $workerId . " LIMIT 1";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if ($abbr) $workerName = $row['name_family'] . ' ' . mb_substr($row['name_first'], 0, 1, 'utf8') . '. ' .  mb_substr($row['name_middle'], 0, 1, 'utf8') . '.';
			else $workerName = $row['name_family'] . ' ' . $row['name_first'] . ' ' . $row['name_middle'];
	}
	mysql_free_result($result);
	return $workerName;
}

function getWorkersByOperation($operationId, $workday) {
	global $connection_stat;
	$workday = $workday . " 08:00";
	// получаем список всех сотрудников, допущенных к этой операции, и id тех, кто уже выбран
	// 1. получаем все должности, допущенные к этой операции
	// 2. выбираем всех сотрудников на этой должности
	// 3. выбираем всех сотрудников, у готорых дополнительно разрешена эта операция
	$sqlAllWorkers = "SELECT * FROM `workers` WHERE (`sector` IN
	(
	SELECT `department` 
	FROM `appointments` 
	WHERE `allowedOperationsDefault` LIKE '%\"" . $operationId . "\"%'
	) 
	OR `additionalOperations` LIKE '%\"" . $operationId . "\"%')
	AND (`date_start_work` <= '" . $workday . "' OR `date_start_work` IS NULL)
	AND (`date_finish_work` >= '" . $workday . "' OR `date_finish_work` IS NULL)
	AND `isActive` = 1";
	
	$resultAllWorkers = mysql_query($sqlAllWorkers, $connection_stat) or die(mysql_error());
	$workersIds = array();
	$workersNames = array();
	while ($rowAllWorkers = mysql_fetch_array($resultAllWorkers, MYSQL_ASSOC)) {
		$workersIds[] = $rowAllWorkers['id'];
		$workersNames[] = $rowAllWorkers['name_family'] . ' ' . $rowAllWorkers['name_first'] . ' ' . $rowAllWorkers['name_middle'];
	}
	mysql_free_result($resultAllWorkers);
	return array($workersIds, $workersNames); // массив из id и имён сотрудников
}

?>