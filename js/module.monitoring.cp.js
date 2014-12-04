document.title = 'Контрольная панель мониторинга — Система статистики Хардвуд трейдинг';
n = 2;
$(document).ready(function() {
	var axisLabelsFormat = '%#d %b %H:%M';

	
	$('#containerLoadingBar').show();
    $.getJSON('includes/monitoring/getCpPresets.php', function(dataPreset) {
		$('#containerLoadingBar').hide();
		periodForTitle = Array();
		startDate = Array();
		endDate = Array();
		var now = new Date();
		// выбираем из базы виджеты и создаём их через шаблон
		for (vidgetData in dataPreset) {
			cpPlotVariant = plotVariants[dataPreset[vidgetData].machine][dataPreset[vidgetData].variant];
			switch (dataPreset[vidgetData].period) {
				case "8_hours":
					periodForTitle[vidgetData] = " за 8 часов";
					startDate[vidgetData] = moment().subtract('hours', 8).format("X");
					endDate[vidgetData] = moment().format("X");
				break;
				case "16_hours":
					periodForTitle[vidgetData] = " за 16 часов";
					startDate[vidgetData] = moment().subtract('hours', 16).format("X");
					endDate[vidgetData] = moment().format("X");
				break;
				case "1_day":
					periodForTitle[vidgetData] = " за сутки";
					startDate[vidgetData] = moment().subtract('days', 1).format("X");
					endDate[vidgetData] = moment().format("X");
				break;
				case "2_days":
					periodForTitle[vidgetData] = " за 2 суток";
					startDate[vidgetData] = moment().subtract('days', 2).format("X");
					endDate[vidgetData] = moment().format("X");
				break;
			}
			templateData = {
				vidget_id: 'vidget_' + vidgetData,
				database_id: dataPreset[vidgetData].database_id,
				order: cpPlotVariant.order,
				title: machineNames[dataPreset[vidgetData].machine] + periodForTitle[vidgetData],
				id: 'cpChart' + vidgetData,
				content: cpPlotVariant.title + ':',
				value: ''
			}
			
			$("#addNewVidget").before($("#vidgetTemplate").tmpl( templateData ));
		}
		
		// вставляем в виджеты графики
		for (vidgetData in dataPreset) {
			
			cpPlotVariant = plotVariants[dataPreset[vidgetData].machine][dataPreset[vidgetData].variant];
			// единицы измерения
			$('#vidget_' + vidgetData + '_value').html(cpPlotVariant.units);
			
			$('#vidget_' + vidgetData + '_value').prepend("<img src='img/loading_small_3.gif' />");
			// получаем суммарное значение
			$.ajax({
				type: 'GET',
				url: 'includes/monitoring/getData2.php', 
				data: {
					plot_variant: dataPreset[vidgetData].machine + '-' + dataPreset[vidgetData].variant,
					date_begin: startDate[vidgetData],
					date_end: endDate[vidgetData],
					callback_position: vidgetData,
					divide: 0,
					use_corrections: 1
				},
				dataType:"json",
				async: true
				}).done(function(data) {
					$('#vidget_' + data.callback_position + '_value img').remove();
					var oldContent = $('#vidget_' + data.callback_position + '_value').html();
					$('#vidget_' + data.callback_position + '_value').html(data.payload + ' ' + oldContent);
			});
			$.ajax({
				type: 'GET',
				url: 'includes/monitoring/getData2.php', 
				data: {
					plot_variant: dataPreset[vidgetData].machine + '-' + dataPreset[vidgetData].variant,
					date_begin: startDate[vidgetData],
					date_end: endDate[vidgetData],
					callback_position: vidgetData,
					divide: 1,
					mx: 0.25,
					use_corrections: 0
				},
				dataType:"json",
				async: true
				}).done(function(dataObject) {
				
				chartId = 'cpChart' + dataObject.callback_position;
				$('#' + chartId).empty();
				
				plot2 = $.jqplot(chartId, [dataObject.payload], {
					seriesColors: ['#00749F'],
					title: false,
					highlighter: {
						show: true,
						sizeAdjust: 7.5
					},
					axes:{
						xaxis:{
							renderer: $.jqplot.DateAxisRenderer,
							tickOptions:{
								showMark: true,
								showLabel: false,
								showGridline: true,
								show: true,
								useSeriesColor: true,
								formatString:'%#d&nbsp;%b&nbsp;%H:%M'
							},
							autoscale: true,
							//tickInterval: '30 second',
							min: moment(startDate[dataObject['callback_position']], "X").add(moment().zone() + 180, 'minutes').format("DD MMMM YYYY HH:mm"),
							max: moment(endDate[dataObject['callback_position']], "X").add(moment().zone() + 180, 'minutes').format("DD MMMM YYYY HH:mm"),
						},
						yaxis:{
						  tickOptions:{
							showMark: false,
							showLabel: false,
							},
							min :0
						},
					},
					series: [{
						lineWidth: 2,
						markerOptions: {style: 'circle', size: 4},
						rendererOptions: {
						smooth: true,
						animation: {
							show: true,
							speed: 1000
						}
					},
					}],
					cursor: {
						show: false,
						formatString: axisLabelsFormat
					},
					grid: {
						drawGridLines: true,
						gridLineColor: 'transparent',
						background: 'transparent',
						borderColor: 'transparent',
						borderWidth: 0,
						shadow: false,
					},
					canvasOverlay: {
            show: true,
            objects: [
                {horizontalLine: {
                    name: 'zero',
                    y: 0,
                    lineWidth: 1,
                    color: 'rgb(55, 100, 124)',
                    shadow: false
                }}
            ]
        }
				});
				n++;
				$('#vidget_' + dataObject['callback_position'] + ' .cpLoadingBar').hide();
			})
		}
	});
	// инициализируем sortable
	$( "#cpVidget_container" ).sortable({
            distance: 0,
			handle: ".cpVidgetMove",
			cancel: ".addNewVidget",
			update: updateOrder,
			start: function( event, ui ) { $(ui.item).css("border", "2px solid #ffffbe") },
			beforeStop: function( event, ui ) { $(ui.item).css("border", "1px solid #dbdce2") }
        });
    $( "#cpVidget_container" ).disableSelection();


	$('#editVidget_machine').chosen();
	$('#editVidget_variant').chosen();
	$('#editVidget_period').chosen();


	$('#editVidgetDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 310,
		height: 350,
		buttons: {
			"Сохранить": function() {
				$.get("includes/monitoring/editCpPresets.php", {
					database_id: document.getElementById('editVidget_database_id').value,
					machine: parseInt(document.getElementById('editVidget_machine').value),
					variant: document.getElementById('editVidget_variant').value,
					order: document.getElementById('editVidget_order').value,
					period: document.getElementById('editVidget_period').value,
				}, function(data) {
					window.location.reload(true);
				}, "json");
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
	$('#removeVidgetDialog').dialog({
		resizable: false,
            height: 140,
			autoOpen: false,
            modal: true,
            buttons: {
                "Удалить": function() {
                    $( this ).dialog( "close" );
					$.get("includes/monitoring/editCpPresets.php", {
						database_id: document.getElementById('editVidget_database_id').value,
						order: 'remove',
					}, function(data) {
						window.location.reload(true);
					}, "json");
                },
                "Отмена": function() {
                    $( this ).dialog( "close" );
                }
            }
        });





	// vidget settings: changing machine and field. uses plotVariants varible
	$('#editVidget_machine').change(function() {
		$('#editVidget_variant').empty();
		for (variant in plotVariants[this.value]) {
			var obj = plotVariants[this.value][variant];
			// summary для вариантов построения с операцией avg_percent считаются некорректно
			if (obj.operation != 'avg_percent') $('#editVidget_variant').append("<option value='" + variant + "'>" + obj.title + "</option>");
		}
		$('#editVidget_variant').trigger("liszt:updated");
	});
	$('#editVidget_machine').trigger("change"); // simulating change on init
	
	
	
	// edit vidget button click
	$('#cpVidgetSettings').live("click", function() {
		
	});
});

function updateOrder() { // запись в базу порядка виджетов
	var sorted = $( "#cpVidget_container" ).sortable( "serialize", { key: "vidget" } );
	sorted = sorted.replace(/&vidget=/g, "-");
	sorted = sorted.replace(/vidget=/, "");
	$.get("includes/monitoring/updateCpOrder.php", { order: sorted });
}

function editWidget(widgetId) {
	$('#removeWidget').show(); // displaying "delete" button for EditWidget dialog
	$('#editDialogLoadingBar').show();
	$.getJSON("includes/monitoring/getCpPresetInfo.php",
	  {"database_id": widgetId},
	function(data) {
		//alert(parseInt(data['variant']));
		var machine = parseInt(data['machine']);
		document.getElementById('editVidget_machine').value = machine;
		
		$('#editVidget_variant').empty();
		for (variant in plotVariants[machine]) {
			var obj = plotVariants[machine][variant];
			// summary для вариантов построения с операцией avg_percent считаются некорректно
			if (obj.operation != 'avg_percent') $('#editVidget_variant').append("<option value='" + variant + "'>" + obj.title + "</option>");
		}
		document.getElementById('editVidget_variant').value = parseInt(data['variant']);
		document.getElementById('editVidget_period').value = data['period'];
		document.getElementById('editVidget_order').value = parseInt(data['order']);
		document.getElementById('editVidget_database_id').value = widgetId;
		$('#editVidget_machine').trigger("liszt:updated");
		$('#editVidget_variant').trigger("liszt:updated");
		$('#editVidget_period').trigger("liszt:updated");
		$('#editDialogLoadingBar').hide();
	});
	$('#editVidgetDialog').dialog('open');
}
function addWidget() {
	$('#editDialogLoadingBar').hide();
	var machine = 0;
	document.getElementById('editVidget_machine').value = 0;
	$('#editVidget_variant').empty();
	$('#removeWidget').hide(); // hiding "delete" button for NewWidget dialog
	for (variant in plotVariants[machine]) {
		var obj = plotVariants[machine][variant];
		// summary для вариантов построения с операцией avg_percent считаются некорректно
		if (obj.operation != 'avg_percent') $('#editVidget_variant').append("<option value='" + variant + "'>" + obj.title + "</option>");
	}
	document.getElementById('editVidget_variant').value = 0;
	document.getElementById('editVidget_period').value = '8_hours';
	document.getElementById('editVidget_order').value = 'last';
	document.getElementById('editVidget_database_id').value = 'new';
	$('#editVidgetDialog').dialog('open');
}
function removeWidget(widgetId) {
	$('#editDialogLoadingBar').hide();
	$('#removeVidgetDialog').dialog('open');
}