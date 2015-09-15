<?php

if ( isset($_GET["width"]) ) $width = $_GET["width"];
if ( isset($_GET["height"]) ) $height = $_GET["height"];

if ( isset($width) && isset($height) ) {
	$file = fopen("warmer-out.txt", "a");
	date_default_timezone_set('Europe/Moscow');
	$string = date("H:i:s d.m.Y") . "	" . str_replace(".", ",", $width) . "	" . str_replace(".", ",", $height) . "\n"; echo $string;
	fwrite($file, $string);
	fclose($file);
} else {
	echo "Not enough data";
}

?>