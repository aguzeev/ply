<?php

require_once('init.php');

$ACCESSED_MODULE = 2;
$_LOGGING = false;
include('cerber.php');

if ( isset($_GET['dept']) ) {
	$dept = mysql_real_escape_string($_GET['dept']);
} else {
	echo json_encode(array("result"=>"error","text"=>"Íå óêàçàíî ïîäðàçäåëåíèå"));
	die();
};

if ($dept == 0) $query = "SELECT `workers`.`id`, `name_family`, `name_first`, `name_middle`, `appointments`.`appointment`
FROM `workers` LEFT JOIN `appointments` ON `workers`.`appointment` = `appointments`.`id` WHERE `workers`.`isActive` = '1' ORDER BY `name_family`"; // выбираем всех сотрудников завода
	else $query = "SELECT `workers`.`id`, `name_family`, `name_first`, `name_middle`, `appointments`.`appointment`
FROM `workers` LEFT JOIN `appointments` ON `workers`.`appointment` = `appointments`.`id`
WHERE `workers`.`sector` IN (" . $dept . ") AND `workers`.`isActive` = '1' ORDER BY `name_family`"; // выбираем сотрудников по подразделению

$result = mysql_query($query, $connection_stat) or die(mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$workersApps[] = array("id" => $row['id'],
	"name_family" => $row['name_family'],
	"name_first" => mb_substr($row['name_first'], 0, 1, 'utf8'),
	"name_middle" => mb_substr($row['name_middle'], 0, 1, 'utf8'),
	"appointment" => $row['appointment']
);
}


echo( json_encode($workersApps) );

?>