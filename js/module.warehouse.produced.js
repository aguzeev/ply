document.title = 'Склад — Система статистики Хардвуд трейдинг';
$(document).ready(function(e) {
	$('#packsTableTmpl').template('packsTableTmpl');
    drawTable();
});

function getPacks( params ) {
	getPacksRequest = $.ajax({
		url: "includes/warehouse/getPacks.php",
		dataType: "json",
		data: { params: JSON.stringify( params ) }
	});
	return getPacksRequest;
}

function drawTable() {
	$.when( getPacks() ).done(function(data) {
		console.log(data);
		if ( data.result == "ok" ) {
			$.tmpl('packsTableTmpl', data).appendTo('#packs');
		} else if ( data.result == "empty" ) $("#packs").empty().append("<p align='center'>Ничего не найдено</p>");
		else {
				myAlert(data.text, "error");
				return false;
		}
	});
}