document.title = 'Справочник форматов фанеры — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	$('#plyThickness').mask("99.9");
	$("#addToCatalogDialog").dialog({
		autoOpen: false,
		buttons: {
			"Добавить": function() {
				if ( $("#addToCatalogForm").valid() ) addFormatToDict();
			}
		}
	});
	
	$("#addToCatalogForm").validate({
		errorPlacement: function(error, element) {
			$( element ).next(".errorContainer").html( error );
		}
	});
	
	$("#addFormatButton").on("click", function() { $("#addToCatalogDialog").dialog( "open" ); });
});

function addFormatToDict() {
	$.ajax({
		url: "includes/warehouse/addFormatToDict.php",
		dataType: "json",
		data: {
			type: $("#plyType").val(),
			length: $("#plyLength").val(),
			width: $("#plyWidth").val(),
			thickness: $("#plyThickness").val().replace(".", "")
		}
	}).done(function(data) {
		if ( data.result == "ok" ) {
			$("#addToCatalogDialog").dialog( "close" );
			myAlert("Формат успешно добавлен в справочник", "success");
			setTimeout( function() { window.location.reload(); 	}, 1000 );
		} else if ( data.result == "restricted" ) {
			myAlert(data.text, "error");
		}else if ( data.result == "duplicate" ) {
			myAlert("Такой формат уже есть в справочнике", "error");
		} else {
		}
	});
}