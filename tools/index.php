<?php

include "../includes/init.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Инструменты проверки и диагностики</title>

<link rel="stylesheet" href="css/jquery-ui-1.8.23.custom.css" />
<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="jquery.ui.datepicker-ru.js"></script>
<script type="text/javascript" src="jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="scripts.js"></script>


</head>

<body>
<table width="100%" border="0" cellspacing="5" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td width="150" valign="top">
    <p>Операция:</p>

      <p>
        <label>
          <input type="radio" name="machine" value="lopping" id="machine_0" checked="checked" />
          Опиловка</label>
        <br />
        <label>
          <input type="radio" name="machine" value="grinding" id="machine_1" />
          Шлифовка</label>
        <br />
      </p>
	</td>
    <td width="350" valign="top">
      <p>Вид контроля:</p>
        <p>
          <label>
            <input type="radio" name="type" value="consist" id="type_0" checked="checked" />
            Контроль последовательности</label>
          <br />
          <label>
            <input type="radio" name="type" value="thicknessQuant" id="type_1" />
            Последовательность укладки по толщинам</label>
          <br />
        </p>
	</td>
    <td width="190" valign="top"><p>Период:</p>
    <p>
     <label for="dateBegin" style="width: 18px; display: inline-block;">с</label>
     <input type="text" name="dateBegin" id="dateBegin" /><br />
     
     <label for="dateEnd" style="width: 18px; display: inline-block;">по</label>
     <input type="text" name="dateEnd" id="dateEnd" />
    </p></td>
    <td width="100" align="center" valign="middle">
    <p>
      <input type="submit" name="showButton" id="showButton" value="Показать" onclick="javascript:submitControl();" /><br />
      <input type="submit" name="showButton2" id="showButton2" value="Сбросить" onclick="javascript:resetFilter();" />
    </p>
    </td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php

if ( isset($_GET['machine']) ) { $machine = mysql_real_escape_string($_GET['machine']); } else { $machine = 'lopping'; }
if ( isset($_GET['type']) ) { $type = mysql_real_escape_string($_GET['type']); } else { $type = 'consist'; }
if ( isset($_GET['d1']) ) { $date1 = mysql_real_escape_string($_GET['d1']); } else { die('Укажите дату'); }
if ( isset($_GET['d2']) ) { $date2 = mysql_real_escape_string($_GET['d2']); } else { die('Укажите дату'); }
echo "date1: $date1";

$fieldName = "part_id"; // имя поля со счётчиком

$errCount = 0;

if ($type == 'consist') { // проверка последовательности
	if (isset($date1) and isset($date2)) { 
		$sql = "SELECT `$fieldName`, `timestamp` FROM `$machine` WHERE `timestamp` BETWEEN '$date1' AND '$date2'";
	}
	echo "<p>Выполнен запрос: $sql</p>";
	$result = mysql_query($sql, $connection_hardware);
	
	$n = 0;
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
		if ($n > 0) {
			if ($row[0] - 1 != $oldValue) {
				echo "Ошибка в индексе " . $row[0] . "<br>
				Предыдущее значение: $oldRow[0] $oldRow[1]<br>
				Текущее значение: $row[0] $row[1]<br>&nbsp;<br>";
				$errCount++;
			}
		}
		$oldValue = $row[0];
		$oldRow = $row;
		$n++;
	}
	if ($errCount == 0) echo '<p align="center">Нарушений последовательности индекса нет.</p>';
}

?>
</body>
</html>