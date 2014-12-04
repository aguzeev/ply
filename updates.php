<img src="img/news.png" alt="Обновления" title="Обновления" /><!--<span class="updatesCounter"></span>-->
<div class="updatesList" id="updatesList">
<?php
if ( filesize("updates.txt") > 3 ) {
	echo "<p style='font-weight: bold'>Обновления</p>";
	$file = fopen("updates.txt", "r");
	$linesCount = 0;
	while ( ($line = fgets($file, 4096)) != false && $linesCount < 5 ) {
		$update = explode("\t", $line);
		echo "<p>" . $update[1] . "</p>";
		$linesCount++;
	}
	if ( $linesCount == 5 ) { // то есть в файле есть ещё строки.
		echo "<p align='center'><a href='index.php?act=updates'>Предыдущие обновления</a></p>";
	}
} else {
	echo "<p>Обновлений нет</p>";
}
?>
