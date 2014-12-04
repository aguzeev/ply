document.title = 'Редактирование табеля — Система статистики Хардвуд трейдинг';



// ============= Работа с переходами на даты  ====================
var now = new Date();

if (now.getMonth() > 0) {
	thisMonth = now.getFullYear() + '-' + (now.getMonth() + 1);
	prevMonth = now.getFullYear() + '-' + now.getMonth();
} else {
	thisMonth = now.getFullYear() + '-' + (now.getMonth() + 1);
	prevMonth = (now.getFullYear() - 1) + '-0';
}

today = dateFormatting(now, '-', 'desc');
currDate = (typeof GETvalues['showmonth'] != "undefined") ? GETvalues['showmonth'] : today;

function gotoDate(date) { // навигатор по датам
	if (typeof GETvalues['showmonth'] == "undefined") newUrl = window.location.href + '&showmonth=' + date;
		else newUrl = window.location.href.replace(GETvalues['showmonth'], date);
	window.location.href = newUrl;
}
// =================================

function applyFilter() {
	var workers = $('#timeBoarNavNameApp').val();
	var sectors = $('#timeBoarNavSector').val();
	var month = $('#timeBoarNavMonth').val(); if (month < 10) month = '0' + month;
	var year = $('#timeBoarNavYear').val(); if (year < 10) year = '0' + year;
	
	if (typeof GETvalues['showmonth'] == "undefined") newUrl = window.location.href + '&showmonth=' + year + '-' + month;
		else newUrl = window.location.href.replace(GETvalues['showmonth'], year + '-' + month);
	
	if (workers != null) {
		if (typeof GETvalues['workers'] == "undefined") newUrl = newUrl + '&workers=' + workers;
			else newUrl = newUrl.replace(GETvalues['workers'], workers);
			
		window.location.href = newUrl;
	} else if (sectors != null) {
		if (typeof GETvalues['sectors'] == "undefined") newUrl = newUrl + '&sectors=' + sectors;
			else newUrl = newUrl.replace(GETvalues['sectors'], sectors);
		newUrl = newUrl.replace('&workers=' + GETvalues['workers'], ' ');
		
		//newUrl = newUrl.replace(' ', ''); // избавляемся от пробелов
		window.location.href = newUrl;
	} else {
		alert('Сначала выберите сотрудников или целое подразделение.');
	}
	
}
function clearFilter() {
	newUrl = window.location.href.split('?');
	act = GETvalues['act'];
	newUrl = newUrl[0] + '?&act=' + act;
			
	window.location.href = newUrl;
}

$(document).ready(function() {
	$('#timeBoarNavSector').chosen().change( function() { renewApps(); });
	$('#timeBoarNavAppointment').chosen();
	$('#timeBoarNavNameApp').chosen();
	$('#timeBoarNavMonth').chosen({disable_search_threshold: 12});
	$('#timeBoarNavYear').chosen({disable_search_threshold: 12});
	
	
	$('#accuralDetails').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons: {
			"Ok": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	// устанавливаем выбранными пункты фильтра, полученные из GET
	if (typeof GETvalues['sectors'] != "undefined") {
		sectors = GETvalues['sectors'].split(',');
		for (var key in sectors) {
			$('#timeBoarNavSector option[value=' + sectors[key] + ']').attr("selected", "yes");
		}
		$('#timeBoarNavSector').trigger('liszt:updated');
	}
	renewApps();
	
	// установление значений фильтра для поля «сотрудники» СМОТРИ в функции renewApps();
	if (typeof GETvalues['showmonth'] != "undefined") {
		$('#timeBoarNavYear option[value=' + parseFloat(GETvalues['showmonth'].substr(0, 4)) + ']' ).attr("selected", "yes").trigger('liszt:updated');
		$('#timeBoarNavMonth option[value=' + parseFloat(GETvalues['showmonth'].substr(5, 6)) + ']' ).attr("selected", "yes").trigger('liszt:updated');
	}
	
});



function showDetails(workerId, day) {
	$.getJSON('includes/getAccuralInfo.php', {
		workerId: workerId,
		day: day
	}, function(data) {	
		dayToShow = day.substr(8, 2) + '.' + day.substr(5, 2) + '.' + day.substr(0, 4)
		$('#accuralDetails').dialog({title: "Расчёт за " + dayToShow});
		$('#accuralDetailsCont').empty();
		$("#detailTemplate").tmpl( data['parts'] ).appendTo( "#accuralDetailsCont" );
		$("#accuralDetailsCont").append('<hr /><h4>Всего начислено: ' + data['sum'] + ' руб.</h4>');
		$('#accuralDetails').dialog( "open" );
		
	});
}
function renewApps() {
	//field — id поля select, в котором нужно отобразить результат
	//dept — id подразделения, в котором выполнить поиск
	//active — value пункта меню, в который сделать выбранным
	/*$.getJSON("includes/getAppointmentsInDept.php",
		{"dept": dept},
			function(data) {
				$('#timeBoarNavAppointment').empty();
				
				for (var key in data) {
					$('#timeBoarNavAppointment').append( $('<option value="' + data[key][0] + '">' + data[key][1] + '</option>'));
				}
				$('#timeBoarNavAppointment').trigger('liszt:updated');
	});*/
	dept = ($("#timeBoarNavSector").val() != null) ? $("#timeBoarNavSector").val(): 0;
	$.getJSON("includes/getWorkersAppInDept.php",
	{"dept": dept.toString()},
	function(data) {
		$( "#timeBoarNavNameApp" ).empty();
		$( "#workerAppTemplate" ).tmpl( data ).appendTo( "#timeBoarNavNameApp" );
		$( "#timeBoarNavNameApp" ).trigger('liszt:updated');
		
		if (typeof GETvalues['workers'] != "undefined") {
		workers = GETvalues['workers'].split(',');
		for (var key in workers) {
			$('#timeBoarNavNameApp option[value=' + workers[key] + ']').attr("selected", "yes");
		}
		$('#timeBoarNavNameApp').trigger('liszt:updated');
	}
	});
}

function getCompleteAccural(showMonth, showNextMonth, sectors, workers) {
	$('#completeAccural').html('<div style="width: 100%; text-align: center; margin: 25px auto;"><img src="../img/loading.gif" /></div>');
	$.get("includes/getCompleteAccural.php",
	{
		showMonth: showMonth,
		showNextMonth: showNextMonth,
		sectors: sectors,
		workers: workers
	},
	function(data) {
		$('#completeAccural').html(data);
	});
}
