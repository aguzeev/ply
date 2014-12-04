document.title = 'Мониторинг работы оборудования — Система статистики Хардвуд трейдинг';

var p_url=location.search.substring(1);
var parametr=p_url.split("&");
var POSTvalues= new Array();
var isBroadcasting = 0;
var onlineSet;

for(i in parametr) {
    var j=parametr[i].split("=");
    POSTvalues[j[0]]=unescape(j[1]);
}

mach = (typeof POSTvalues['mach'] != "undefined") ? POSTvalues['mach'] : 0;
field = (typeof POSTvalues['field'] != "undefined") ? POSTvalues['field'] : 0;

function initDateTime() {
	currentTime = new Date();
	month = currentTime.getMonth() + 1;
		if (month < 10) month = '0' + month;
	day = currentTime.getDate();
		if (day < 10) day = '0' + day;
	year = currentTime.getFullYear();
	hours = currentTime.getHours();
		if (hours < 10) hours = '0' + hours;
	minutes = currentTime.getMinutes();
		if (minutes < 10) minutes = '0' + minutes;
}

var d1_now = new Date(); var d2_now = new Date();
d1_now.setHours(08, 00, 00);
if (d2_now.getHours() < 8) { // ещё вчера :)
	d1_now = addDays(d1_now, -1);
}
			
d1 = (typeof POSTvalues['d1'] != "undefined") ? POSTvalues['d1'] : timeFormatting(d1_now);
d2 = (typeof POSTvalues['d2'] != "undefined") ? POSTvalues['d2'] : timeFormatting(d2_now);


$(document).ready(function(){
	moment().zone("+04:00");
	
	for (var key in machineNames) {
		$('#plotMachine').append('<option value="' + key + '">' + machineNames[key] + '</option>');
	}
	/*
	$('#plotMachine').append('<optgroup id="machGroup" label="Отдельные станки:"></optgroup>');
	for (var key in machineNames) {
		$('#plotMachine optgroup#machGroup').append('<option value="' + key + '">' + machineNames[key] + '</option>');
	}
	$('#plotMachine').append('<optgroup id="complexGroup" label="Участки:"></optgroup>');
	$('#plotMachine optgroup#complexGroup').append('<option disabled="disabled" value="11">Лущение + сушка</option>');
	*/
	
	$('#dateBegin').attr( 'value', d1 );
	$('#dateEnd').attr( 'value', d2 );
	
	renewPlotFields();
	$('#plotMachine').on("change", function() {
		mach = this.value;
		renewPlotFields();
		shareLink();
	});
	$('#plotMachine').chosen( {max_selected_options: 2} );
	$('#plotField').chosen( {max_selected_options: 2} );
	$('#plotField').on("change", function() {
		//console.log( $('#plotField').val() );
		shareLink();
	});
	
	$('#plotMachine').attr( 'value', mach );
	$('#plotMachine').trigger('liszt:updated');
	
	$('#onlineBox').on("change", function() {
		$(this).attr('checked') == 'checked' ? onlineSet = 1 : onlineSet = 0;
	});
	
	$( "#tooLongPeriod" ).dialog({
		autoOpen: false,
		modal: false,
		width: 400,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$("#shareLink").on("click", function() {
		this.select();
	});
	
	setFromGet();
});
function shareLink() {
	var share = "http://www.ply-stat.ru/v2/index.php?act=monitoring.period";
	var todayLink = share;
	share += "&mach=" + $('#plotMachine').val();
	todayLink += "&mach=" + $('#plotMachine').val();
	if ( $('#plotField').val() != null && $('#dateBegin').val() != null /*&& $('#dateEnd').val() != null*/ ) {
		share += "&field=" + $('#plotField').val();
		share += "&start=" + moment( $('#dateBegin').val(), "DD.MM.YYYY HH:mm" ).format("X");
		share += "&finish=" + moment( $('#dateEnd').val(), "DD.MM.YYYY HH:mm" ).format("X");
		$("#shareLink").val(share);
		
		todayLink += "&field=" + $('#plotField').val();
		todayLink += "&start=today";
		$("#todayLink").attr("href", todayLink).show();
	} else {
		$("#shareLink").val("");
		$("#todayLink").hide();
	}
}
function setFromGet() {
	var get_mach = (typeof GETvalues['mach'] != "undefined") ? GETvalues['mach'] : false;
	var get_field = (typeof GETvalues['field'] != "undefined") ? GETvalues['field'] : false;
	var get_start = (typeof GETvalues['start'] != "undefined") ? GETvalues['start'] : false;
	var get_finish = (typeof GETvalues['finish'] != "undefined") ? GETvalues['finish'] : false;
	
	if ( get_start == "today" ) {
		var timezoneDiff = moment().zone() + 180;
		moment( moment().format("X") - timezoneDiff * 60, "X" ).hour() < 8 ? daysToSub = 1 : daysToSub = 0;
		get_start = moment().subtract(daysToSub, 'days').hour(8).minute(0).second(0).format("X") - timezoneDiff * 60;
		get_finish = moment().format("X") - timezoneDiff * 60;
	}
	
	if ( get_mach && get_field && get_start && get_finish ) {
		mach = get_mach;
		$('#plotMachine').attr( 'value', mach );
		$('#plotMachine').trigger('liszt:updated');
		renewPlotFields();
		
		field = get_field.split(",");
		$('#plotField').val( field );
		$('#plotField').trigger('liszt:updated');
		
		$('#dateBegin').attr( 'value', moment(get_start, "X").format("DD.MM.YYYY HH:mm") );
		$('#dateEnd').attr( 'value', moment(get_finish, "X").format("DD.MM.YYYY HH:mm") );
		
		shareLink();
		plotIt2();
	}
}

function renewPlotFields() {
	machine = $('#plotMachine').val();
	$('#plotField').empty();
	for (i = 0; i < plotVariants[machine].length; i++) {
		$('#plotField').append('<option value="' + i + '">' + plotVariants[machine][i].title + '</option>');
	}
	$('#plotField').trigger('liszt:updated');
}

// ============== инициализация интерфейса jQuery UI ===================
$(document).ready(function(){
	$('#dateBegin, #dateEnd').mask("99.99.9999 99:99");
	
	var startDateTextBox = $('#dateBegin');
	var endDateTextBox = $('#dateEnd');
	
	startDateTextBox.datetimepicker({ 
		hourGrid: 4,
		minuteGrid: 10,
		defaultValue: '2014',
		//defaultValue: moment().hour(8).minute(0).second(0).format('DD.MM.YYYY HH:mm'),
		onClose: function(dateText, inst) {
			if (endDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					endDateTextBox.datetimepicker('setDate', testStartDate);
			}
			else {
				endDateTextBox.val(dateText);
			}
			shareLink();
		},
		onSelect: function (selectedDateTime){
			var endBugfix = endDateTextBox.attr( 'value' ); // bugfix
			endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
			endDateTextBox.attr( 'value', endBugfix ); // bugfix
			shareLink();
		}
	});
	endDateTextBox.datetimepicker({
		hourGrid: 4,
		minuteGrid: 10,
		defaultValue: '20.06.2013',
		onClose: function(dateText, inst) {
			if (startDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					startDateTextBox.datetimepicker('setDate', testEndDate);
			}
			else {
				startDateTextBox.val(dateText);
			}
			shareLink();
		},
		onSelect: function (selectedDateTime){
			var startBugfix = startDateTextBox.attr( 'value' ); // bugfix
			startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
			startDateTextBox.attr( 'value', startBugfix ); // bugfix
			shareLink();
		}
	});

	$( "#mxSlider" ).slider({
		value: 2,
		min: 0,
		max: 4,
		step: 1,
		slide: function( event, ui ) {
			ob = $(this).parent();
			if (ui.value == 0)
				$('#mx').attr( 'value', 0.25 );
			else if (ui.value == 1)
				$('#mx').attr( 'value', 0.75 );
			else if (ui.value == 2)
				$('#mx').attr( 'value', 1 );
			else if (ui.value == 3)
				$('#mx').attr( 'value', 1.25 );
			else if (ui.value == 4)
				$('#mx').attr( 'value', 1.5 );
			else $('#mx').attr( 'value', 1 );
		}
	});
});


function clearForm(button) {
	obj = $(button+':parent').find('form');
	$(obj).find('input[type="text"]').attr('value', '');
	$(obj).find('input[type="checkbox"]').attr('checked', false);
}

function timeFormatting(myDate) {
	var month = myDate.getMonth() + 1;
		if (month < 10) month = '0' + month;
	var day = myDate.getDate();
		if (day < 10) day = '0' + day;
	var year = myDate.getFullYear();
	var hours = myDate.getHours();
		if (hours < 10) hours = '0' + hours;
	var minutes = myDate.getMinutes();
		if (minutes < 10) minutes = '0' + minutes;
	myDateTime = day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
	return myDateTime;
}

function addDays(date, n) {
	// может отличаться на час, если было переведено время
	var d = new Date();	
	d.setTime(date.getTime() + n * 24 * 60 * 60 * 1000);
	return d;
}

function plotPreset(preset) {
	initDateTime();
	var d1 = new Date(); var d2 = new Date();
	switch(preset) {
		case 'today':
		d1.setHours(08, 00, 00);
		if (d2.getHours() < 8) { // ещё вчера :)
			d1 = addDays(d1, -1);
		}
			$('input#dateBegin').val( timeFormatting(d1) );
			$('input#dateEnd').val( timeFormatting(d2) );
		break;
		case 'yesterday':
		if (d2.getHours() < 8) { // ещё вчера :)
			d2 = addDays(d2, -1);
		}
			d2.setHours(08, 00, 00);
			d1 = addDays(d2, -1);
			$('input#dateBegin').val( timeFormatting(d1) );
			$('input#dateEnd').val( timeFormatting(d2) );
		break;
		case 'week':
			if ( (d1.getDay() == 0) || (d1.getDay() >= 2) || ((d1.getDay() == 1) && (d1.getHours() < 8)) ) {
				d1.setHours(08, 00, 00);
				// если день недели — любой, кроме ПН, или ПН до 8 утра, то ищемм ближайший прошедший понедельник
				d1 = addDays(d2, -7);
				while (d1.getDay() != 1) {
					//alert(d2);
					d1 = addDays(d1, 1)
				}
			} else {
				d1.setHours(08, 00, 00);
			}
			$('input#dateBegin').val( timeFormatting(d1) );
			$('input#dateEnd').val( timeFormatting(d2) );
		break;
		case 'month':
			if ( (d1.getDate() == 1) && (d2.getHours() < 8) ) { // ещё прошлый месяц :)
				d1 = addDays(d2, -1); // переводим начало на вчера, чтобы позже установить датой 1-е число
			}
			d1.setDate(1); d1.setHours(08, 00, 00);
			
			$('input#dateBegin').val( timeFormatting(d1) );
			$('input#dateEnd').val( timeFormatting(d2) );
		break;
		default:
		break;
	}
	plotIt2();
	shareLink();
}

/*
function prepareToPlot() {
	// проверка заполненности полей
	dateBeginField = $('input#dateBegin');
	dateEndField = $('input#dateEnd');

	dateBegin = $(dateBeginField).val(); //начало периода
	dateEnd = $(dateEndField).val();
	
	if ( $(dateBeginField).val()=='' ) {
		markElements( [dateBeginField] );
		return false;
	}
	
	if ( $(dateEndField).val()=='' ) {
		markElements( [dateEndField] );
		return false;
	}
	
	if ( parseDate( $(dateBeginField).val() ) >= parseDate( $(dateEndField).val() ) ) {
		markElements( [dateBeginField, dateEndField] );
		return false;
	}
	
	field = $('#plotField').attr( 'value' );
	addState = 0;
	
	// проверяем, нужно ли показывать график
	showGraph = ($('#showGraph').attr("checked") == "checked") ? true : false;
	if (showGraph) {
		plotIt(
			'chart1',
			mach + '-' + field,
			plotVariants[mach][field],
			moment(dateBegin, "DD.MM.YYYY HH:mm").format("X"),
			moment(dateEnd, "DD.MM.YYYY HH:mm").format("X"),
			$('#mx').attr('value')
		);
	} else {
		$('#chart1').empty();
		$('#chart1').css("height", "auto");
	}
	//prepareSummary();
	selectSummarySet();
	//event.preventDefault();
}
*/

function checkOnline(button) {
	$('#onlineBox').attr('checked') == 'checked' ? onlineSet = 1 : onlineSet = 0;
	if ( $('#onlineBox').attr('checked') == 'checked') {
		$('#onlineBox').attr('disabled', 'disabled');
	} else {
		$('#onlineBox').attr('disabled', false);
	}
	
	if (onlineSet) {
		isBroadcasting = 1;
		$(button).css({'background-image': 'url(img/broadcast-bg.gif)', 'background-size': 'cover'});
		$(button).attr('value', 'остановить');
		$(button).attr('onclick', 'javascript:stopBroadcasting(this)');
		
		initDateTime();
		d2 = day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
		$('#setPlotOpts_1').find('#dateEnd').attr( 'value', d2 );
		
		timer = setInterval(function() { checkOnline(button) }, 600000);
		prepareToPlot();
		
	} else {
		if (!isBroadcasting) {
			prepareToPlot();
		} else {
			stopBroadcasting();
		}
	}
}

function stopBroadcasting(button) {
	isBroadcasting = 0;
	$('#onlineBox').attr('checked', false);
	$('#onlineBox').attr('disabled', false);
	clearInterval(timer);
	$(button).css('background-image', 'none');
	//$(button).css('background-color', '#ffff99');
	$(button).attr('value', 'отобразить');
	$(button).attr('onclick', 'javascript:checkOnline(this)');
}

function changeSliderVal(val) {
	var currSlide = $('#mxSlider').slider('value');
	switch(val) {
		case 'dec':
			if (currSlide > $('#mxSlider').slider("option", "min")) {
				$('#mxSlider').slider('value', currSlide - 1);
				$('#mx').attr('value', Math.pow(2, currSlide - 1) / 4);
			}
		break;
		case 'inc':
			if (currSlide < $('#mxSlider').slider("option", "max")) {
				$('#mxSlider').slider('value', currSlide + 1);
				$('#mx').attr('value', Math.pow(2, currSlide + 1) / 4);
			}
		break;
	}
}

function markElements(elem) {
	// выделение неверно заполненных полей
	var oldBg = '#FFF';
	for (var key in elem) {
		//oldBg = $(elem[key]).css( 'background-color' );
		if (oldBg == 'transparent') oldBg = '#FFF';
		$(elem[key]).animate({backgroundColor: "#f65252"}, 50 ).delay(100)
		.animate({backgroundColor: oldBg}, 50 ).delay(100)
		.animate({backgroundColor: "#f65252"}, 50 ).delay(100)
		.animate({backgroundColor: oldBg}, 50 ).delay(100)
		.animate({backgroundColor: "#f65252"}, 300 ).delay(2000)
		.animate({backgroundColor: oldBg}, 1000 );
	}
}
function showUserBar(obj) {
	// отображение панели пользователя
	$(obj).find('#userBar').show();
	$(obj).css('background-color', '#FEC79E');
}
function hideUserBar(obj) {
	// скрытие панели пользователя
	$(obj).find('#userBar').hide();
	$(obj).css('background-color', 'transparent');
}