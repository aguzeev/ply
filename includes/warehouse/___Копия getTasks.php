<?php

require_once('../init.php');

$ACCESSED_MODULE = 10;
$_LOGGING = false;
include('../cerber.php');

$isFirst = true;

?>

<table class="whTasks whEditable">
	<thead>
      <tr>
        <td class="whDate">&nbsp;</td>
        <td class="whType">Марка</td>
        <td class="whLength">Длина</td>
        <td class="whWidth">Ширина</td>
        <td class="whThickness">Толщина</td>
        <td class="whSort1">Сорт 1</td>
        <td class="whSort2">Сорт 2</td>
        <td class="whSanding">Шлифов.</td>
        <td class="whQuantity">Листов</td>
        <td class="whComment">Комментарий к заданию</td>
        <td class="whReadiness">Статус выполнения</td>
        <td class="whEditColumn"></td>
      </tr>
    </thead>
    <tbody>
    
  <?php
  	$sqlSelect = "SELECT * FROM `wh_tasks` WHERE `readyDate` >= CURRENT_DATE ORDER BY `readyDate` ASC, `timestamp` ASC";
	$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
	$prevReadyDate = "";
	if ( mysql_num_rows($resultSelect) > 0 ) {
		while ( $row = mysql_fetch_array( $resultSelect, MYSQL_ASSOC ) ) {
			if ( $prevReadyDate != $row["readyDate"] ) {
				
				if ( !$isFirst ) echo "
	</tbody>
	<tbody>";
				
				$isFirst = false;
				
				$date = strtotime( $row["readyDate"] );
				echo '<tr><td colspan="12" class="readyDateHeader">' . date("j", $date) . "&nbsp;" . $_monthes_rp[date("m", $date) - 1] . '</td></tr>';
				$prevReadyDate = $row["readyDate"];
				$isEditable = strtotime($prevReadyDate) > date("U") ? true : false;
			}
			
			echo '
		<tr id="rowID_' . $row["id"] . '"' . (!$isEditable ? ' class="currentDayTask"' : '') . '>
			<td class="whReadyDate taskNumber" data-value="' . date("d", $date) . "." . date("m", $date) . "." . date("Y", $date) . '">#' . $row["id"] . '</td>
			<td class="whType" data-value="' . $row["type"] . '">' . $wh_ply_types[ $row["type"] ] . '</td>
			<td class="whWidth">' . $row["length"] . '</td>
			<td class="whLength">' . $row["width"] . '</td>
			<td class="whThickness">' . $row["thickness"] . '</td>
			<td class="whSort1">' . $row["sort_1"] . '</td>
			<td class="whSort2">' . $row["sort_1"] . '</td>
			<td class="whSanding" data-value="' . $row["sanding"] . '">' . $wh_ply_sanding[ $row["sanding"] ] . '</td>
			<td class="whQuantity">' . $row["quantity"] . '</td>
			<td class="whComment">' . $row["comment"] . '</td>
			<td class="whReadiness">—</td>
			<td class="whEditColumn">';
				// editBar
		if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 && $isEditable ) {
			echo '
			<div class="editBar" id="editBar_rowID_"' . $row['id'] . '">
				<i class="isIcon icon-edit isPointer" data-id="' . $row["id"] . '">&#xe801;</i>
				<i class="isIcon  icon-cancel-circled isPointer" data-id="' . $row["id"] . '">&#xe806;</i>
			</div>
			';
		} else {
			echo '<div align="right"><i class="isIcon icon-lock-filled">&#xe807;</i></div>';
		}
		echo '
			</td>
		</tr>
			';
		}
	} else {
		echo '<tr><td colspan="12" align="center">Нет заданий</td></tr>';
	}
  ?>
  
    </tbody>
</table>