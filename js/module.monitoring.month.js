// © Александр Гузеев (Alexander Guzeev), 2012—2014

document.title = 'Мониторинг за месяц — система статистики Хардвуд трейдинг';
var yearToPlot, monthToPlot, selectedVariant, selectedVariant;
var monthVariants = ['0-1', '1-4', '3-4', '3-5', '4-1', '5-7', '6-2', '6-13', '8-0'];
var monthes = Array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');

$(document).ready(function() {
	// generate month and year select
	for (year = 2011; year <= moment().format("YYYY"); year++) $('#yearSelect').append('<option value="' + year + '">' + year + '</option>');
	
	//generate DIVs for each machine graph
	for (i = 0; i < monthVariants.length; i++) { $('#monthCompleteContainer').append('<div id="monthSummary' + i + '" class="monthSummary"></div>'); }
	
	// event handlers
	$('#yearSelect').on("change", function() {
		yearToPlot = document.getElementById('yearSelect').value;
		renewMonthSelect( yearToPlot );
	});
	
	$('div.month-heading-machine[data-tab]').on("click", function() {
		$('div.month-heading-machine[data-tab]').removeClass("active-machine");
		$(this).addClass("active-machine");
		selectedVariant = $(this).attr("data-tab");
		$('div.monthSummary').hide();
		$('div#monthSummary' + selectedVariant).show();
	});
	$('td.day').live("click", function() {
		$('div#monthSummary' + selectedVariant + ' td.day').removeClass("active-day");
		$(this).addClass("active-day");
		selectedDayDetailes = $(this).attr("data-day");
	});
	
	// set default values
	if ( typeof(GETvalues["machine"]) != "undefined" ) {
		
		var machine = GETvalues["machine"];
		
		if ( typeof(GETvalues["year"]) != "undefined" ) var year = GETvalues["year"];
		else var year = moment().format("YYYY");
		document.getElementById('yearSelect').value = year;		
		renewMonthSelect( year );
		
		if ( typeof(GETvalues["month"]) != "undefined" ) month = GETvalues["month"];
		else month = moment().format("M");
		document.getElementById('monthSelect').value = month;
		
		$('div.month-heading-machine[data-machine=' + machine + ']').trigger("click");
		
	} else {
		document.getElementById('yearSelect').value = moment().format("YYYY");
		renewMonthSelect();
		document.getElementById('monthSelect').value = moment().format("M");
		$('div.month-heading-machine[data-tab=0]').trigger("click");
	}
	
	redrawMonthTable();
	plotMonthGraph();
});

function plotMonthGraph() {
	// month for move3to2date detection
	dMoveDateDetection = moment().year(document.getElementById('yearSelect').value).month(document.getElementById('monthSelect').value - 1).startOf('month').hour(8).minute(0).second(0);
	
	if ( dMoveDateDetection >= move3to2date ) {
		var shiftInterval = 60 * 60 * 12;
		var numShifts = 2;
	} else {
		var shiftInterval = 60 * 60 * 8;
		var numShifts = 3;
	}
	
	redrawMonthTable();
	var target = 'monthGraph' + selectedVariant;
	$('#' + target).append("<img src='img/loading.gif' style='margin: 30px 0 0 0;' />");
	$('div#monthSummary'+ selectedVariant).find('.periodTitle').text(monthes[monthToPlot] + ' ' + yearToPlot);
	
	$.ajax({
		url: 'includes/monitoring/getData2.php',
		data: {
			plot_variant: monthVariants[selectedVariant],
			date_begin: moment().year(document.getElementById('yearSelect').value).month(document.getElementById('monthSelect').value - 1).startOf('month').hour(8).minute(0).second(0).format("X"),
			date_end: moment().year(document.getElementById('yearSelect').value).month(document.getElementById('monthSelect').value - 1).endOf('month').add('days', 1).hour(7).minute(59).second(59).format("X"),
			divide: 1,
			interval: shiftInterval,
			use_corrections: 1
		},
		dataType: "json"
		})
	.done(function(data) {
	
		var shift1 = Array(), shift2 = Array(), shift3 = Array(), ticks = Array(), dayVals = Array();
		var monthTotal = 0;
		var optimum = 130;
		
		
		for (var i in data.payload) {
			if (i % numShifts == 0) {
				shift1.push(data.payload[i][1]);
				var day = data.payload[i][0].split(" ")[0].split("-")[2];
				ticks.push(day);
				dayVals[Math.round(i/numShifts)] = 0; // initialize array element
			} else if (i % numShifts == 1) shift2.push(data.payload[i][1]);
			else if (i % numShifts == 2) shift3.push(data.payload[i][1]);
			dayVals[Math.floor(i/numShifts)] += data.payload[i][1];
		}
		// sum days values
		for (var i in dayVals) {
			$( '#monthSummary' + selectedVariant + ' td#day' + (parseInt(i) + 1) ).text( Math.round(dayVals[i] * 10) / 10 );
			monthTotal += dayVals[i];
		}
		
		// month total value
		monthTotal = Math.round(monthTotal * 10) / 10;
		$("div#monthSummary"+ selectedVariant + " p.monthTotal").html('Всего за месяц: ' + monthTotal + ' м<sup>3</sup>');
		
		// excel button
		var url = "includes/__toExcel.php?";
		url += "plot_variant=" + monthVariants[selectedVariant];
		url +="&date_begin=" + moment().year(document.getElementById('yearSelect').value).month(document.getElementById('monthSelect').value - 1).startOf('month').hour(8).format("X");
		url += "&date_end=" + moment().year(document.getElementById('yearSelect').value).month(document.getElementById('monthSelect').value - 1).endOf('month').add('days', 1).hour(7).minute(59).second(59).format("X");
		url += "&divide=1";
		if ( dMoveDateDetection >= move3to2date ) {
			url += "&interval=" + 60 * 60 * 12;
		} else {
			url += "&interval=" + 60 * 60 * 8;
		}
		url += "&use_corrections=1";
		$("div#monthSummary"+ selectedVariant + " a.toExcelLink").attr("href", url);
		
		// graph
		if ( dMoveDateDetection >= move3to2date ) {
			var dataToPlot = [shift1, shift2];
		} else {
			var dataToPlot = [shift1, shift2, shift3];
		}
		
		$('#' +  target).empty();
		var plot1 = $.jqplot(target, dataToPlot, {
			stackSeries: true,
			//seriesColors:['#a0deea', '#fff471', '#b8f3bd'],
			seriesDefaults:{
				renderer:$.jqplot.BarRenderer,
				rendererOptions: {
					barMargin: 0,
					shadow: false
				},
				pointLabels: { show: true, formatString: '%.1f', hideZeros: true, edgeTolerance: 0, stackedValue: false },
				shadow: true,
			},
			highlighter: {
				show: false,
				sizeAdjust: 0,
				showTooltip: true,
				tooltipAxes: 'y',
				formatString: '%.1f'
			},
			canvasOverlay: {
				show: true,
				objects: [
					{ dashedHorizontalLine: {
						name: 'barney',
						y: optimum,
						lineWidth: 1,
						color: 'rgb(0, 166, 81)',
						shadow: false
					}}
				]
			},
			grid: {
				drawGridLines: true,        // wether to draw lines across the grid or not.
				gridLineColor: '#cccccc',   // *Color of the grid lines.
				background: 'transparent',      // CSS color spec for background color of grid.
				borderColor: 'transparent',     // CSS color spec for border around grid.
				borderWidth: 0,           // pixel width of border around grid.
				shadow: false,               // draw a shadow for grid.
			},
			axes:{
				xaxis: {
					
					renderer: $.jqplot.CategoryAxisRenderer,
					ticks: ticks,
				},
				yaxis: {
					label: true,
					min: 0,
					padMin: 0.1,
					tickOptions: {
	
					},
				}
			},
		});
	}).error(function() {
		myAlert("Ошибка загрузки данных для графика из базы", "error");
		//$('#loading').hide();
	});
}

function redrawMonthTable() {
	var daysInMonth = moment().month(document.getElementById('monthSelect').value - 1).daysInMonth();
	$('#monthCompleteContainer div#monthSummary'+ selectedVariant).empty();
	//$('#monthCompleteContainer div#monthSummary'+ selectedVariant).append('<span class="periodTitle"></span>');
	$('#monthCompleteContainer div#monthSummary'+ selectedVariant).append('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="monthTable"></table>');
	var _table = $('div#monthSummary'+ selectedVariant + ' table.monthTable');
	
	$(_table).append('<tr class="monthValues"></tr>');
	$(_table).append('<tr class="graphTR"></tr>');
	$(_table).append('<tr class="dayNumbers"></tr>');
	
	// days sum
	$(_table).find('tr.monthValues').append('<td align="right" width="50"><strong>Сутки:</strong></td>');
	for (i = 1; i <= daysInMonth; i++) { $(_table).find('tr.monthValues').append('<td class="dayValue" id="day' + i + '">&nbsp;</td>'); }
	$(_table).find('tr.monthValues').append('<td>&nbsp;</td>');
	
	// graph
	$(_table).find('tr.graphTR').append('<td>&nbsp;</td><td class="graphTD" colspan="' + daysInMonth + '"><div class="graphWraper"><div id="monthGraph' + selectedVariant + '" class="monthGraph"></div></div></td><td>&nbsp;</td>');
	
	// day numbers
	$(_table).find('tr.dayNumbers').append('<td height="25">&nbsp;</td>');
	for (i = 1; i <= daysInMonth; i++) { $(_table).find('tr.dayNumbers').append('<td class="day" data-day="' + i + '">' + i + '</td>'); }
	$(_table).find('tr.dayNumbers').append('<td>&nbsp;</td>');
	
	$('#monthCompleteContainer div#monthSummary'+ selectedVariant).append('<p class="monthTotal">Всего за месяц:&nbsp;<img src="img/loading_small_3.gif" width="12" height="12"></p>');
	$('#monthCompleteContainer div#monthSummary'+ selectedVariant).append('<p><a class="toExcelLink" href="#">сохранить в Excel</a></p>');
}
function renewMonthSelect(year) {
	var year = document.getElementById('yearSelect').value;
	if (year < moment().format("YYYY")) maxMonth = 12;
		else maxMonth = moment().format("M");
	$('#monthSelect').empty();
	for (month = 1; month <= maxMonth; month++) $('#monthSelect').append('<option value=' + month + '>' + monthes[month - 1] + '</option>');
}
function convertToExcel() {
	

	window.location.href = url;
}