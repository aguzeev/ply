// © Александр Гузеев (Alexander Guzeev), 2012—2014

p_url = window.location.search.substring(1);
var POSTparametr = p_url.split("&");
var POSTvalues = new Array();
for(i in POSTparametr) {
    var j = POSTparametr[i].split("=");
    POSTvalues[j[0]] = unescape(j[1]);
}

var p_url = location.search.substring(1);
var parametr = p_url.split("&");
var GETvalues = new Array();
for(i in parametr) { var j=parametr[i].split("="); GETvalues[j[0]]=unescape(j[1]); }
act = GETvalues['act'];

move3to2date = new Date(2014, 9, 1, 8, 0, 0); // 01 November 2014, 08:00 — moving from 3-shifts day to 2-shifts (смены)

$(document).ready(function() {
	//$("#billNavigation a[href*=" + act + "]").css("background-image", "url(img/menu-bg-hover.png)");
	
	$('.editable tr').mouseover(function() {
		$('#editBar_' + this.id).css('visibility', 'visible');
		$('table.editBar:not(#editBar_' + this.id + ')').css('visibility', 'hidden');
	});
	$('.editable tr').mouseout(function() {
		id = '#editBar_' + this.id;
		$(id).css('visibility', 'hidden');
		//$(id).find('select').css('visibility', 'hidden');
	});
	$('.updates *').click(function() {
		$('#updatesList').toggle();
		//$(id).find('select').css('visibility', 'hidden');
	});
	
	// вывод ошибок
	$.ajaxSetup({error: function() {
		myAlert("Произошла ошибка, попробуйте обновить страницу.", "error");
	} });
	$.pnotify.defaults.styling = "jqueryui";
	$.pnotify.defaults.history = false;
	$.pnotify.defaults.closer = true;
	$.pnotify.defaults.closer_hover = false;
	$.pnotify.defaults.delay = 3000;
	$.pnotify.defaults.animate_speed = "normal";
	
	$( "#showError" ).dialog({
		modal: true,
		width: 400,
		autoOpen: false,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$(document).ajaxSuccess(function(evt, request, settings){
		/*var response = $.evalJSON(request.responseText);
		if ( response.result == "error" ) {
			showError(response.text, response.source);
		}*/
	});
	
	// Статус машин в мониторинге
	//renewMachinesStatus();
	//window.setInterval(renewMachinesStatus, 60000 * 5); // 5 минут
});

function renewMachinesStatus() {
	// Статус машин в мониторинге
	$('ul#monitoring li').css("background-image", "url(img/loading_small.gif) 90% 50% no-repeat");
	$('ul#monitoring li').css('border-left-color', '#707070');
	
	$.getJSON('includes/monitoring/checkBatchStatus.php', function(data) {
		for (var i in data) {
			$('ul#monitoring li[data-mach=' + i + ']').css('border-left-color', data[i] ? '#74d274' : '#dc3c2b');
			$('ul#monitoring li[data-mach=' + i + ']').css("background-image", "none");
		}
	});
}

function showError(text, source) {
	$(document).ready(function() {
		$('#errorText, #errorSource').empty();
		$('#errorText').text(text);
		if (typeof source != "undefined") $('#errorSource').text("Ошибка в модуле " + source + '.');
		$( "#showError" ).dialog("open");
	})
}
function myAlert(text, alertType) {
	typeof(alertType) == 'undefined' ? alertType = 'notice' : false;
	alertType == 'success' ? addclass = 'myAlertSuccess' : addclass = false;
	var pnotify = $.pnotify({
		text: text,
		type: alertType,
		addclass: addclass
	});
}

function dateFormatting(myDate, separator, order) { // дата, [разделитель], [порядок], по умолчанию asc — ДД.ММ.ГГГГ
	var sep = (typeof separator != "undefined") ? separator : '.';
	var ord = (typeof order != "undefined") ? order : 'asc';
	var month = myDate.getMonth() + 1;
		if (month < 10) month = '0' + month;
	var day = myDate.getDate();
		if (day < 10) day = '0' + day;
	var year = myDate.getFullYear();
	if (ord == 'asc') myDate = day + sep + month + sep + year;
		else myDate = year + sep + month + sep + day;
	return myDate;
}
function dateTimeFormatting(myDate) {
	var month = myDate.getMonth() + 1;
		if (month < 10) month = '0' + month;
	var day = myDate.getDate();
		if (day < 10) day = '0' + day;
	var year = myDate.getFullYear();
	var hours = myDate.getHours();
		if (hours < 10) hours = '0' + hours;
	var minutes = myDate.getMinutes();
		if (minutes < 10) minutes = '0' + minutes;

	myDate = day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
	return myDate;
}
function addDays(date, n) {
	// добавление или отнимание целого количества дней
	var d = new Date();	
	d.setTime(date.getTime() + n * 24 * 60 * 60 * 1000);
	return d;
}
function strToMYSQLDate(str) {
	temp = str.split('.');
	return temp[2] + '-' + temp[1] + '-' + temp[0];
}

function markElements(elem) { //выделение неверно заполненных полей
	var oldBg = '#FFF';
	
	//$("span.redArrow").remove();
	for (var key in elem) {
		//oldBg = $(elem[key]).css( 'background-color' );
		if (oldBg == 'transparent') oldBg = '#FFF';
		$('#' + elem[key]).stop().clearQueue()
		.animate({backgroundColor: "#f65252"}, 50 ).delay(100)
		.animate({backgroundColor: oldBg}, 50 ).delay(100)
		.animate({backgroundColor: "#f65252"}, 50 ).delay(100)
		.animate({backgroundColor: oldBg}, 50 ).delay(100)
		.animate({backgroundColor: "#f65252"}, 50 ).delay(1000)
		.animate({backgroundColor: oldBg}, 1000 );
		
		//$('#' + elem[key]).after('<span class="redArrow"><img src="/img/red-arrow.png" style="margin: 0 0 0 5px;" /></span>');
	}
}

function process_daily_count() {
	var alertStart = myAlert("Выполняется обновление дневных значений и максимумов.");
	$("#dailyCountFrame").attr("src", "tasks/generateDailyValues.php");
	$("#maxesFrame").attr("src", "tasks/maxes.php");
}