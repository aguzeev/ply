document.title = 'Редактирование выполненных объёмов — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	
	$('#timeBoarNavDate').datepicker({
		onSelect: function(dateText, inst) {
			dd = dateText.split('.'); // переводим между долбанными форматами
			gotoDate(dd[2] + '-' + dd[1] + '-' + dd[0]);
		}
	});
	
	$('#timeBoarNavOperation').chosen();
	$('.editTimeboardCellDialogInput').chosen();

	
	var d = new Date();
	$('#timeBoarNavDate').attr( 'value', currDate );
	
	// устанавливаем выбранными пункты фильтра, полученные из GET
	if (typeof GETvalues['operations'] != "undefined") {
		operations = GETvalues['operations'].split(',');
		for (var key in operations) {
			$('#timeBoarNavOperation option[value=' + operations[key] + ']').attr("selected", "yes");
		}
		$('#timeBoarNavOperation').trigger('liszt:updated');
	}
	

	$('.timeBoardDay').on('click', function() { // показать диалог с начислениями за день
		var id = this.id;
		var params = new Array();
		params = id.split('_');
		// получаем название операции
		$.getJSON('includes/getOperationInfo.php', {
			operationId: params[3],
		}, function(data) {
			$('#editTimeboardCellDialog_operation').text(data[0]);
			$('#editTimeboardCellDialog_operationInput').val(params[3]);
		});
		$('#editTimeboardCellDialog_date').text(days[params[1]]);
		$('#editTimeboardCellDialog_dateInput').val(days[params[1]]);
		
		$('#timeboardList').empty();
		
		$.getJSON('includes/getTimeboardPerDay.php', {
			operationId: params[3],
			timeboardDate: days[params[1]]
		}, function(data) {
			$('#timeboardList').empty();
			if (data == '') { // если пусто
				$('#timeboardList').html('<p>Нет данных о работавших сотрудниках за выбранный период. Необходимо сначала внести эту информацию в табель.</p>');
			} else { // если не пусто
				// data[0][0]] — время начала
				// data[0][1] — время окончания
				// data[0][2] — массив с id сотрудников, один сотрудник — data[0][2][0]
				// data[0][3] — массив с ФИО сотрудников, один сотрудник — data[0][3][0]
				// data[0][4] — массив сотрудников, участвовавших в выполнении работы
				// data[0][5] — массив с ФИО сотрудников, участвовавших в выполнении работы
				// data[0][6] — выполненный за период по данной операции объём
				
				//$('#debug3').text(data[0][0] + "; " + data[0][1] + '; ' + data[0][2][0]);
				for (var key in data) {
					//форматируем даты для вывода
					begin = data[key][0].substring(11, 16);
					end = data[key][1].substring(11, 16);
					if (begin == '08:00' && end == '16:00') { timemarkB = 'I смена'; timemarkE = ''; per = 1; }
						else if (begin == '16:00' && end == '00:00') { timemarkB = 'II смена'; timemarkE = ''; per = 2; }
						else if (begin == '00:00' && end == '08:00') { timemarkB = 'III смена'; timemarkE = ''; per = 3; }
						else { timemarkB = begin; timemarkE = end; per = 0; }
						
					
					$('#timeboardList').append('<div class="timeboardListBlock" id="tlb_' + key + '">' + 
					'<div style="display: inline-block; margin: 0 10px 0 0; text-align: left;">' + 
					
					 
					'<div class="editTimeboardCellDialogLabel">' + 
					'<div id="editTiming_' + key + '" class="editTiming"><input type="text" id="timeboardNewPeriod_dateBegin' + key + '" value="' + timemarkB + '" name="begin_' + 
					key + '" class="timeBoarEditPeriod" /><br />' + 
					'<input type="text" id="timeboardNewPeriod_dateEnd' + key + '" value="' + timemarkE + '" name="end_' + 
					key + '" class="timeBoarEditPeriod" /><br /></div>' +
					
					
					// смены
					'<span class="asSmallButton" data-key="' + key + '" data-per="1">I</span>' + 
					'<span class="asSmallButton" data-key="' + key + '" data-per="2">II</span>' + 
					'<span class="asSmallButton" data-key="' + key + '" data-per="3">III</span><br /></div>' + 
					
					'<div class="productionCelContainer">' +
					'<span style="font-size: 12px" id="productionCellDialog_workers_' + key + '" name="workers_' + key + '">В смене работали: </span><br />' +
					'<span>Выполненный объём: </span><input type="text" size="10" id="productionCellDialog_value_' + key + '" name="value_' + key + '" /> м³</div>' +
					
					'<hr color="#CFCFCF" style="width: 522px" /></div>');
					
					// заполняем существующие значения объёма
					$('#productionCellDialog_value_' + key).val(data[key][6]);
					
					// помечаем кнопку с номером смены
					$("span[data-key=" + key + "][data-per=" + per + "]").css('background-color', '#C77');
					
					// скрываем поле редактирования времени там, где есть смена
					if (per != 0) $('div#editTiming_' + key).hide();
					// и убираем кнопки выбора смены там, где смены нет
					if (per == 0) $('span.asSmallButton[data-per]:[data-key=' + key + ']').hide();
					
					
					//Заполняем span со списком работавших сотрудников
					//alert(data[key][5]);
					for (var markedWorkerKey in data[key][4]) {
						if (markedWorkerKey > 0) $('#productionCellDialog_workers_' + key).append(", ");
						$('#productionCellDialog_workers_' + key).append(data[key][5][markedWorkerKey]);
					}
				} // конец «если не пусто»
			}
		});
		
		
		
		

		$('#editTimeboardCellDialog').dialog('open');
	});
	
	$('#editTimeboardCellDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 550,
		height: 700,
		buttons: {
			"Сохранить": function() {
				timeboardObject = new Array(); obj = new Array();
				$('#currentTimeboard div.timeboardListBlock').each(function(index, element) {
                    obj = {
						timeBegin: $(this).find("input[name^='begin_']").val(),
						timeEnd: $(this).find("input[name^='end_']").val(),
						prodValue: $(this).find("input[name^='value_']").val()
					}
					timeboardObject.push(obj);
                });
				$.get("includes/insertProductionPerDay.php", {
					operationId: document.getElementById('editTimeboardCellDialog_operationInput').value,
					timeboardDate: document.getElementById('editTimeboardCellDialog_dateInput').value,
					timeboardObject: $.toJSON( timeboardObject )
				}, function(data) {
					location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
});

function periodPreset(num) { // формирует 2 временные метки
	var beginPreset = new Date;
	var endPreset = new Date;
	
	var datePreset = $('#editTimeboardCellDialog_dateInput').attr( 'value' );
	var parts = datePreset.split('-');
	beginPreset.setUTCFullYear(parts[0], parts[1] - 1, parts[2]);
	endPreset.setUTCFullYear(parts[0], parts[1] - 1, parts[2]);
	switch (num) {
		case 1:
			beginPreset.setHours(8, 0);
			endPreset.setHours(16, 0);
		break;
		case 2:
			beginPreset.setHours(16, 0);
			endPreset = addDays(endPreset, 1); endPreset.setHours(0, 0);
		break;
		case 3:
			beginPreset = addDays(beginPreset, 1); beginPreset.setHours(0, 0);
			endPreset = addDays(endPreset, 1); endPreset.setHours(8, 0);
		break;
	}
	$('#timeboardNewPeriod_dateBegin').attr( 'value', dateTimeFormatting(beginPreset) );
	$('#timeboardNewPeriod_dateEnd').attr( 'value', dateTimeFormatting(endPreset) );
}

function periodTextPreset(num, obj) { // формирует строку с названием смены и вторую строку — пустую
	switch (num) {
		case 1: worktime = 'I смена'; break;
		case 2: worktime = 'II смена'; break;
		case 3: worktime = 'III смена'; break;
	}
	$('#timeboardNewPeriod_dateBegin' + obj).val(worktime);
	$('#timeboardNewPeriod_dateEnd' + obj).val('');
}


function showTiming(num, source) {
	$('#editTiming_' + num).show();
	//$('#editTiming_' + num).next().hide();
}


var now = new Date();
today = dateFormatting(now, '-', 'desc');
yesterday = dateFormatting( addDays(now, -1), '-', 'desc');
currDate = (typeof GETvalues['lastdate'] != "undefined") ? GETvalues['lastdate'] : today;

function gotoDate(date) {
	if (typeof GETvalues['lastdate'] == "undefined") newUrl = window.location.href + '&lastdate=' + date;
		else newUrl = window.location.href.replace(GETvalues['lastdate'], date);
	
	// и заодно проверяем фильтр операций
	var operations = $('#timeBoarNavOperation').val();
	if (operations != null) {
		if (typeof GETvalues['operations'] == "undefined") newUrl = newUrl + '&operations=' + operations;
			else newUrl = newUrl.replace(GETvalues['operations'], operations);
	}
	
	window.location.href = newUrl;
}

function applyFilter() {
	var operations = $('#timeBoarNavOperation').val();
	newUrl = window.location.href;
	
	if (operations != null) {
		if (typeof GETvalues['operations'] == "undefined") newUrl = newUrl + '&operations=' + operations;
			else newUrl = newUrl.replace(GETvalues['operations'], operations);
			
		window.location.href = newUrl;
	} else {
		//alert('Сначала выберите необходимые операции.');
		clearFilter();
	}
	
}
function clearFilter() {
	newUrl = window.location.href.replace('&operations=' + GETvalues['operations'], '');
	window.location.href = newUrl;
}