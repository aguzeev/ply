<?php

defined('_EXEC') or die;
$category = 0;
$ACCESSED_MODULE = 0;
$_LOGGING = false;
require_once('includes/init.php');
include('includes/cerber.php');

?>

<div style="width: 80%; max-width: 1000px; margin: 10px auto;">
<?php
if ( filesize("updates.txt") > 3 ) {
	echo "<p style='font-weight: bold'>Обновления</p>";
	$file = fopen("updates.txt", "r");
	while ( ($line = fgets($file, 4096)) != false ) {
		$update = explode("\t", $line);
		echo "<p class='updateDate'><span>" . $update[0] . ":</span>&nbsp;" . $update[1] . "</p>";
	}
} else {
	echo "<p>Обновлений нет</p>";
}
?>
</div>
