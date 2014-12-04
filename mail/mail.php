<?php

if ( isset($_GET['magicword']) ) {
	if ( $_GET['magicword'] == 'please' ) {	
		if ( isset($_GET['service']) ) {
			$services = array("boiler", "lopping", "merger-old", "merger-new", "press");
			$service = array_search( addslashes($_GET['service']), $services );
			
			$message = "Служба для участка " . $services[$service] . " была перезапущена.";
		}
		mail("a.guzeev@gmail.com", "Перезапуск службы на сервере hardwood", $message);
	} else {
		die();
	}
} else {
	die();
}
?>