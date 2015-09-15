function summaryContent() {
	this.format = 'blocks';
	this.value = 0;
	this.title = '';
	this.units = '';
}

raute_1 = new summaryContent();
	raute_1.value = function() {
		return 3;
	};
	raute_1.title = plotVariants[0][0].title;
	raute_1.units = plotVariants[0][0].units;


//startDate = $('#setPlotOpts_1').find('input#dateBegin').val();
//		endDate = $('#setPlotOpts_1').find('input#dateEnd').val();
//		return getSummary(plotVariants[0][0], startDate, endDate);
		

function raute_2() {
	this.value = getSummary(plotVariants[0][3], startDate, endDate);
	this.title = plotVariants[0][3].title;
	this.units = plotVariants[0][3].units;
}
raute_2.prototype = new summaryContent;

function raute_3() {
	var cut_1 = getSummary(plotVariants[1][0], startDate, endDate);
	var cut_2 = getSummary(plotVariants[1][1], startDate, endDate);
	var cut_3 = getSummary(plotVariants[1][2], startDate, endDate);
	/*var wasEdited = '';
	if ( (cut_1.indexOf('*')>-1) || (cut_2.indexOf('*')>-1) || (cut_3.indexOf('*')>-1) ) wasEdited = '*';
	var cutterTotal = (	parseFloat(cut_1) +	parseFloat(cut_2) +	parseFloat(cut_3) ).toFixed(2);*/
		
	this.value = cutterTotal;
	this.title = plotVariants[0][0].title;
	this.units = plotVariants[0][0].units;
}
raute_3.prototype = new summaryContent;

function selectSummarySet() {
	$('#summaryCantainer').show();
	
	startDate = $('input#dateBegin').val();
	endDate = $('input#dateEnd').val();
	
	if (mach == 0 || mach == 1) { // лущение или ножницы
		summary = 
		[{ // объём поданного кряжа
			plot_variant: '0-1',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[0][1].title,
			units: plotVariants[0][1].units,
			add_to_deferred_index: 1
		},
		{ // объём карандаша
			plot_variant: '0-4',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[0][4].title,
			units: plotVariants[0][4].units
		},
		{ // выход с ножниц
			machine_id: '1',
			field_ids: '5,6,7',
			operation: 'sum',
			value_scale: 1000000000,
			date_begin: startDate,
			date_end: endDate,
			title: 'Выход с ножниц',
			units: plotVariants[1][1].units,
			add_to_deferred_index: 2
		},
		{ // % выхода/входа
			value_scale: 1000000000,
			title: '% выхода/входа',
			units: '%',
			deferred_here: true,
			deferred_operation: 'percent'
		},
		{ // средний диаметр чурака
			plot_variant: '0-5',
			date_begin: startDate,
			date_end: endDate,
			title: 'Средний диаметр чурака',
			units: plotVariants[0][5].units
		},
		{ // средний диаметр карандаша
			plot_variant: '0-6',
			date_begin: startDate,
			date_end: endDate,
			title: 'Средний диаметр карандаша',
			units: plotVariants[0][6].units
		},
		{ // количество чураков
			plot_variant: '0-7',
			date_begin: startDate,
			date_end: endDate,
			title: 'Количество чураков',
			units: plotVariants[0][7].units
		},
		{ // Чураков с пилы (на 10 мин раньше)
			plot_variant: '9-2',
			date_begin: moment(startDate, "DD.MM.YYYY HH:mm").subtract("minutes", 10).format("DD.MM.YYYY HH:mm"),
			date_end: moment(endDate, "DD.MM.YYYY HH:mm").subtract("minutes", 10).format("DD.MM.YYYY HH:mm"),
			title: 'Чураков с пилы (на 10 мин ранее)', // plotVariants[9][3].title,
			units: plotVariants[9][3].units,
		},
		{ // эффективное время
			machine_id: '0',
			field_ids: '0',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);
		drawSummaryListsByFormats(startDate, endDate);
		
	} else if (mach == 2) { // Котельная
		summary = 
		[{ // заданная температура
			machine_id: '2',
			field_ids: '0', // excluding time and id fields
			operation: 'avg',
			value_scale: plotVariants[2][0].valueScale,
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[2][0].title,
			units: plotVariants[2][0].units
		},
		{ // прямая температура
			plot_variant: '2-1',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[2][1].title,
			units: plotVariants[2][1].units
		},
		{ // обратная температура
			machine_id: '2',
			field_ids: '2', // excluding time and id fields
			operation: 'avg',
			value_scale: plotVariants[2][2].valueScale,
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[2][2].title,
			units: plotVariants[2][2].units
		},
		{ // мощность
			plot_variant: '2-3',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[2][3].title,
			units: plotVariants[2][3].units
		},
		{ // эффективное время
			machine_id: '2',
			field_ids: '0',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);

	} else if (mach == 3) { // Сушилка
		summary = 
		[{ // Средняя скорость подачи
			plot_variant: '3-1',
			date_begin: startDate,
			date_end: endDate,
			title: 'Средняя скорость подачи',
			units: plotVariants[3][1].units
		},
		{ // Средняя скорость движения листов
			plot_variant: '3-2',
			date_begin: startDate,
			date_end: endDate,
			title: 'Средняя скорость движения листов',
			units: plotVariants[3][2].units
		},
		{ // Подано сырого шпона
			plot_variant: '3-4',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'Подано сырого шпона',
			units: 'м<sup>3</sup>',
			add_to_deferred_index: 1
		},
		{ // Выход сухого шпона
			plot_variant: '3-5',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'Выход сухого шпона',
			units: 'м<sup>3</sup>',
			add_to_deferred_index: 2
		},
		{ // Из них коротких листов
			plot_variant: '3-7',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'Из них коротких листов',
			units: 'м<sup>3</sup>'
		},
		{ // Подано красного шпона
			plot_variant: '3-3',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'Подано красного шпона',
			units: 'м<sup>3</sup>'
		},
		{ // Отходы
			plot_variant: '3-8',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'из них объём отходов ≈',
			units: 'м<sup>3</sup>'
		},
		{ // % использования шпона
			value_scale: 1,
			title: '% использования шпона',
			units: '%',
			deferred_here: true,
			deferred_operation: 'percent'
		},
		{ // число длинных листов
			plot_variant: '3-10',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'число длинных листов',
			units: ' шт'
		},
		{ // число коротких листов
			plot_variant: '3-11',
			date_begin: startDate,
			date_end: endDate,
			operation: '',
			title: 'число коротких листов',
			units: ' шт'
		},
		{ // эффективное время
			machine_id: '3',
			field_ids: '0',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);

	} else if (mach == 4) { // Опиловка
		summary = 
		[{ // Объём опиленных листов
			plot_variant: '4-1',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[4][1].title,
			units: plotVariants[4][1].units
		},
		{ // Объём опиленных листов
			plot_variant: '4-2',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[4][2].title,
			units: plotVariants[4][2].units
		},
		{ // Объём опиленных листов
			plot_variant: '4-4',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[4][4].title,
			units: plotVariants[4][4].units
		},
		{ // Объём опиленных листов
			plot_variant: '4-5',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[4][5].title,
			units: plotVariants[4][1].units
		},
		{ // эффективное время
			machine_id: '4',
			field_ids: '5',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);
		drawLoppingFormats(startDate, endDate);
		
	} else if (mach == 5) { // Шлифовка
		summary = 
		[{ // Всего ошлифовано
			plot_variant: '5-7',
			date_begin: startDate,
			date_end: endDate,
			title: 'Всего ошлифовано',
			units: plotVariants[5][7].units
		},
		{ // Всего ошлифовано
			plot_variant: '5-4',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[5][4].title,
			units: plotVariants[5][4].units
		},
		{ // Количество поданных листов
			plot_variant: '5-5',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[5][5].title,
			units: plotVariants[5][5].units
		},
		{ // эффективное время
			machine_id: '5',
			field_ids: '4,5,6',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);
		
	} else if (mach == 6) { // Сращивание
		summary = 
		[{ // Суммарный объём
			value_scale: 1,
			title: 'Суммарный объём',
			units: 'м<sup>3</sup>',
			deferred_here: true,
			deferred_operation: 'sum'
		},
		{ // Объём (новая линия)
			plot_variant: '6-2',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][2].title,
			units: plotVariants[6][2].units,
			add_to_deferred_index: 1
		},
		{ // Объём с пресса 1
			plot_variant: '6-3',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][3].title,
			units: plotVariants[6][3].units
		},
		{ // Объём с пресса 2
			plot_variant: '6-4',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][4].title,
			units: plotVariants[6][4].units
		},
		{ // Объём с пресса 3
			plot_variant: '6-5',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][5].title,
			units: plotVariants[6][5].units
		},
		{ // Объём (старая линия)
			plot_variant: '6-13',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][13].title,
			units: plotVariants[6][13].units,
			add_to_deferred_index: 2
		},
		{ // Объём с пресса 1
			plot_variant: '6-14',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][14].title,
			units: plotVariants[6][14].units
		},
		{ // Объём с пресса 2
			plot_variant: '6-15',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][15].title,
			units: plotVariants[6][15].units
		},
		{ // Объём с пресса 3
			plot_variant: '6-16',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[6][16].title,
			units: plotVariants[6][16].units
		},
		{ // эффективное время (новая линия)
			machine_id: '6',
			field_ids: '4,5,6',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время (нов. линия)',
			units: '%',
		},
		{ // эффективное время (старая линия)
			machine_id: '8',
			field_ids: '4,5,6',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время (стар. линия)',
			units: '%',
		}];
		generateSummary(summary);
		
	} else if (mach == 7) { // распиловка
		summary = [{
			plot_variant: '7-0',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[7][0].title,
			units: plotVariants[7][0].units,
		},
		{ // эффективное время
			machine_id: '7',
			field_ids: '0',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}];
		generateSummary(summary);
		drawSummarySawFormats(startDate, endDate);
		
	} else if (mach == 8) { // пресс
		summary = [{ // объём
			plot_variant: '8-0',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[8][0].title,
			units: plotVariants[8][0].units,
		},
		{ // количество листов
			plot_variant: '8-1',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[8][1].title,
			units: plotVariants[8][1].units,
		},
		/*{ // эффективное время
			machine_id: '8',
			field_ids: '0',
			operation: 'active_time',
			value_scale: 1,
			date_begin: startDate,
			date_end: endDate,
			title: 'Эффективное время',
			units: '%',
		}*/];
		generateSummary(summary);
		drawPressFormats(startDate, endDate);
	}  else if (mach == 9) { // раскряжёвка
		summary = [{ // Количество стволов
			plot_variant: '9-0',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[9][0].title,
			units: plotVariants[9][0].units,
		},
		{ // Теоретически чураков
			plot_variant: '9-3',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[9][3].title,
			units: plotVariants[9][3].units,
		},
		{ // Количество чураков
			plot_variant: '9-2',
			date_begin: startDate,
			date_end: endDate,
			title: plotVariants[9][2].title,
			units: plotVariants[9][2].units,
		},];
		generateSummary(summary);
	}
}
function generateSummary(summary) {
	var get_array_all = [], get_array_inuse = [];
	$('#summaryValues').empty(); $('#summaryUnder').empty();
	for (var index in summary) {
		$('#summaryValues').append("<div class='summaryVal' id='sumVal_" + index + "'><p class='summaryHeader'>" + summary[index].title + "</p><p class='summaryValue'><span><img src='img/loading_small_3.gif' />&nbsp;</span>" + summary[index].units + "</p></div>");
		
		if ( summary[index].plot_variant || (summary[index].machine_id && summary[index].field_ids) ) { // no deferred call after
			get_array_all[index] = $.ajax({
				url: 'includes/monitoring/getData2.php',
				type: 'GET',
				async: true,
				dataType: 'json',
				data: {
					plot_variant: summary[index].plot_variant,
					machine_id: summary[index].machine_id,
					field_ids: summary[index].field_ids,
					operation: summary[index].operation,
					value_scale: summary[index].value_scale,
					//date_begin: moment(summary[index].date_begin, "DD.MM.YYYY HH:mm").format("X"),
					//date_end: moment(summary[index].date_end, "DD.MM.YYYY HH:mm").format("X"),
					date_begin: summary[index].date_begin,
					date_end: summary[index].date_end,
					callback_position: index,
					divide: 0,
					use_corrections: 1
				},
				success: function(data) {
					if ( data.wasCorrected ) wasCorrected = '*';
						else wasCorrected = '';
					$('div#sumVal_' + data.callback_position + ' p.summaryValue').children('span').html(data.payload + wasCorrected) + summary[index].units;
				},
				error: function() {
					myAlert("Ошибка загрузки сводки из базы", "error");
				}
			});
		} else if ( summary[index].deferred_here ) {
			var deferred_post_here = index; // position, where deferred result should be placed
		}
		if ( summary[index].add_to_deferred_index ) get_array_inuse.push( get_array_all[index] );

	}
	//console.log(get_array_all);
	//console.log(get_array_inuse);
	$.when.apply( null, get_array_inuse ).done(function(cb1, cb2) { // maximum 2 callbacks are in use
		//console.log(cb1[0].payload);
		//console.log(cb2[0].payload);
		if ( typeof(deferred_post_here) != 'undefined' ) { // if we know the position where deferred result append to
			if (summary[deferred_post_here].deferred_operation == 'sum') {
				value =  Math.round( (cb2[0].payload + cb1[0].payload) * 10 ) / 10;
			} else if (summary[deferred_post_here].deferred_operation == 'percent') {
				if ( cb1[0].payload * 100 > 0 ) {
					value = Math.min( Math.round(cb2[0].payload / cb1[0].payload * 100), 100);
				} else {
					value = 0;
				}
			}
			$('div#sumVal_' + deferred_post_here + ' p.summaryValue').children('span').html( value ) + summary[deferred_post_here].units;
		}
	});
}
function drawSummaryListsByFormats(startDate, endDate) {
	$('#summaryUnder').html('<div style="text-align: center; width: 50px; margin: 10px auto;"><img src="img/loading.gif" width="50" height="50" /></div>');
	$.ajax({
		url: 'includes/monitoring/getRauteCutterBins.php',
		type: 'GET',
		data: {
			start: startDate,
			end: endDate,
		},
		success: function(data) {
			$('#summaryUnder').html(data);
		},			
		error: function() {
			myAlert("Ошибка загрузки сводки по листам", "error");
			//$('#loading').hide();
		}
	});
}
function drawPressFormats(startDate, endDate) {
	$('#summaryUnder').html('<div style="text-align: center; width: 50px; margin: 10px auto;"><img src="img/loading.gif" width="50" height="50" /></div>');
	$.ajax({
		url: 'includes/monitoring/getPressFormats.php',
		type: 'GET',
		data: {
			start: startDate,
			end: endDate,
		},
		success: function(data) {
			$('#summaryUnder').html(data);
		},			
		error: function() {
			myAlert("Ошибка загрузки сводки по толщинам", "error");
		}
	});
}

function drawLoppingFormats(startDate, endDate) {
	$('#summaryUnder').html('<div style="text-align: center; width: 50px; margin: 10px auto;"><img src="img/loading.gif" width="50" height="50" /></div>');
	$.ajax({
		url: 'includes/monitoring/getLoppingFormats.php',
		type: 'GET',
		data: {
			start: startDate,
			end: endDate,
		},
		success: function(data) {
			$('#summaryUnder').html(data);
		},			
		error: function() {
			myAlert("Ошибка загрузки сводки по толщинам", "error");
		}
	});
}

function drawSummarySawFormats(startDate, endDate) {
	$('#summaryUnder').html('<div style="text-align: center; width: 50px; margin: 10px auto;"><img src="img/loading.gif" width="50" height="50" /></div>');
	$.ajax({
		url: 'includes/monitoring/getSawFormats.php',
		type: 'GET',
		dataType: "json",
		data: {
			start: startDate,
			end: endDate,
		},
		success: function(data) {
			$('#summaryUnder').empty();
			$('#summaryUnder').append('<p class="summaryListsHeader">Поступившие брёвна:</p>');
			if (data.result == 'alert') {
				$('#summaryUnder').append('<p>' + data.text + '</p>');
			} if (data.total > 0)	{
				
				$('#summaryUnder').append('<table width="400" border="0" cellspacing="0" cellpadding="3" class="summaryLists"></table>');
				if (data.tree49 > 0) $('#summaryUnder table').append('<tr><td>4900 мм — <strong>' + data.tree49 + '  шт.</strong></td></tr>');
				if (data.tree54 > 0) $('#summaryUnder table').append('<tr><td>5400 мм — <strong>' + data.tree54 + '  шт.</strong></td></tr>');
				if (data.tree66 > 0) $('#summaryUnder table').append('<tr><td>6600 мм — <strong>' + data.tree66 + '  шт.</strong></td></tr>');
				$('#summaryUnder table').append('<tr><td>Теоретически чураков 1.3 м — <strong>' + data.total + ' шт.</strong></td></tr>');
			} else {
				$('#summaryUnder table').append('<span class="summaryLists" style="margin-top: 3px;">За выбранный период брёвен не подавалось.</span>');
			}
		},
		error: function() {
			myAlert("Ошибка загрузки сводки по форматам", "error");
			//$('#loading').hide();
		}
	});
}