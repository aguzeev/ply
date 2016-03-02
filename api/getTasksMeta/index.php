<?php

// get tasks metadata

header('Content-Type: text/json; charset=utf-8');

require_once('../../includes/init.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');

$columns = array(
	array(
		"field" => "type_text", 
		"width" => "like_caption", 
		"tr" => "Марка"
	),
	array(
		"field" => "length", 
          "width" => "like_caption", 
          "tr" => "Длина"
	),
	array(
		"field" => "width", 
          "width" => "like_caption", 
          "tr" => "Ширина"
	),
	array(
		"field" => "thickness", 
          "width" => "like_caption", 
          "tr" => "Толщина"
	),
	array(
		"field" => "sort_1", 
          "width" => "like_caption", 
          "tr" => "Сорт 1"
	),
	array(
		"field" => "sort_2", 
          "width" => "like_caption", 
          "tr" => "Сорт 2"
	),
	array(
		"field" => "sanding_text", 
          "width" => "like_caption", 
          "tr" => "Шлиф."
	),
	array(
		"field" => "quantity", 
          "width" => "like_caption", 
          "tr" => "Количество"
	),
	array(
		"field" => "comment", 
          "width" => "expanding", 
          "tr" => "Комментарий"
	)
);

$task_types = array(
	array(
		"type" => "lopping",
		"tr" => "Опиловка"
	),
	array(
		"type" => "packing",
		"tr" => "Упаковка"
	)
);

echo json_encode( array("result" => "ok", "columns" => $columns, "task_types" => $task_types), JSON_UNESCAPED_UNICODE );