document.title = 'Задания на упаковку — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	$("#plyReadyDate").datepicker({
		showOn: "button",
		buttonImage: "img/edit.png",
		buttonImageOnly: true,
		buttonText: "Выберите дату",
		minDate: 1,
	});
	$('#plyThickness').mask("99.9");
	$("#addEditTaskDialog").dialog({
		autoOpen: false,
		width: 500,
		buttons: [
			{
				//text: "Добавить",
				id: "dialogButton",
				click: function() {
					if ( $("#addEditTask").valid() ) { addEditTask(); }
				}
			},
			{
				text: "Отменить",
				click: function() {
					$("#formAction").val( "" ); $(this).dialog( "close" );
				}
			}
		]
	});
	$("#deleteTaskDialog").dialog({
		autoOpen: false,
		width: 500,
		buttons: {
			"Удалить": function() {
				$("#formAction").val( "delete" );
				addEditTask();
			},
			"Отмена": function() {
				$("#formAction").val( "" );
				$(this).dialog( "close" );
			}
		}
	});
	
	$("#addTask").validate({
		errorPlacement: function(error, element) {
			$( element ).next(".errorContainer").html( error );
		}
	});
	
	$("#addTaskButton").on("click", function() { // adding
		$('#dialogButton').button("option", "label", "Добавить");
		$("#formAction").val("add");
		$("#rowId").val("add");
		$("#addEditTaskDialog").dialog( "open" );
	});
	
	$(".whTasks .icon-edit").on("click", function() { // editing
		$('#dialogButton').button("option", "label", "Сохранить изменения");
		var id = $(this).attr("data-id");
		
		$("#formAction").val( "edit" );
		$("#rowId").val( $(this).attr("data-id") );
		$("#plyReadyDate").val( $("#rowID_" + id + " .whReadyDate").attr("data-value") );
		$("#plyType option[value=" + $("#rowID_" + id + " .whType").attr("data-value") + "]").prop("selected", true);
		$("#plyLength").val( $("#rowID_" + id + " .whLength").text() );
		$("#plyWidth").val( $("#rowID_" + id + " .whWidth").text() );
		
		var thickness = $("#rowID_" + id + " .whThickness").text();
		thickness.length < 3 ? thickness = "0" + thickness : thickness = thickness;
		$("#plyThickness").val( thickness.substr(0, 2) + "." + thickness.substring(2, 3) );
		
		$("#plySort_1").val( $("#rowID_" + id + " .whSort1").text() );
		$("#plySort_2").val( $("#rowID_" + id + " .whSort2").text() );
		
		$("input[name=plySanding]").filter("[value=" + $("#rowID_" + id + " .whSanding").attr("data-value") + "]").prop("checked", true);
		$("#plyQuantity").val( $("#rowID_" + id + " .whQuantity").text() );
		$("#plyComment").val( $("#rowID_" + id + " .whComment").text() );
		
		$("#addEditTaskDialog").dialog( "open" );
	});
	
	$(".whTasks .icon-cancel-circled").on("click", function() { // deleting
		$("#formAction").val( "delete" );
		$("#rowId").val( $(this).attr("data-id") );
		$("#taskToDeleteNumber").text( $(this).attr("data-id") );
		$("#deleteTaskDialog").dialog( "open" );
	});
	
});

function addEditTask() {
	$.ajax({
		url: "includes/warehouse/addEditTask.php",
		dataType: "json",
		data: {
			id: $("#rowId").val(),
			action: $("#formAction").val(),
			readyDate: $("#plyReadyDate").val(),
			type: $("#plyType").val(),
			length: $("#plyLength").val(),
			width: $("#plyWidth").val(),
			thickness: $("#plyThickness").val().replace(".", ""),
			sort_1: $("#plySort_1").val(),
			sort_2: $("#plySort_2").val(),
			sanding: $("input[name=plySanding]:checked").val(),
			quantity: $("#plyQuantity").val(),
			comment: $("#plyComment").val(),
		}
	}).done(function(data) {
		if ( data.result == "ok" ) {
			$("#addToCatalogDialog").dialog( "close" );
			myAlert(data.text, "success");
			setTimeout( function() { window.location.reload(); 	}, 1000 );
		} else if ( data.result == "error" ) {
			myAlert(data.text, "error");
		} else {
			myAlert("Неизвестная ошибка", "error");
		}
	});
}