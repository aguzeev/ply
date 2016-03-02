document.title = 'Задания на опиловку и упаковку — Система статистики Хардвуд трейдинг';
var blankRow = {"isEditable": true, "isNewRow": true, "taskData": {"date_text": "", "id": "new", "type": "F", "type_text": "", "width": 0, "length": 0, "thickness": 0, "sort_1": 1, "sort_2": 1, "sanding": 0, "sanding_text": "", "quantity": 0, "comment": "", "readyness":"—"}};
var activeTabContent; // jQuery pointer to selected tab's content
var isInEditMode = { lopping: false, packing: false };

$(document).ready(function() {
	$( "#tasksCommonBlock" ).tabs({
		activate: function(event, ui) { activeTabContent = ui.newPanel; showHideSaveCancelButtons(); },
		create: function(event, ui) { activeTabContent = ui.panel; showHideSaveCancelButtons(); }
	});
	$("#plyReadyDate").datepicker({
		buttonImage: "img/edit.png",
		buttonImageOnly: true,
		buttonText: "Выберите дату",
		minDate: 1,
	});
	$("#addTaskDateSelectorDialog").dialog({
		autoOpen: false,
		buttons: [
			{ text: "Ок", click: function() {
				console.log( activeTabContent );
				if ( $( activeTabContent ).find( "#loppingTasks" ).length > 0 ) taskType = "lopping";
				else taskType = "packing";
				
				addNewRow( $("#plyReadyDate").val(), $("#plyReadyShift input:checked").val(), null, taskType );
				$(this).dialog( "close" );
			} },
			{ text: "Отменить", click: function() { $(this).dialog( "close" ); }	}
		]
	});
	$("#deleteTaskDialog").dialog({
		autoOpen: false,
		width: 500,
		buttons: {
			"Удалить": function() { tasksHandler({ "delete": [$("#taskToDeleteId").val()] }); $(this).dialog( "close" ); },
			"Отмена": function() { $(this).dialog( "close" ); }
		}
	});
	$("#cancelEditedButton").on("click", function() {
		reloadTasks();
		$(activeTabContent).attr("data-isEdited", "false"); showHideSaveCancelButtons(); // save & cancel buttons
		$("#addTaskDateSelectorDialog").dialog( "close" );
	});
	
	$("body").on("keypress", "#tasksCommonBlock input, #tasksCommonBlock select", function(event) {
		if ( event.keyCode == 13 ) prepareToSave();
	});
	
	$("body").on("keypress", function(event) {
		if ( event.keyCode == 27 ) {
			reloadTasks();
			$(activeTabContent).attr("data-isEdited", "false"); showHideSaveCancelButtons(); // save & cancel buttons
			$("#addTaskDateSelectorDialog").dialog( "close" );
		}
	});
	
	$("body").on("click", ".icon-edit", function() {
		$(activeTabContent).attr("data-isEdited", "true");
		showHideSaveCancelButtons();
		var id = "rowID_" + $(this).attr("data-id");
		var rowContentToEdit = serializeRow( $(activeTabContent).find("tr#" + id) ); // getting row content into JSON
		
		$(activeTabContent).find("tr#" + id).replaceWith( $("#taskEditingTmpl").tmpl( rowContentToEdit ) );
		$(".whThickness input").mask("99.9");
		$(activeTabContent).find("tr#" + id).find(".whType input:checked").trigger("click"); // to filter formats dropdown list — see corresp event
	});
	$("body").on("click", ".icon-export", function() { copyRowToPacking( $(this).parents("tr") ); });
	$("#saveEditedButton").on("click", function() { prepareToSave(); });
	
	$("body").on("click", ".icon-trash", function() { // deleting
		if ( $(this).parents("tr").is(".isNewRow") ) { // the row haven't been written to DB yet
			$(this).parents("tr").remove();
		} else { // the row are in DB, so need to proceed a DELETE query
			$("#taskToDeleteId").val( $(this).attr("data-id") );
			$("#taskToDeleteIdText").text( $(this).attr("data-id") );
			$("#deleteTaskDialog").dialog( "open" );
		}
	});
	$("body").on("click", ".whType input", function() {
		if ( $(this).val() == "F" ) { // FSF
			$(this).parents("tr").find(".whSortSelect option[value=0]").attr("disabled", true);
			$(this).parents("tr").find(".whSortSelect option[value!=0]").attr("disabled", false);
			if ( $(this).parents("tr").find(".whSortSelect option[value=0]").is(":selected") )
				$(this).parents("tr").find(".whSortSelect option[value=1]").attr("selected", true);  // choose 1 if 0 selected
		} else { // FBV
			$(this).parents("tr").find(".whSortSelect option[value=0]").attr("selected", true); // choose 0
			$(this).parents("tr").find(".whSortSelect option[value=0]").attr("disabled", false);
			$(this).parents("tr").find(".whSortSelect option[value!=0]").attr("disabled", true);
		}
	});
	
	$("body").on("click", "tr.addNewRow", function() {
		addNewRow( $(this).attr("data-date"), $(this).attr("data-shift") );
		$(activeTabContent).attr("data-isEdited", "true"); showHideSaveCancelButtons(); // save & cancel buttons
	});
	$("#addTaskButton").on("click", function() {
		$("#addTaskDateSelectorDialog").dialog( "open" );
		$(activeTabContent).attr("data-isEdited", "true"); showHideSaveCancelButtons(); // save & cancel buttons
	});
	
	reloadTasks( "lopping" );
	reloadTasks( "packing" );
});

function reloadTasks( taskType ) {
	$(activeTabContent).attr("data-isEdited", "false"); showHideSaveCancelButtons();
	if ( typeof(taskType) == "undefined" ) {
		// if taskType doesn't specified, then reload current tab
		if ( $(activeTabContent).is("#loppingTasks") ) taskType = "lopping";
		else taskType = "packing";
	}
	$.ajax({
		url: "includes/warehouse/getTasks.php",
		dataType: "json",
		data: { taskType: taskType }
	}).done(function(data) {
		if ( data.result == "ok" ) drawTable( taskType, data.content );
		else if ( data.result == "empty" ) drawTable( taskType, "empty" );
		else myAlert(data.text, "error");
	});
}

function drawTable( taskType, content, activeTabContent_local ) {
	if ( typeof(activeTabContent_local) == "undefined" ) activeTabContent_local = activeTabContent;
	
	var tgt;
	if ( taskType == "lopping" ) {
		tgt = $("#loppingTasks");
	} else {
		tgt = $("#packingTasks");
	}
	
	$( tgt ).empty();
	if ( content === "empty" ) $( tgt ).html( "<p align='center'>Нет заданий</p>" );
	else {
		//$("#taskTableHeadingTmpl").tmpl({ taskType: taskType }).appendTo( tgt );
				
		/*$.each( content, function(index, value) {
			$( tgt ).find(".whTasks").append("<tbody data-date='" + index  + "'" +
			( !value[0].isEditable ? " class='currentDayTask'" : "" ) +
			"><tr><td colspan='12' class='whDate whUnhoverable'>" + value[0].date_text +
			"</td></tr><tr class='lastRow" +
			( value[0].isEditable ? " addNewRow" : "" ) +
			"' data-date='" + index + "'><td colspan='12'>" +
			( value[0].isEditable ? "<i class='isIcon icon-plus-circle isPointer'><span>&#xe805;</span></i>" : "" ) +
			"</td></tr>");
			
			$( tgt ).find("tbody[data-date='" + index + "']").find("tr.lastRow").before( $("#taskTmpl").tmpl( value ) );
		});*/
		
		//$( tgt ).find(".whTasks").append( $("#tasksBodyTmpl").tmpl({"data": content}) );
		
		tmplData = {
			taskType: taskType,
			data: content
		};
		//console.log(tmplData);	
		//console.log($( "#taskCompleteTableTmpl" ).tmpl( tmplData ));
		
		$( "#taskCompleteTableTmpl" ).tmpl( tmplData ).appendTo( tgt );
	}
}

function addNewRow( date, d_shift, editData, taskType ) {
	if ( typeof(editData) == "undefined" || editData === null ) {
		editData = blankRow;
		editData.taskData.id = "new" + Math.floor( Math.random() * 1000 );
	}
	if ( $(activeTabContent).find("table.whTasks").length == 0 ) { // need to create new table
		$( "#taskCompleteTableTmpl" ).tmpl({ taskType: taskType}).appendTo( activeTabContent );
		console.log("New table created for " + taskType);
	}
	if ( $(activeTabContent).find("tbody[data-date='" + date + "'][data-shift=" + d_shift + "]").length == 0 ) { // need to create new tbody
		$(activeTabContent).find(".whTasks").append("<tbody data-date='" + date + "' data-shift='" + d_shift + "'><tr><td colspan='12' class='whDate whUnhoverable'>" +
		moment(date, "DD.MM.YYYY").format("LL") + ", " + d_shift + " смена" +
		"</td></tr><tr class='lastRow addNewRow' data-date='" + date + "' data-shift='" + d_shift + "'><td colspan='12'><i class='isIcon icon-plus-circle isPointer'><span>&#xe805;</span></i></td></tr></tbody>");
		//console.log("new tBody");
	}
		
	$(activeTabContent).find("tbody[data-date='" + date + "'][data-shift=" + d_shift + "]").find("tr.lastRow").before( $("#taskEditingTmpl").tmpl( editData ) );
	$(".whThickness input").mask("99.9");
}

function serializeRow( row ) {
	var out = new Object();
	out.taskData = {};
	
	var d_shift = $( row ).parents("tbody").attr("data-shift");
	if ( d_shift == 1 ) d_s = " 09:00"; else d_s = " 21:00";
	
	if ( $(row).is(".notSavedYet") ) { // editing mode — td with input
		// may be both editing existing or adding new
		out.taskData.id = $( row ).find("td.taskNumber").text();
		out.taskData.taskType = $( row ).parents("table.whTasks").attr("data-taskType");
		out.taskData.readyDate = $( row ).parents("tbody").attr("data-date") + d_s;
		out.taskData.type = $( row ).find("td.whType").find("input:checked").attr("value");
		out.taskData.length = $( row ).find("td.whLength").find("input").val();
		out.taskData.width = $( row ).find("td.whWidth").find("input").val();
		out.taskData.thickness = parseFloat( $( row ).find("td.whThickness").find("input").val() ) * 10;
		out.taskData.sort_1 = $( row ).find("td.whSort1").find("option:selected").attr("value");
		out.taskData.sort_2 = $( row ).find("td.whSort2").find("option:selected").attr("value");
		out.taskData.sanding = $( row ).find("td.whSanding").find("option:selected").attr("value");
		out.taskData.quantity = $( row ).find("td.whQuantity").find("input").val();
		out.taskData.comment = $( row ).find("td.whComment").find("input").val();
		out.isEditable = true;
		out.isNewRow = false;
	} else { // normal display mode — simple td text
		out.taskData.id = $( row ).find("td.taskNumber").text();
		out.taskData.taskType = $( row ).parents("table.whTasks").attr("data-taskType");
		out.taskData.readyDate = $( row ).parents("tbody").attr("data-date") + d_s;
		out.taskData.type = $( row ).find("td.whType").attr("data-value");
		out.taskData.length = $( row ).find("td.whLength").text();
		out.taskData.width = $( row ).find("td.whWidth").text();
		out.taskData.thickness = parseFloat( $( row ).find("td.whThickness").text() ) * 10;
		if ( out.taskData.thickness < 100 ) out.taskData.thickness = "0" + out.taskData.thickness;
		out.taskData.sort_1 = $( row ).find("td.whSort1").text(); if ( out.taskData.sort_1 == "—" ) out.taskData.sort_1 = 0;
		out.taskData.sort_2 = $( row ).find("td.whSort2").text(); if ( out.taskData.sort_2 == "—" ) out.taskData.sort_2 = 0;
		out.taskData.sanding = $( row ).find("td.whSanding").attr("data-value");
		out.taskData.quantity = $( row ).find("td.whQuantity").text();
		out.taskData.comment = $( row ).find("td.whComment").text();
		out.isEditable = true;
		out.isNewRow = false;
	}
	//console.log(out);
	
	return out;
}

function prepareToSave() {
	var edited = Array(); var added = Array();
	var serialized = {};
	
	var validated = true;
	$(".notSavedYet").each(function(index, element) { // old records wich have been edited
		if ( validateRow(element) == true ) { // is valid
			if ( $(element).is(".isNewRow") ) { // just added
				serialized = serializeRow( $(element) );
				added.unshift( serialized.taskData );
			} else { // only edited
				serialized = serializeRow( $(element) );
				edited.unshift( serialized.taskData );
			}
		} else { // is not valid
			validated = false;
		}
	});
	if ( validated ) {
		checkFormats( [edited, added] );
	}
}

function copyRowToPacking( tr ) {
	var rowData = serializeRow(tr);
	rowData.taskData.id = "new_packing_copy";
	rowData.taskData.taskType = "packing";
	tasksHandler( { "insert": [rowData.taskData] } );
}

function tasksHandler( params ) {
	if ( typeof(params) != "undefined" ) {
		/*params = {
			"delete": [1, 2, 3],
			"update": [
				{ id: 1, width: 1200, length: 1400 },
				{ id: 2, width: 1440, length: 2400 } ],
			"insert": [
				{ id: "new_1", width: 1200, length: 1400 },
				{ id: "new_2", width: 1440, length: 2400 } ]
		};*/
		
		// params = { "delete": [1, 2, 3] };
		
		$.ajax({
			url: "includes/warehouse/tasksHandler.php",
			type: "POST",
			dataType: "json",
			data: {
				params: JSON.stringify(params),
			}
		}).done(function(data) {
			if ( data.result == "ok" ) {
				myAlert( data.text, "success" );
			} else if ( data.result == "error" || data.result == "restricted" ) {
				myAlert( data.text, "error" );
			} else {
				myAlert( "Неизвестная ошибка", "error" );
			}
			reloadTasks( "lopping" );
			reloadTasks( "packing" );
		});
	} else {
		console.log("No data to save");
	}
}

function showHideSaveCancelButtons() {
	if ( $(activeTabContent).attr("data-isEdited") == "true" ) { $("#saveEditedButton, #cancelEditedButton").show(); }
	else { $("#saveEditedButton, #cancelEditedButton").hide(); }
}

function checkFormats( formats ) {
	var arrayToCheck = Array();
	for ( var i in formats ) {
		if ( formats[i].length > 0 ) {
			for ( var j in formats[i] ) {
				arrayToCheck.push( formats[i][j] );
				$(activeTabContent).find("tr#rowID_" + formats[i][j]["id"]).find(".processIcon").css("visibility", "visible");
			}
		}
	}
	
	$.ajax({
		url: "includes/warehouse/checkFormats.php",
		type: "POST",
		dataType: "json",
		data: { formats: JSON.stringify( arrayToCheck ) },
	}).done(function(data) {
		$(activeTabContent).find(".processIcon").css("visibility", "hidden");
		if ( data.result == "ok" ) {
			var totalFormatValidation = true;
			
			for ( var i in data.checkResults ) {
				if ( data.checkResults[i] ) {
					$(activeTabContent).find("tr#rowID_" + i).find(".noSuchFormatIcon").css("visibility", "hidden");
					$(activeTabContent).find("tr#rowID_" + i).find(".fieldError").removeClass("fieldError");
				} else {
					totalFormatValidation = false;
					$(activeTabContent).find("tr#rowID_" + i).find(".noSuchFormatIcon").css("visibility", "visible");
					$(activeTabContent).find("tr#rowID_" + i).find(".whType, .whLength, .whWidth, .whThickness").addClass("fieldError");
				}
			}
			if ( totalFormatValidation ) tasksHandler({ "update": formats[0], "insert": formats[1] });	
		} else if ( data.result == "empty" ) {
			$(activeTabContent).attr("data-isEdited", "false"); showHideSaveCancelButtons();
		} else {
			myAlert( "Неизвестная ошибка", "error" );
		}
	});
}

function validateRow( row ) {
	var isValid = true;
	
	var elements = [
		$( row ).find("td.whLength").find("input"),
		$( row ).find("td.whWidth").find("input"),
		$( row ).find("td.whThickness").find("input"),
		$( row ).find("td.whQuantity").find("input"),
	];
	
	$( elements ).each(function(index, element) {
        if ( $( element ).val() == "" || $( element ).val() == 0 ) {
			$( element ).addClass("fieldError");
			isValid = false;
		} else {
			$( element ).removeClass("fieldError");
		}
    });
	
	return  isValid;
}