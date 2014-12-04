document.title = 'Редактирование табеля — Система статистики Хардвуд трейдинг';
var prevDayWarningText = "*Периоды времени, начинающиеся до 07:59, будут отображены<br />в предыдущих сутках.";


var now = new Date();
today = dateFormatting(now, '-', 'desc');
yesterday = dateFormatting( addDays(now, -1), '-', 'desc');
currDate = (typeof GETvalues['lastdate'] != "undefined") ? GETvalues['lastdate'] : today;

function gotoDate(date) { // навигатор по датам
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

function applyOpFilter() {
	var ops = $('#timeBoarNavOperation').val();
	if (ops != null) {
		if (typeof GETvalues['operations'] == "undefined") newUrl = window.location.href + '&operations=' + ops;
			else newUrl = window.location.href.replace(GETvalues['operations'], ops);
		window.location.href = newUrl;
	} else clearOpFilter();
}
function clearOpFilter() {
	newUrl = window.location.href.replace('&operations=' + GETvalues['operations'], '');
	window.location.href = newUrl;
}

$(document).ready(function() {
	
	$('#timeBoarNavDate').datepicker({
		onSelect: function(dateText, inst) {
			dd = dateText.split('.'); // переводим между долбанными форматами
			gotoDate(dd[2] + '-' + dd[1] + '-' + dd[0]);
		}
	});
	
	if (typeof GETvalues['operations'] != "undefined") { // задан фильтр операций
		var ops = GETvalues['operations'].split(",");
		for (var key in ops) {
			$('#timeBoarNavOperation option[value=' + ops[key] + ']').attr('selected', 'yes');
		}
		$('#timeBoarNavOperation').trigger('liszt:updated');
	}
	
	$('#timeBoarNavOperation').chosen();
	$('.editTimeboardCellDialogInput').chosen();
	
	
	var d = new Date();
	$('#timeBoarNavDate').attr( 'value', currDate );
	
	// кнопки выбора смены в диалоге
	$('span.asSmallButton[data-key]').live('click', function() {
		// получаем значение счётчика для подстановки в идентификатор
		num =  $(this).attr('data-key');
		switch ( $(this).text() ) {
			case 'I': worktime = 'I смена'; break;
			case 'II': worktime = 'II смена'; break;
			case 'III': worktime = 'III смена'; break;
		}
		$('#timeboardNewPeriod_dateBegin' + num).val(worktime);
		$('#timeboardNewPeriod_dateEnd' + num).val('');
		$("span[data-key=" + num + "]").css('background-color', '#FBFBFB');
		$(this).css('background-color', '#C77');
	});
	
	// кнопки показа полей времени в диалоге
	$('span[data-type="displayTiming"]').live('click', function() {
		num =  $(this).attr('data-key');
		$('#editTiming_' + num).show();
		$(this).hide();
	});

// редактирование ячейки табеля
	$('.timeBoardDay').on('click', function() {
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
			timeboardDate: days[params[1]],
			workday: document.getElementById('editTimeboardCellDialog_dateInput').value
		}, function(data) {
			$('#timeboardList').empty();
			if (data == '') { // если пусто
				$('#timeboardList').html('<p>Нет данных о работавших сотрудниках за выбранный период. Вы можете добавить эту информацию ниже.</p>');
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
						
					/*$('#currentTimeboard').append('<div class="timeboardListBlock">' + 
					'<input type="text" class="editTimeboardCellDialogLabel" value="' + timemark + '"></input><br />' + 
					'<input type="text" class="editTimeboardCellDialogLabel" value="' + timemark + '"></input>' + 
					'<select id="editTimeboardCellDialog_workers_' + key + '" name="workers_' + key + '" class="editTimeboardCellDialogInput" multiple="multiple" data-placeholder="Выберите одного или нескольких сотрудников"></select>' + 
					'</div>');*/
					
					$('#timeboardList').append('<div class="timeboardListBlock" id="tlb_' + key + '">' + 
					'<div style="display: inline-block; margin: 0 10px 0 0; text-align: center;">' + 
					
					 
					'<div class="editTimeboardCellDialogLabel">' + 
					'<div id="editTiming_' + key + '" class="editTiming"><input type="text" id="timeboardNewPeriod_dateBegin' + key + '" value="' + timemarkB + '" name="begin_' + 
					key + '" class="timeBoarEditPeriod" data-id="' + key + '" /><br />' + 
					'<input type="text" id="timeboardNewPeriod_dateEnd' + key + '" value="' + timemarkE + '" name="end_' + 
					key + '" class="timeBoarEditPeriod" data-id="' + key + '" /><br /></div>' +
					
					
					// смены
					'<span class="asSmallButton" data-key="' + key + '" data-per="1">I</span>' + 
					'<span class="asSmallButton" data-key="' + key + '" data-per="2">II</span>' + 
					'<span class="asSmallButton" data-key="' + key + '" data-per="3">III</span><br />' + 
					'<span class="smallLink" data-key="' + key + '" data-type="displayTiming">уточнить время</span></div>' + 
					
					'<div style="display: inline-block; vertical-align: top; text-align: left;">' +
					'<select id="editTimeboardCellDialog_workers_' + key + '" name="workers_' + key + '" class="editTimeboardCellDialogInput" multiple="multiple" data-placeholder="Выберите одного или нескольких сотрудников"></select><br />' +
					'<span class="prevDayWarning" id="prevDayWarning_' + key + '"></span></div>' +
					'<div class="removeImg"><a href="javascript:removePeriod(' + key + ')"><img src="img/remove.png" alt="Удалить запись"/></a></div>' +
					'<hr color="#CFCFCF" /></div>');
					
					// помечаем кнопку с номером смены
					$("span[data-key=" + key + "][data-per=" + per + "]").css('background-color', '#C77');
					
					// скрываем поле редактирования времени там, где есть смена
					if (per != 0) $('div#editTiming_' + key).hide();
					// и убираем кнопку «уточнить время» там, где смены нет
					if (per == 0) $('span[data-type="displayTiming"]:[data-key=' + key + ']').hide();
					
					$('#timeboardNewPeriod_dateBegin' + key).timepicker({
						hourGrid: 4,
						minuteGrid: 10,
						onSelect: function(dateText, inst) { // предупреждение об отнесении периода к предыдущим суткам
							var id = $(this).attr('data-id');
							if ( dateText.substring(0, 2) < 8 ) $('#prevDayWarning_' + id).html(prevDayWarningText);
								else $('#prevDayWarning_' + id).empty();
						}
					});
					$('#timeboardNewPeriod_dateEnd' + key).timepicker({ hourGrid: 4, minuteGrid: 10 });
					
					//Заполняем выпадающий список сотрудников
					for (var workerKey in data[key][2]) {
						$('#editTimeboardCellDialog_workers_' + key).append('<option value="' + data[key][2][workerKey] + '">' +
						data[key][3][workerKey] + '</option>');
					}
	
					//Помечаем сотрудников, которые уже указаны в периоде
					for (var markedWorkerKey in data[key][4]) {
						$('#editTimeboardCellDialog_workers_' + key + ' option[value=' + data[key][4][markedWorkerKey] + ']').attr('selected', 'yes').trigger('liszt:updated');
					}
					
					
					$('#editTimeboardCellDialog_workers_' + key).chosen({myHideResults: false});
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
						workers: $(this).find("select[name^='workers_']").val()
					}
					timeboardObject.push(obj);
                });
				$.get("includes/insertTimeboardPerDay.php", {
					operationId: document.getElementById('editTimeboardCellDialog_operationInput').value,
					timeboardDate: document.getElementById('editTimeboardCellDialog_dateInput').value,
					//timeBegin: document.getElementById('timeboardNewPeriod_dateBegin').value,
					//timeEnd: document.getElementById('timeboardNewPeriod_dateEnd').value,
					//workerIds: document.getElementById('timeboardNewPeriod_workers').value
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


function addNewPeriod() {
	flagSureToClose = true;
	var key = $('#currentTimeboard div.timeboardListBlock').length; // количество уже существующих записей
	editPeriodHtml = '<div class="timeboardListBlock" id="tlb_' + key + '">' + 
		'<div style="display: inline-block; margin: 0 10px 0 0; text-align: center;">' + 
		 
		'<div class="editTimeboardCellDialogLabel">' + 
		'<div id="editTiming_' + key + '" class="editTiming" style="display: none;"><input type="text" id="timeboardNewPeriod_dateBegin' + key + '" value="начало" name="begin_' + 
		key + '" class="timeBoarEditPeriod" data-id="' + key + '" />—<br />' + 
		'<input type="text" id="timeboardNewPeriod_dateEnd' + key + '" value="окончание" name="end_' + 
		key + '" class="timeBoarEditPeriod" data-id="' + key + '" /><br /></div>' +
		
		// смены
		'<span class="asSmallButton" data-key="' + key + '">I</span>' + 
		'<span class="asSmallButton" data-key="' + key + '">II</span>' + 
		'<span class="asSmallButton" data-key="' + key + '">III</span><br />' + 
		'<span class="smallLink" data-key="' + key + '" data-type="displayTiming">уточнить время</span></div>' + 
		
		'<div style="display: inline-block; vertical-align: top; text-align: left;">' +
		'<select id="editTimeboardCellDialog_workers_' + key + '" name="workers_' + key + '" class="editTimeboardCellDialogInput" multiple="multiple" data-placeholder="Выберите одного или нескольких сотрудников"></select><br />' +
		'<span class="prevDayWarning" id="prevDayWarning_' + key + '"></span></div>' +
		'<div class="removeImg"><a href="javascript:removePeriod(' + key + ')"><img src="img/remove.png" alt="Удалить запись"/></a></div>' +
		'<hr color="#CFCFCF" /></div>';

	$('#timeboardList').append(editPeriodHtml);
	// заполняем выпадающий список всеми сотрудниками, допущенными к этой операции
	$.get("includes/getWorkersByOperation.php",
		{
			operationId: document.getElementById('editTimeboardCellDialog_operationInput').value,
			workday: document.getElementById('editTimeboardCellDialog_dateInput').value
		},
		function(data) {
			$('#editTimeboardCellDialog_workers_' + key).chosen({myHideResults: false});
			$('#editTimeboardCellDialog_workers_' + key).empty();
			$('#editTimeboardCellDialog_workers_' + key).append(data).trigger('liszt:updated');
		}
	);
	$('#timeboardNewPeriod_dateBegin' + key).timepicker({
		hourGrid: 4,
		minuteGrid: 10,
		onSelect: function(dateText, inst) { // предупреждение об отнесении периода к предыдущим суткам
			var id = $(this).attr('data-id');
			if ( dateText.substring(0, 2) < 8 ) $('#prevDayWarning_' + id).html(prevDayWarningText);
				else $('#prevDayWarning_' + id).empty();
		}
	});
	$('#timeboardNewPeriod_dateEnd' + key).timepicker({ hourGrid: 4, minuteGrid: 10 });
}
function removePeriod(num) {
	$('#tlb_' + num).remove();
}
function showTiming(num, source) {
	$('#editTiming_' + num).show();
	//$('#editTiming_' + num).next().hide();
}