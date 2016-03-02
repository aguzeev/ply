<?php

require_once('../init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
include('../cerber.php');
if ( $permition != 2 ) {
	echo json_encode( array("result" => "restricted", "text" => "У вас не хватает прав для редактирования") );
	die();
}

$successText = array();
$totlalResult = true;

if ( isset($_POST["params"]) ) $params = json_decode( $_POST["params"], true );

// delete
if ( isset($params["delete"]) ) {
	if ( sizeof($params["delete"]) > 0 ) {
		$ids_to_delete = array();
		
		if ( is_array($params["delete"]) ) {
			foreach ( $params["delete"] as $value ) $ids_to_delete[] = intval($value);
			$ids_to_delete = implode(", ", $ids_to_delete);
		}
		
		$sql = "DELETE FROM `wh_tasks` WHERE `id` IN (" . $ids_to_delete . ")";
		//echo $sql;
		
		$result = mysql_query($sql, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
		if ( $result ) $successText[] = "Успешно удалено<br>";
		$totlalResult = $totlalResult & $result;
	} else {
		echo json_encode( array("result" => "error", "text" => "ID for delete doesn't specified") );
		die();
	}
}


// update
if ( isset($params["update"]) && is_array($params["update"]) ) {
	foreach ( $params["update"] as $rowToUpdate ) {
		if ( isset($rowToUpdate["id"]) ) {
			$volume = $rowToUpdate["length"] * $rowToUpdate["width"] * $rowToUpdate["thickness"] / 10 * $rowToUpdate["quantity"];
			$sql = "UPDATE `wh_tasks` SET
				`type` = '" . $rowToUpdate["type"] . "',
				`length` = " . $rowToUpdate["length"] . ",
				`width` = " . $rowToUpdate["width"] . ",
				`thickness` = " . $rowToUpdate["thickness"] . ",
				`sort_1` = " . $rowToUpdate["sort_1"] . ",
				`sort_2` = " . $rowToUpdate["sort_2"] . ",
				`sanding` = " . $rowToUpdate["sanding"] . ",
				`quantity` = " . $rowToUpdate["quantity"] . ",
				`volume` = " . $volume . ",
				`comment` = '" . $rowToUpdate["comment"] . "'
				WHERE `id` = " . $rowToUpdate["id"] . " LIMIT 1";
			
			//echo $sql . "<br>";
			$result = mysql_query($sql, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
			if ( $result ) $successText[] = "Успешно обновлено<br>";
			$totlalResult = $totlalResult & $result;
		} else {
			echo json_encode( array("result" => "error", "text" => "ID for update doesn't specified") );
			die();
		}
	}
}

// insert
if ( isset($params["insert"]) && is_array($params["insert"]) ) {
	foreach ( $params["insert"] as $rowToUpdate ) {
		if ( isset($rowToUpdate["id"]) or true ) {
			$rowToUpdate["readyDate"] = date("Y-m-d H:i", strtotime($rowToUpdate["readyDate"]) );
			$volume = $rowToUpdate["length"] * $rowToUpdate["width"] * $rowToUpdate["thickness"] / 10 * $rowToUpdate["quantity"];
			$sql = "INSERT INTO `wh_tasks` (`taskType`, `readyDate`, `type`, `length`, `width`, `thickness`, `sort_1`, `sort_2`, `sanding`, `quantity`, `volume`, `comment`) VALUES (
				'" . $rowToUpdate["taskType"] . "',
				'" . $rowToUpdate["readyDate"] . "',
				'" . $rowToUpdate["type"] . "',
				 " . $rowToUpdate["length"] . ",
				 " . $rowToUpdate["width"] . ",
				 " . $rowToUpdate["thickness"] . ",
				 " . $rowToUpdate["sort_1"] . ",
				 " . $rowToUpdate["sort_2"] . ",
				 " . $rowToUpdate["sanding"] . ",
				 " . $rowToUpdate["quantity"] . ",
				 " . $volume . ",
				 '" . $rowToUpdate["comment"] . "')";
			//echo $sql . "<br>";
			$result = mysql_query($sql, $connection_stat) or die( "error in query for 'result': " . mysql_error($connection_stat) );
			if ( $result ) {
				strpos($rowToUpdate["id"], "copy") === false ? $successText[] = "Успешно добавлено<br>" : $successText[] = "Успешно скопировано<br>";
				$totlalResult = $totlalResult & $result;
			}
		} else {
			echo json_encode( array("result" => "error", "text" => "Insert ID doesn't specified. Please shure to provide an array as params.insert") );
			die();
		}
	}
}


if ( $totlalResult ) {
	echo json_encode( array("result" => "ok", "text" => implode($successText)) );
} else {
	echo json_encode( array("result" => "error", "text" => "DB error") );
	die();
}


?>