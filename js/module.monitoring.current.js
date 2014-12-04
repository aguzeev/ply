document.title = 'Мониторинг текущего состояния работы — Система статистики Хардвуд трейдинг';

var titles = [ 'Лущение', 'Ножницы', 'Сушка', 'Опиловка', 'Шлифовка', 'Сращивание (нов)', 'Сращивание (стар)', 'Пресс' ];
var tableSet = [ '1-4', '3-5', '4-1', '5-7', '6-2', '6-13', '8-0' ];
var gaugeSet = [ '0-1', '1-4', '3-4', '4-1', '5-7', '6-2', '6-13', '8-0' ];
var optimal = [ 14, 10, 8, 18, 14, 4.5, 4.5, 6 ];
var daysToSub;
var timezoneDiff = moment().zone() + 180;

moment( moment().format("X") - timezoneDiff * 60, "X" ).hour() < 8 ? daysToSub = 1 : daysToSub = 0;

//console.log( "timezone offset: " + moment().zone() );
//console.log( "timezoneDiff: " + timezoneDiff );
//moment(timezoneDiff).format("DD MMMM YYYY HH:mm")

// 2-сменный график:
var colls = [
	{// нужно делать поправку на сутки, если время до 8:00
		start: moment().subtract(daysToSub, 'days').hour(8).minute(0).second(0).format("X"),
		end: moment().subtract(daysToSub, 'days').hour(19).minute(59).second(59).format("X")
	},
	{
		start: moment().subtract(daysToSub, 'days').hour(20).minute(0).format("X"),
		end: moment().subtract(daysToSub, 'days').hour(07).minute(59).second(59).add(1, 'days').format("X")
	},
	{
		//not in use
		start: moment().subtract(daysToSub, 'days').hour(0).minute(0).second(0).format("X"),
		end: moment().hour(7).minute(59).second(59).add(1, 'days').subtract(daysToSub, 'days').format("X")
	},
	{
		start: moment().subtract(daysToSub, 'days').hour(8).minute(0).second(0).format("X"),
		end: moment().format("X")
	},
	{
		start: moment().startOf('month').hour(8).minute(0).second(0).format("X"),
		end: moment().format("X")
	}
];

/*
// 3-сменный график:
var colls = [
	{// нужно делать поправку на сутки, если время до 8:00
		start: moment().subtract(daysToSub, 'days').hour(8).minute(0).second(0).format("X"),
		end: moment().subtract(daysToSub, 'days').hour(15).minute(59).second(59).format("X")
	},
	{
		start: moment().subtract(daysToSub, 'days').hour(16).minute(0).format("X"),
		end: moment().subtract(daysToSub, 'days').hour(23).minute(59).second(59).add(1, 'days').format("X")
	},
	{
		start: moment().subtract(daysToSub, 'days').hour(0).minute(0).second(0).format("X"),
		end: moment().hour(7).minute(59).second(59).add(1, 'days').subtract(daysToSub, 'days').format("X")
	},
	{
		start: moment().subtract(daysToSub, 'days').hour(8).minute(0).second(0).format("X"),
		end: moment().format("X")
	},
	{
		start: moment().startOf('month').hour(8).minute(0).second(0).format("X"),
		end: moment().format("X")
	}
];*/
	
$(document).ready(function() {
	currentMonitoring();
	//window.setInterval( function() {	currentMonitoring(); myAlert("Данные обновлены.", "success"); }, 5 * 60 * 1000 );
});

function currentMonitoring() {
	// месяц
	for (var r in tableSet) {
		$('tr[data-machine=' + r + '] td[data-field=4]').append("<img src='img/loading_small.gif' />");
		$.ajax({
			url: 'includes/monitoring/getData2.php',
			type: 'GET',
			async: true,
			dataType: 'json',
			data: {
				plot_variant: tableSet[r],
				date_begin: colls[4].start - timezoneDiff * 60,
				date_end: colls[4].end - timezoneDiff * 60,
				callback_position: r,
				divide: 0,
				use_corrections: 1
			},
			success: function(data) {
				var row = data['callback_position'].split("*")[0];
				
				$('tr[data-machine=' + row + '] td[data-field=4]').children('img').remove();
				var oldContent = $('tr[data-machine=' + row + '] td[data-field=4]').html();
				var dayVal = data['payload'].toFixed(2);
				$('tr[data-machine=' + row + '] td[data-field=4]').html( dayVal );
			},
			error: function() {
				myAlert("Ошибка загрузки сводки из базы", "error");
			}
		});
	}
	
	// смены и сутки
	for (var r in tableSet) {
		//console.log( moment(colls[0].start, "X").format("DD MMMM YYYY HH:mm") );
		//console.log( colls[0].start );
		//console.log( colls[0].start - timezoneDiff * 60 );
		$('tr[data-machine=' + r + ']').children('td[data-field=0], td[data-field=1], td[data-field=2], td[data-field=3]').append("<img src='img/loading_small.gif' />");
		$.ajax({
			url: 'includes/monitoring/getData2.php',
			type: 'GET',
			async: true,
			dataType: 'json',
			data: {
				plot_variant: tableSet[r],
				date_begin: colls[0].start - timezoneDiff * 60,
				date_end: colls[1].end - timezoneDiff * 60,
				callback_position: r,
				divide: 1,
				interval: 60 * 60 * 12,
				use_corrections: 1
			},
			success: function(data) {
				var row = data['callback_position'];
				
				$('tr[data-machine=' + row + '] td[data-field=0]').children('img').remove();
				//var oldContent = $('tr[data-machine=' + row + '] td[data-field=0]').html();
				$('tr[data-machine=' + row + '] td[data-field=0]').html(data.payload[0][1]);
				
				$('tr[data-machine=' + row + '] td[data-field=1]').children('img').remove();
				var oldContent = $('tr[data-machine=' + row + '] td[data-field=1]').html();
				$('tr[data-machine=' + row + '] td[data-field=1]').html(data['payload'][1][1]);
				
				//$('tr[data-machine=' + row + '] td[data-field=2]').children('img').remove();
				//var oldContent = $('tr[data-machine=' + row + '] td[data-field=2]').html();
				//$('tr[data-machine=' + row + '] td[data-field=2]').html(data['payload'][2][1]);
				
				$('tr[data-machine=' + row + '] td[data-field=3]').children('img').remove();
				var oldContent = $('tr[data-machine=' + row + '] td[data-field=3]').html();
				//$('tr[data-machine=' + row + '] td[data-field=3]').html( (data['payload'][0][1] + data['payload'][1][1] + data['payload'][2][1]).toFixed(2));
				$('tr[data-machine=' + row + '] td[data-field=3]').html( (data['payload'][0][1] + data['payload'][1][1]).toFixed(2));
			},
			error: function() {
				myAlert("Ошибка загрузки сводки из базы", "error");
			}
		});
	}
	
	// ===== meterGauge =====
	$("#meterContainer").empty();
	for (var r in gaugeSet) {
		$("#meterContainer").append("<div id='meter_" + r + "' class='meterGauge'><img src='img/loading_small_2.gif' style=' margin: 30% auto;' /></div>");
		
		var minutes = Math.round( moment().minute() / 5 ) * 5;
		$.ajax({
			url: 'includes/monitoring/getData2.php',
			type: 'GET',
			async: true,
			dataType: 'json',
			data: {
				plot_variant: gaugeSet[r],
				date_begin: moment().minute(minutes).second(0).subtract(1, 'hours').format("X") - timezoneDiff * 60,
				date_end: moment().minute(minutes).second(0).format("X") - timezoneDiff * 60,
				callback_position: r,
				divide: 0,
				use_corrections: 1
			},
			success: function(data) {
				var r = data.callback_position;
				var pos = 'meter_' + r;
				s1 = data.payload;
				$('#' + pos).empty();
				var gaugeMax = Math.round(optimal[r] * 1.2 / 2 ) * 2; // optimal + 20% and round to nearest integer
				
				plot1 = $.jqplot(pos,[[s1]],{
					title: {
						//text:  '<span class="gaugeTitle-machine">' + data.machineTitle + '</span>' 
						//' + <br><span class="gaugeTitle-field">' + data.title + '</span>'
						text:  '<span class="gaugeTitle-machine">' + titles[r] + '</span>'
					},
					seriesDefaults: {
						renderer: $.jqplot.MeterGaugeRenderer,
						rendererOptions: {
						   min: 0,
						   max: gaugeMax,
						   intervals:[gaugeMax * 0.2, gaugeMax * 0.8, gaugeMax],
						   intervalColors:['#EE7777', '#FFFFFF', '#66cc66']
					   }
					}
				});
			},
			error: function() {
				myAlert("Ошибка загрузки сводки из базы", "error");
			}
		});
	}
	
	
	$.ajax({
		url: 'includes/monitoring/getMaxes.php',
		type: 'GET',
		async: true,
		dataType: 'json',
		success: function(data) {
			for (var index in data) {
				console.log();
				templateData = {
					title : data[index].title,
					units : " м³",
					value : data[index].value,
					date : data[index].dateShift,
					link:  data[index].link,
					isNew: data[index].isNew
				}
				//console.log(templateData);
				$("#maxesContainer").append( $("#leaderShiftTemplate").tmpl( templateData ) );
			}
		},
		error: function() {
			myAlert("Ошибка загрузки сводки из базы", "error");
		}
	});
}