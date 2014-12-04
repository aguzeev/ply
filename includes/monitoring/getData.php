<?php
  // © Александр Гузеев (Alexander Guzeev), 2012
  
  require_once('../init.php');
  
  $start_time = microtime(true);
  
  if(isset($_GET['machine'])) $machine = $_GET['machine'];
  if(isset($_GET['field'])) $field = $_GET['field'];
  if(isset($_GET['start'])) $startDate = $_GET['start'];
  if(isset($_GET['end'])) $endDate = $_GET['end'];
  if(isset($_GET['multiplexer'])) $mx = $_GET['multiplexer'];
  if(isset($_GET['maxDetail'])) $maxDetail = $_GET['maxDetail'];
  if(isset($_GET['operation'])) $operation = $_GET['operation'];
  if(isset($_GET['accuracy'])) $accuracy = $_GET['accuracy'];
  	else $accuracy = 0;
  if(isset($_GET['valueScale'])) $valueScale = $_GET['valueScale'];
  	else $valueScale = 1;
  
  if ( isset($_GET['callback_position']) ) $callback_position = mysql_real_escape_string($_GET['callback_position']);

  $minInterval = 60; // минимальный интервал отображения — 1 минута
  
  if(!isset($_GET['machine'])) $debugMode = true;
  /* DEBUG */ if ($debugMode) {
	  $machine = 'warmer';
	  $field = 'sqLd';
	  $startDate = '02.12.2012 08:00';
	  $endDate = '02.12.2012 14:18';
	  $mx = 1;
	  $operation = 'avg';
	  $valueScale = 1;
	  $addState = 0;
  }
  
  /*$timezoneOffset = date("O", $startDate) / 100 * 60 * 600;
  //$timezoneOffset = 60 * 60 * 3;
  echo "timezoneOffset: " . $timezoneOffset / 60 / 60 . "<br><br>";
  
  $startDateInit = $startDate;
  echo "original time: " . $startDate . "<br>";
  
  $startDate = date("U", strtotime($startDateInit));
  echo "unixtime: " . $startDate . "<br>";
  
  $startDate = date("Y-m-d G:i:s O", $startDate);
  echo "back to normal time: " . $startDate . "<br><br>";
  
  $interval = 60*60*4;
  $startDate = floor( date("U", strtotime($startDateInit) + $timezoneOffset) / $interval) * $interval - $timezoneOffset;
  echo "after division: " . $startDate . "<br>";
  $startDate = date("Y-m-d G:i:s O", $startDate);
  echo "after division in normal time: " . $startDate . "<br><br>";
  

  
  $startDate = floor( date("U", (strtotime($startDateInit))) / $interval) * $interval;
  echo $startDate . "<br>";
  $startDate = date("Y-m-d G:i:s O", $startDate);
  echo $startDate . "<br><br>";*/
  
  
  /* ================= fast backup ==================
  $diff = strtotime($endDate) - strtotime($startDate);
  if ($diff >= $maxDiffBeforeDaily) { // берём общие дневные значения
	  $machine = $machine . "_daily";
	  $startDate = substr($startDate, 0, 10); // обрезаем часы и минуты
	  $endDate = substr($endDate, 0, 10);
	  $interval = 60 * 60 * 24;
	  $valueScale = 1;
  } else { // берём точные значения по дням
	  if ($accuracy == 0) $interval = max(floor($diff / (120*$mx)), $maxDetail); // автоматическое определение детализации
	  else $interval = max($accuracy, $maxDetail); // детализация задана вручную
  }
  
  $nTimes = floor($diff / $interval);
  */
  
  
  // определение детализации
  $diff = strtotime($endDate) - strtotime($startDate);
  
  // >= 60 дней
  if ($diff >= 60 * 60 * 24 * 60) { // берём общие дневные значения
	  $machine = $machine . "_daily";
	  $interval = 60 * 60 * 24;
	  $valueScale = 1;

  // >= 15 дней
  } else if ($diff >= 60 * 60 * 24 * 15) {
	  //$interval = 60 * 60 * 6;
	  $interval = $diff / 15 / 6;
  // >= 7 дней
  } else if ($diff >= 60 * 60 * 24 * 7) {
	  //$interval = 60 * 60 * 2;
	  $interval = $diff / 12 / 7;
  // >= 1 дня
  } else if ($diff >= 60 * 60 * 24) {
	  // $interval = 60 * 30;
	  $interval = $diff / 4 / 24;
  // >=12 часов
  } else if ($diff >= 60 * 60 * 12) {
	  $interval = 60 * 10;
  // >=6 часов
  } else if ($diff >= 60 * 60 * 6) {
	  $interval = 60 * 5;
  // >=3 часов
  } else if ($diff >= 60 * 60 * 3) {
	  $interval = 60 * 3;
  // < 3 часов
  } else {
	  $interval = 60;
  }
  $interval = max( ($interval / $mx) , $minInterval );
  
  $timezoneOffset = date("O", $startDate) / 100 * 60 * 60;
  $timezoneOffset = $timezoneOffset + 60 * 60;  //timezone bugfix
  $startDate = floor( date("U", strtotime($startDate) + $timezoneOffset) / $interval) * $interval - $timezoneOffset; // округляем до начала периода
  $startDate = date("Y-m-d G:i:s", $startDate);
  $endDate = floor( date("U", strtotime($endDate) + $timezoneOffset) / $interval + 1) * $interval - $timezoneOffset; // округляем до конца периода
  $endDate = date("Y-m-d G:i:s", $endDate);
  
  $nTimes = floor($diff / $interval);
  
  //echo('diff: ' . $diff . '<br>');
  //echo('interval: ' . $interval . '<br>');
  //echo('nTimes' . $nTimes . '<br>');
  
  // проверяем, сколько полей нужно извлечь
  $field = str_replace(' ', '', $field);
  $fieldsArr = explode(',', $field);
  
  $sql_begin = "SELECT `timestamp`";
  for ($fieldN = 0; $fieldN < count($fieldsArr); $fieldN++) {
	  $sql_begin .= ", `" . $fieldsArr[$fieldN] . "`";
  }
  $sql = $sql_begin . " FROM `" . $machine . "` 
	WHERE `timestamp` BETWEEN '" . date("Y-m-d G:i:s", strtotime($startDate)) . "' AND '" . date("Y-m-d G:i:s", strtotime($endDate)) . "' ORDER BY `timestamp`";
  $result = mysql_query($sql);
  //echo($sql . "<br>");

	$arrIndex = 0;
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$MYSQLresult_arr[$arrIndex][0] = $row['timestamp'];
		for ($f = 0; $f < count($fieldsArr); $f++) {
			$MYSQLresult_arr[$arrIndex][] = $row[$fieldsArr[$f]];
		}
		$arrIndex++;
	}
	mysql_free_result($result);
   if (!isset($MYSQLresult_arr[0][0])) $MYSQLresult_arr[0][0] = 0; // Если MYSQL вернула пустой результат
   
   /* DEBUG */ if ($debugMode) print_r($MYSQLresult_arr);
   /* DEBUG */ if ($debugMode) echo('<br><br>DEBUG: count: ' . count($MYSQLresult_arr ) . "<br><br>");  
	  
	  

  $beginIndex = 0; $endIndex = 0;
  for ($epoch = 0; $epoch <= $nTimes; $epoch++) {
	  //echo('<br>epoch: '. $epoch . '<br>');
	  
	  $beg = date('U', strtotime($startDate)) + ($epoch * $interval);
	  $end = $beg + $interval;
	  /* DEBUG */ if ($debugMode) { echo('DEBUG: beg: ' . date('Y-m-d G:i:s', $beg) . '<br>'); echo('DEBUG: end: ' . date('Y-m-d G:i:s', $end) . '<br><br>'); }
	  
	  $valuesArr[$epoch][0] = date('Y-m-d G:i:s', $beg);
	  $valuesArr[$epoch][1] = 0;
	  
	  //$endTimestamp = date("Y-m-d G:i:s", $end);
	  
if (date('U', strtotime($MYSQLresult_arr[$beginIndex][0])) <= ($end) ) {
		$incr = 0;
		while (
			( $beginIndex + $incr < count($MYSQLresult_arr )  ) &&
			( date('U', strtotime( $MYSQLresult_arr[$beginIndex + $incr][0] ) ) <= ($end) ) ) {
				$incr++;
				/* DEBUG */ if ($debugMode) echo "DEBUG: incremented<br>";
		}
		
		$endIndex = $beginIndex + $incr;
		/* DEBUG */ if ($debugMode) echo('DEBUG: $beginIndex: ' . $beginIndex . '<br>');
		/* DEBUG */ if ($debugMode) echo('DEBUG: $endIndex: ' . $endIndex . '<br>');
		
		$n = $endIndex - $beginIndex + 1; // количество элементов
		//echo "количество элементов = " . $n . "<br>";
		// обрабатываем элементы из запроса
		if (count($fieldsArr) == 1) {
			// если нужно извлечь одно поле
			$val_temp = 0;
			
			for ($j = $beginIndex; $j < $endIndex; $j++) {
				// по простой схеме
				$val_temp += round($MYSQLresult_arr[$j][1]/$valueScale, 3);
			}
		} else {
			if ($operation == 'avg_percent') {
				$val_temp[0] = 0; $val_temp[1] = 0;
				for ($j = $beginIndex; $j < $endIndex; $j++) {
						// подсчёт % на основании входящего и выходящего объёмов
						//$val_temp[0] += round($MYSQLresult_arr[$j][1] / 1000000000, 5); // суммарное значение поступившего объёма
						//$val_temp[1] += $val_temp[0] * $row[$fieldsArr[1]] / 100; // и выходного объёма, получаемое из % выхода
						
					// подсчёт % на основании входящего и выходящего объёмов
					$val_temp[0] += round($MYSQLresult_arr[$j][1] / 1000000000, 5); // суммарное значение поступившего объёма
					$val_temp[1] += round($MYSQLresult_arr[$j][1] / 1000000000, 5) * $MYSQLresult_arr[$j][2] / 10; // и выходного объёма, получаемое из % выхода
				}
			}
		}
		//print_r($val_temp);
	
	  switch($operation) {
	   		case 'sum':
	   			$valTotal = $val_temp;
	   		break;
		   	case 'avg':
				if ($n) $valTotal = $val_temp / $n;
				else $valTotal = 0;
	   		break;
			case 'avg_diameter':
				if ($n) $valTotal = $val_temp / $n * 2;
				else $valTotal = 0;
	   		break;
	   		case 'count':
	   			$valTotal = $n;
	   		break;
			case 'avg_percent':
				if ($val_temp[1] == 0) {
					$valTotal = 0;
				} else {
	   				$valTotal = round($val_temp[1] / $val_temp[0], 3);
				}
	   		break;
	   		default:
	   			$valTotal = $val_temp;
		}
		$valuesArr[$epoch][1] = round($valTotal, 2);
		$beginIndex = $endIndex;
	  } else {
	  	// раньше было $beginIndex++ , но это давало ошибочное отображение
	  }
	 // echo('count($MYSQLresult_arr[1]): ' . count($MYSQLresult_arr[1]) . '<br>');
	  
  } 
  
  //print_r($valuesArr);
  

  if (is_null($valuesArr)) {
	  die ('Пустая строка');
  } else {
	  if (!isset($callback_position)) echo ($str = json_encode($valuesArr));
		else { $str = json_encode(array("payload" => json_encode($valuesArr), "callback_position" => $callback_position)); echo $str; }
  }
  $exec_time = microtime(true) - $start_time;
  /* DEBUG */ if ($debugMode) echo('<br><br>DEBUG: total time: ' . $exec_time . '<br>');

?>