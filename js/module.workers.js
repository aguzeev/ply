document.title = 'Редактирование штатного расписания — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	$('#editWorkerDialog_op').chosen();
	$('#addWorkerDialog_op').chosen();
	$('#editWorkerDialog_sector').chosen();
	$('#addWorkerDialog_sector').chosen();
	$('#editWorkerDialog_app').chosen();
	$('#addWorkerDialog_app').chosen();
	$('#timeBoarNavSector').chosen().change(function() { renewApps('timeBoarNavApp', $('#timeBoarNavSector').val()) });
	$('#timeBoarNavApp').chosen();
	$('#editWorkerDialog_status').chosen();
	
	// устанавливаем выбранными пункты фильтра, полученные из GET
	if (typeof GETvalues['sectors'] != "undefined") {
		sectors = GETvalues['sectors'].split(',');
		for (var key in sectors) {
			$('#timeBoarNavSector option[value=' + sectors[key] + ']').attr("selected", "yes");
		}
		$('#timeBoarNavSector').trigger('liszt:updated');
		
		if (typeof GETvalues['appoints'] != "undefined") appoints = GETvalues['appoints'].split(','); else appoints = 0;
		renewApps('timeBoarNavApp', $('#timeBoarNavSector').val(), appoints);
	} else renewApps('timeBoarNavApp', $('#timeBoarNavSector').val());
	
	if (typeof GETvalues['showinactive'] != "undefined") { // показать неактивных
		$('#showinactive').attr("checked", "checked");
	}
	
	
	$('#editWorkerDialog_dStart').datepicker({
		onSelect: function( selectedDate ) { $( "#editWorkerDialog_dFinish" ).datepicker( "option", "minDate", selectedDate ); } });
	$('#editWorkerDialog_dFinish').datepicker({
		onSelect: function( selectedDate ) { $( "#editWorkerDialog_dStart" ).datepicker( "option", "maxDate", selectedDate ); } });
	$('#addWorkerDialog_dStart').datepicker({
		onSelect: function( selectedDate ) { $( "#addWorkerDialog_dFinish" ).datepicker( "option", "minDate", selectedDate ); } });
	$('#addWorkerDialog_dFinish').datepicker({
		onSelect: function( selectedDate ) { $( "#addWorkerDialog_dStart" ).datepicker( "option", "maxDate", selectedDate ); } });
			
	origList = $('#editWorkerDialog_op').html();

	$('#editWorkerDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 550,
		buttons: {
			"Сохранить": function() {
				dSt = document.getElementById('editWorkerDialog_dStart').value;
				if (dSt != '') {
					dTemp = dSt.split('.');
					dSt = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
					dSt = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
				} else dSt = 0;
				
				dFin = document.getElementById('editWorkerDialog_dFinish').value;
				if (dFin != '') {
					dTemp = dFin.split('.');
					dFin = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
					dFin = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
				} else dFin = 0;
				var isActive = $('#editWorkerDialog_status').val();
				$.get("includes/editWorker.php", {
					workerId: document.getElementById('editWorkerDialog_id').value,
					nFamily: document.getElementById('editWorkerDialog_nFamily').value,
					nFirst: document.getElementById('editWorkerDialog_nFirst').value,
					nMiddle: document.getElementById('editWorkerDialog_nMid').value,
					sector: document.getElementById('editWorkerDialog_sector').value,
					app: document.getElementById('editWorkerDialog_app').value,
					op:  $('#editWorkerDialog_op').val(),
					isActive: isActive,
					dateStart: dSt,
					dateFinish: dFin
				}, function(data) {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$('#removeWorkerDialog').dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Удалить": function() {
				$.get("includes/editWorker.php", {
					workerId: document.getElementById('removeWorkerDialog_id').value,
					act: 'remove'
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
	$('#addWorkerDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 500,
		buttons: {
			"Добавить сотрудника": function() {
				dSt = document.getElementById('addWorkerDialog_dStart').value;
				if (dSt != '') {
					dTemp = dSt.split('.');
					dSt = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
					dSt = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
				} else dSt = 0;
				
				dFin = document.getElementById('addWorkerDialog_dFinish').value;
				if (dFin != '') {
					dTemp = dFin.split('.');
					dFin = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
					dFin = dTemp[2] + '-' + dTemp[1] + '-' + dTemp[0];
				} else dFin = 0;
				
				$.get("includes/editWorker.php", {
					workerId: 'new',
					nFamily: document.getElementById('addWorkerDialog_nFamily').value,
					nFirst: document.getElementById('addWorkerDialog_nFirst').value,
					nMiddle: document.getElementById('addWorkerDialog_nMid').value,
					sector: document.getElementById('addWorkerDialog_sector').value,
					app: document.getElementById('addWorkerDialog_app').value,
					op:  $('#addWorkerDialog_op').val(),
					isActive: 1,
					dateStart: dSt,
					dateFinish: dFin
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
})


function editWorker(workerId) {
	$('#editWorkerDialog_op').html(origList); // восстанавливаем спсок до начального состояния
	$('#editWorkerDialog').dialog('open');
	document.getElementById('editWorkerDialog_id').value = workerId;
	$.getJSON("includes/getWorkerInfo.php",
	  {"workerId": workerId},
	function(data) {
		document.getElementById('editWorkerDialog_nFamily').value = data[0];
		document.getElementById('editWorkerDialog_nFirst').value = data[1];
		document.getElementById('editWorkerDialog_nMid').value = data[2];
		document.getElementById('editWorkerDialog_sector').value = data[3];
		document.getElementById('editWorkerDialog_dStart').value = data[7];
		document.getElementById('editWorkerDialog_dFinish').value = data[8];
		document.getElementById('editWorkerDialog_status').value = data[9];
		$('#editWorkerDialog_sector').trigger('liszt:updated');
		$('#editWorkerDialog_status').trigger('liszt:updated');
		//$('#editWorkerDialog_sector').val( data[3] );
		
		renewApps( 'editWorkerDialog_app', data[3], data[4] ); // обновляем выпадающий список должностей в подразделении (подразделение, активный пункт должности)
			
		// data[6] — персональные операции
		// сначала снимаем выбор с тех пунктов, которые могут быть выбраны ранее
		$('#editWorkerDialog_op option').removeAttr('selected').trigger('liszt:updated');
		// а теперь отмечаем только нужные
		if (data[6]) {
			for (var key in data[6]) {
				var sel = data[6][key];
				$('#editWorkerDialog_op option[value=' + sel + ']').attr('selected', 'yes').trigger('liszt:updated');
			}
		}
		
		// data[5][x][0] — id операции из должности
		// data[5][x][1] — название операции из должности
		var txt = '';
		for (var key in data[5]) {
			if (key > 0) txt += '<br />';
			txt += data[5][key][1];
			//alert(data[5][key][1]);
// !!! -----------------------------------------------
			// убираем из списка дополнительных операций те, что уже определены должностью
			$('#editWorkerDialog_op option[value="' + data[5][key][0] + '"]').remove();
		}
		$('#editWorkerDialog_op').trigger('liszt:updated');
		$('span#editWorkerDialog_defOp').html(txt);
	})
}
function renewApps(field, dept, active) {
	//field — id поля select, в котором нужно отобразить результат
	//dept — id подразделения, в котором выполнить поиск
	// active — value пунктов меню, в который сделать выбранным
	if (typeof(field) != 'object') {
		console.log('Ошибка передачи id выпадающего списка должностей');
	}
	$.getJSON("includes/getAppointmentsInDept.php",
		{"dept": dept},
			function(data) {
				$('#' + field).empty();
				
				for (var key in data) {
					$('#' + field).append( $('<option value="' + data[key]['id'] + '">' + data[key]['appointment'] + '</option>'));
				}
				if (active) {
					for (var key in active) {
						$('#' + field + ' option[value=' + active[key] + ']').attr('selected', 'yes');
					}
				}
				$('#' + field).trigger('liszt:updated');
	});
	
}
function addWorker() {
	$('#addWorkerDialog').dialog('open');
}
function removeWorker(workerId) {
	$('#removeWorkerDialog').dialog('open');
	document.getElementById('removeWorkerDialog_id').value = workerId;
	$.getJSON("includes/getWorkerInfo.php",
	  {"workerId": workerId},
	function(data) {
		$('#removeWorkerDialog span').html(data[0]);
	})
}
function applyFilter() {
	var sectors = $('#timeBoarNavSector').val();
	var appoints = $('#timeBoarNavApp').val();
	var showinactive = ($('#showinactive').attr("checked") == "checked") ? true : false;
	
	newUrl = window.location.href;
	if (showinactive) { // показать неактивных
		if (typeof GETvalues['showinactive'] == "undefined") newUrl = newUrl + '&showinactive=1';
		else newUrl = newUrl.replace('&showinactive=' + GETvalues['showinactive'], '&showinactive=1'); // showinactive задан в GET
	} else newUrl = newUrl.replace('&showinactive=' + GETvalues['showinactive'], '');
		
	if (appoints == null) { // конкретные должности не указаны
		newUrl = newUrl.replace('&appoints=' + GETvalues['appoints'], ''); // могли остаться старые значения
		if (sectors != null) { // задано подразделение целиком
			if (typeof GETvalues['sectors'] == "undefined") newUrl = newUrl + '&sectors=' + sectors;
				else newUrl = newUrl.replace('&sectors=' + GETvalues['sectors'], '&sectors=' + sectors);
			window.location.href = newUrl;
		} else {
			alert('Сначала выберите должности или целое подразделение.');
		}
	} else { // заданы конкретные должности
		newUrl = newUrl.replace('&sectors=' + GETvalues['sectors'], '');
		newUrl = newUrl + '&sectors=' + sectors;
		if (typeof GETvalues['appoints'] == "undefined") newUrl = newUrl + '&appoints=' + appoints;
			else newUrl = newUrl.replace('&appoints=' + GETvalues['appoints'], '&appoints=' + appoints);
		window.location.href = newUrl;
	}
}
function clearFilter() {
	newUrl = window.location.href.replace('&sectors=' + GETvalues['sectors'], '');
	newUrl = newUrl.replace('&appoints=' + GETvalues['appoints'], '');
	newUrl = newUrl.replace('&showinactive=' + GETvalues['showinactive'], '');
	window.location.href = newUrl;
}