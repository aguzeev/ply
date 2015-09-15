// © Александр Гузеев (Alexander Guzeev), 2012
function parseDate(inputDate) {
	var parsedDate = new Date();
	var parts = new Array();
	parts = inputDate.split(' ');
	inputDate = parts[0].split('.');
	if (parts[1]) {
		inputTime = parts[1].split(':');
	} else {
		inputTime[0] = '00';
		inputTime[1] = '00';
	}
	
	parsedDate.setFullYear(inputDate[2], inputDate[1] - 1, inputDate[0]);
	parsedDate.setHours(inputTime[0], inputTime[1]);
	
	return parsedDate
}

function getUtcDate(inputDate) {
	var parts = new Array();
	parts = inputDate.split(' ');
	inputDate = parts[0].split('.');
	if (parts[1]) {
		inputTime = parts[1].split(':');
	} else {
		inputTime[0] = '00';
		inputTime[1] = '00';
	}
	
	var day = inputDate[0];
	var month = inputDate[1];
	var year = inputDate[2];
	var hour = inputTime[0];
	var minute = inputTime[1];
	
	myDateUTC = Date.parse(year + '-' + month + '-' + day + 'T' + hour + ':' + minute) / 1000;
	//var myDateUTC = new Date(myDateUTC)
	return myDateUTC;
}

var onlinePlot;
function stopOnline() {
	clearTimeout(onlinePlot);
	$('#stopOnlineButton').hide();
	$('#startPlotButton').show();
	$('#onlineBox').removeAttr('checked');
}

function plotIt2() {
	dateBeginField = $('input#dateBegin');
	dateEndField = $('input#dateEnd');
	dateBegin = $(dateBeginField).val(); //начало периода
	dateEnd = $(dateEndField).val();
	if (moment(dateEnd, "DD.MM.YYYY hh:mm").format("X") - moment(dateBegin, "DD.MM.YYYY hh:mm").format("X") > 60 * 60 * 24 * 60) {
		$( "#tooLongPeriod" ).dialog( "open" );
		return
	}
	
	if (onlineSet) {
		onlinePlot = setTimeout( plotIt2, 600000 );
		$('#stopOnlineButton').show();
		$('#startPlotButton').hide();
	}
	
	
	// check fields
	if ( $(dateBeginField).val()=='' ) { markElements( [dateBeginField] ); return; }
	if ( $(dateEndField).val()=='' ) { markElements( [dateEndField] ); return; }
	if ( parseDate( $(dateBeginField).val() ) >= parseDate( $(dateEndField).val() ) ) {
		markElements( [dateBeginField, dateEndField] );	return;
	}
	
	var mach = $('#plotMachine').val();
	var fields = $('#plotField').val();
	
	if (fields == null) {
		alert('Выберите параметр работы');
		return;
	}
	
	$('#chart1').html('<div style="text-align: center; vertical-align: middle; padding: 80px 0 30px 0;"><img src="img/loading.gif" width="50" height="50" /></div>');
	selectSummarySet();
	
	var ajax_array = Array(), plot_variant_js = Array();
	for (var index in fields) {
		var plot_variant = mach + '-' + fields[index];
		
		var pv = plotVariants[ plot_variant.split('-')[0] ][ plot_variant.split('-')[1] ];
		if ( typeof(pv.maxLength) != 'undefined' ) {
			if ( moment(dateEnd, "DD.MM.YYYY HH:mm").format("X") - moment(dateBegin, "DD.MM.YYYY HH:mm").format("X") > pv.maxLength ) {
				alert("Слишком большой промежуток времени для построения выбранного графика. Пожалуйста, выберите период меньше " + pv.maxLength / 60 / 60 + ' часов.');
				$('#chart1').empty();
				return false;
			}
		}

		ajax_array[index] = $.ajax({
				url: 'includes/monitoring/getData2.php',
				data: {
					plot_variant: plot_variant,
					mx: $('#mx').attr('value'),
					//date_begin: moment(dateBegin, "DD.MM.YYYY HH:mm").format("X"),
					//date_end: moment(dateEnd, "DD.MM.YYYY HH:mm").format("X"),
					date_begin: dateBegin,
					date_end: dateEnd,
					divide: 1,
					use_corrections: 0
				},
				dataType: "json",
			});
			
			plot_variant_js.push( plotVariants[ plot_variant.split('-')[0] ][ plot_variant.split('-')[1] ] );
	}
	
	$.when.apply( $, ajax_array ).done(function(cb1, cb2) { // maximum 2 callbacks are in use
		if ( $('#profiling').attr('checked') ) {
			$('#profilingResult').empty();
			//$('#profilingResult').append('<hr /><p>' + cb1[0].payload + '</p><p>' + cb2[0].payload + '</p><hr />');
		} else {
			$('#profilingResult').empty();
		}
		
		if (cb2 == 'success') {// this means cb2 is empty
			plotData = [cb1.payload];
			maximum = cb1.maximum;
		} else {
			plotData = [cb1[0].payload, cb2[0].payload];
			maximum = Math.max( cb1[0].maximum, cb2[0].maximum );
		}
		
		grawGraph(plotData, plot_variant_js, dateBegin, dateEnd, maximum);
	});
}

function grawGraph(data, plot_variant, dateBegin, dateEnd, yAxisMaximum) {
	var commonTitle = '', yaxis = {}, y2axis = {}, series = Array();
	var axisLabelsFormat = '%#d %b %H:%M';
	var legend = { show: false }
	var axisNames = ['yaxis', 'y2axis'];
	
	// one or several fields processing
	for (var i in plot_variant) {
		// общий заголовок
		if ( i == 0 ) commonTitle = plot_variant[i].title;
		else commonTitle += ', ' + plot_variant[i].title;
		
		// название оси
		if ( i == 0 ) yaxesLabel = plot_variant[i].units;
		else yaxesLabel += ', ' + plot_variant[i].units;
		
		// ось Y
		if ( i == 0 ) yaxis = {	label: plot_variant[i].units, min: plot_variant[i].yAxisMin, tickOptions: { formatString: plot_variant[i].yAxisFormat } };
		if ( i == 1 ) y2axis = { label: plot_variant[i].units, min: plot_variant[i].yAxisMin, tickOptions: { formatString: plot_variant[i].yAxisFormat } };
		
		
		if ( yAxisMaximum == 0 ) yAxisMaximum = 1;
		yaxis.max = yAxisMaximum * 1.2;
		
		
		series.push({
			lineWidth: 2,
			showLine: plot_variant[i].showLine,
			markerOptions: { style: 'circle', size: 4 },
			rendererOptions: { smooth: true },
			yaxis: axisNames[i],
		});
	}
	
	// multi-line plot processing
	if ( plot_variant.length > 1 ) {
		// y-axis maximum determining
		if ( yaxis.label == y2axis.label && yaxis.min == y2axis.min ) { yaxis.max = yAxisMaximum * 1.2, y2axis.max = yAxisMaximum * 1.2; }
		
		legend = { show: true, placement: 'inside', location: 'se', labels: [plot_variant[0].title, plot_variant[1].title] }
	}
	
	$('#chart1').empty();
	var plot1 = $.jqplot('chart1', data, {
    	title: commonTitle,
		highlighter: {
			show: true,
			sizeAdjust: 2,
			showTooltip: true,
			tooltipAxes: 'both',
		},
		legend: legend,
    	axes:{
			xaxis:{
				renderer: $.jqplot.DateAxisRenderer,
				tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				tickOptions: {
					formatString: axisLabelsFormat,
					angle: -30
				},
				label: 'Время',
				autoscale: true,
				//tickInterval: '30 second',
				min: moment(dateBegin, "DD.MM.YYYY HH:mm").format("DD MMMM YYYY HH:mm"),
				max: moment(dateEnd, "DD.MM.YYYY HH:mm").format("DD MMMM YYYY HH:mm"),
			},
			xaxis:{
				renderer: $.jqplot.DateAxisRenderer,
				tickRenderer: $.jqplot.CanvasAxisTickRenderer,
				tickOptions: {
					formatString: axisLabelsFormat,
					angle: -30
				},
				label: 'Время',
				autoscale: true,
				//tickInterval: '30 second',
				min: moment(dateBegin, "DD.MM.YYYY HH:mm").format("DD MMMM YYYY HH:mm"),
				max: moment(dateEnd, "DD.MM.YYYY HH:mm").format("DD MMMM YYYY HH:mm"),
			},
			yaxis: yaxis,
			y2axis: y2axis,
		},
    	series: series,
	});
}