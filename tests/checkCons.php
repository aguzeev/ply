<?php

$tableName = "lopping"; // имя таблицы
$fieldName = ""; // имя поля со счётчиком
//$id1 = ;
//$id2 = ;
//$date1 = "2012-11-27 22:30";
//$date2 = "2012-11-27 23:10";

$db = mysql_connect("127.0.0.1:3306", "root", "", true) or die (mysql_error());
mysql_select_db("machines_data", $db) or die (mysql_error());



if (isset($date1) and isset($date2)) { // извлечение по дате
	$sql = "SELECT `$fieldName` FROM `$tableName` WHERE `timestamp` BETWEEN '$date1' AND '$date2'";
} else if (isset($id1) and isset($id2)) { // извлечение по id
	$sql = "SELECT `$fieldName` FROM `$tableName` WHERE `id` BETWEEN '$id1' AND '$id2'";
} else { // извлечение всех полей
	$sql = "SELECT `$fieldName` FROM `$tableName`";
}
$result = mysql_query($sql);

$n = 0;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	if ($n > 0) {
		if ($row[0] - 1 != $oldValue) echo "Ошибка в индексе " . $row[$n] . "<br>";
	}
	$oldValue = $row[0];
	$n++;
}

?>