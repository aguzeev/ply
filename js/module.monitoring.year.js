document.title = 'Мониторинг за год — система статистики Хардвуд трейдинг';
var yearToPlot;
var yearVariants = [
	['Лущение', '1-4'],
	['Сушка', '3-4'],
	['Опиловка', '4-1'],
	['Шлифовка', '5-7'],
	['Сращивание (новое)', '6-2'],
	['Сращивание (старое)', '6-13'],
	['Пресс', '8-0'],
];
var monthes = Array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');

$(document).ready(function() {
	// generate year select
	for (year = 2013; year <= moment().format("YYYY"); year++) $('#yearSelect').append('<option value="' + year + '">' + year + '</option>');
	
	//generate DIVs for each machine graph
	for (i = 0; i < yearVariants.length; i++) { $('#yearCompleteContainer').append('<div id="yearSummary' + i + '" class="yearSummary"></div>'); }
	
	// event handlers
	$('#yearSelect').on("change", function() {
		yearToPlot = document.getElementById('yearSelect').value;
	});
	
	// set default values
	document.getElementById('yearSelect').value = moment().format("YYYY");
});

function plotYearGraph(year) {
	$('#yearCompleteContainer').empty();
	
	// generate the month's lengthes array
	var daysPerMonth = Array();
	daysPerMonth.push( moment().year(year).month(0).daysInMonth() );
	for (m = 1; m <= 11; m++) {	daysPerMonth.push( daysPerMonth[m-1] + moment().year(year).month(m).daysInMonth() );	}
			
	for (var selectedVariant in yearVariants) {
		//var target = 'yearGraph' + selectedVariant;
		$('#yearCompleteContainer').append("<table width='80%' align='center' border='0' id='yearTableMachine" + selectedVariant +
		"' cellspacing='10' cellspadding='5'><tr><td valign='middle' align='right' width='100'><strong>" + yearVariants[selectedVariant][0] +
		"</strong></td><td><div class='yearGraph' id='yearGraph" + selectedVariant + 
		"'><img src='img/loading.gif' style='margin: 30px 0 0 0;' /></div></td><td width='150' class='yearMachineSummary'>&nbsp;</td></tr></table>");
			
		$.ajax({
		url: 'includes/monitoring/getData2.php',
		data: {
			plot_variant: yearVariants[selectedVariant][1],
			date_begin: moment().year(year).startOf('year').hour(8).format("X"),
			date_end: moment().year( parseInt(year) + 1).startOf('year').hour(7).minute(59).second(59).format("X"),
			divide: 1,
			//interval: 60 * 60 * 24,
			use_corrections: 1,
			callback_position: selectedVariant
		},
		dataType: "json"
		}).done(function(data) {	
		//console.log(data.payload);	
			var monthValues = Array(), currMonth = 0, monthAcc = 0, yearAcc = 0, machineMax = 0, machineMin = 0, activeMonthCount = 0;
			
			for (var i in data.payload) {
				monthAcc = monthAcc + data.payload[i][1];
				yearAcc = yearAcc + data.payload[i][1];
				
				if (i > daysPerMonth[currMonth] - 2 || i == data.payload.length - 1) {
					monthValues[currMonth] = monthAcc;
					if (monthAcc > machineMax) machineMax = monthAcc;
					if ( (monthAcc < machineMin && monthAcc > 0) || machineMin == 0) machineMin = monthAcc;
					if ( monthAcc > 0 ) activeMonthCount++; // for correct AVG count
					monthAcc = 0;
					currMonth++;
				}
			}
			
			// Year summary for machine
			var machineAvg;
			activeMonthCount > 0 ? machineAvg = Math.floor(yearAcc * 100 / activeMonthCount) / 100 : machineAvg = 0;
			$('table#yearTableMachine' + data.callback_position + ' .yearMachineSummary').append('<table width="100%" border="0" cellspacing="3"><tr><td>Всего:</td><td align="right"><strong>' + Math.floor(yearAcc * 100) / 100 +
			'</strong></td></tr><tr><td>Max:</td><td align="right"><strong>' + Math.floor(machineMax * 100) / 100 +
			'</strong></td></tr><tr><td>Min:</td><td align="right"><strong>' + Math.floor(machineMin * 100) / 100 +
			'</strong></td></tr><tr><td>В среднем<br>за месяц:</td><td align="right"><strong>' + machineAvg +
			'</strong></td></tr></table>');
			//console.log(monthValues);
			
			$('#yearGraph' + data.callback_position).empty();
			var plot1 = $.jqplot('yearGraph' + data.callback_position, [monthValues], {
				stackSeries: false,
				//seriesColors:['#a0deea', '#fff471', '#b8f3bd'],
				seriesDefaults:{
					renderer:$.jqplot.BarRenderer,
					rendererOptions: {
						barMargin: 10,
						shadow: true
					},
					pointLabels: { show: true, formatString: '%.1f', hideZeros: true, edgeTolerance: -5 },
					shadow: true,
				},
				highlighter: {
					show: false,
					sizeAdjust: 0,
					showTooltip: true,
					tooltipAxes: 'y',
					formatString: '%.1f'
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
						ticks: monthes,
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
			myAlert("Ошибка загрузки данных для графика из базы");
			//$('#loading').hide();
		});
	}
}