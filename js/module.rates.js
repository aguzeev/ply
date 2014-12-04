document.title = 'Редактирование тарифов — Система статистики Хардвуд трейдинг';



$(document).ready(function() {
	
	$('#workersTable tr').mouseover(function() {
		$('#editBar_' + this.id).css('visibility', 'visible');
	});
	$('#workersTable tr').mouseout(function() {
		$('#editBar_' + this.id).css('visibility', 'hidden');
	});
	
	$('#ratesDialog_op').chosen();
	$('#ratesDialog_app').chosen();
	$('#ratesDialog_date_start').datepicker({ minDate: 1 }); // можно устанавливать только начиная с завтрашнего дня
	
	// устанавливаем выбранными пункты фильтра, полученные из GET
	if (typeof GETvalues['operations'] != "undefined") {
		operations = GETvalues['operations'].split(',');
		for (var key in operations) {
			$('#timeBoarNavOperation option[value=' + operations[key] + ']').attr("selected", "yes");
		}
		$('#timeBoarNavOperation').trigger('liszt:updated');
	}
	
	
	$('#ratesDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 430,
		height: 500,
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
				if (checkForm() ) {
					$.get("includes/editRate.php", {
						act: 'edit',
						rateId: document.getElementById('ratesDialog_id').value,
						op: document.getElementById('ratesDialog_op').value,
						app: document.getElementById('ratesDialog_app').value,
						dateStart: strToMYSQLDate(document.getElementById('ratesDialog_date_start').value),
						rate1: document.getElementById('ratesDialog_rate1').value,
						rate2: document.getElementById('ratesDialog_rate2').value,
						cond2: document.getElementById('ratesDialog_cond2').value,
						rate3: document.getElementById('ratesDialog_rate3').value,
						cond3: document.getElementById('ratesDialog_cond3').value
					}, function(data) {
						location.reload(true);
					});
				}
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	
	$('#deleteRateDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons: {
			"Удалить": function() {
				$.get("includes/editRate.php", {
					act: 'remove',
					rateId: document.getElementById('deleteRatesDialog_id').value
				}, function(data) {
					location.reload(true);
				});
			},
			"Отмена": function(data) {
				//alert(data);
				$( this ).dialog( "close" );
			}
		}
	});
	
	
	$('#timeBoarNavOperation').chosen();
	$('.editTimeboardCellDialogInput').chosen();


	
});



function applyFilter() {
	var operations = $('#timeBoarNavOperation').val();
	newUrl = window.location.href;
	
	if (operations != null) {
		if (typeof GETvalues['operations'] == "undefined") newUrl = newUrl + '&operations=' + operations;
			else newUrl = newUrl.replace(GETvalues['operations'], operations);
			
		window.location.href = newUrl;
	} else {
		//alert('Сначала выберите необходимые операции.');
		clearFilter();
	}
	
}
function clearFilter() {
	newUrl = window.location.href.replace('&operations=' + GETvalues['operations'], '');
	window.location.href = newUrl;
}



function editRate(rateId) {
	$('#ratesDialog').dialog('open');
	document.getElementById('ratesDialog_id').value = rateId;
	
	$.getJSON("includes/getRateInfo.php",
	  {"rateId": rateId},
	function(data) {
		$('#ratesDialog_op option[value=' + data['op'] + ']').attr("selected", "yes").trigger('liszt:updated');
		renewAppList(data['app']);
		document.getElementById('ratesDialog_date_start').value = data['start_date'];
		document.getElementById('ratesDialog_rate1').value = data['rate_1'];
		document.getElementById('ratesDialog_rate2').value = data['rate_2'];
		document.getElementById('ratesDialog_cond2').value = data['cond_2'];
		document.getElementById('ratesDialog_rate3').value = data['rate_3'];
		document.getElementById('ratesDialog_cond3').value = data['cond_3'];
	});
	$('#ratesDialog_op').on("change", function() {
		renewAppList();
	});
}

function renewAppList(active) { // в active — id должности, которую нужно сделать активной
	$.getJSON("includes/getAppointmentsByOp.php",
	{"opId": $('#ratesDialog_op').val()},
	function(data) {
		$('#ratesDialog_app').empty();
		if (data) {
			for (var key in data) {
				$('#ratesDialog_app').append( $('<option value="' + data[key][0] + '">' + data[key][1] + '</option>'));
			}
		} else { // Нет подходящих должностей
		}
		$('#ratesDialog_app option[value=' + active + ']').attr("selected", "yes"); // установка активного пункта
		$('#ratesDialog_app').trigger('liszt:updated');
	});
}
function addRate() {
	$('#ratesDialog').dialog('open');
	document.getElementById('ratesDialog_id').value = "new";
	
	var now = new Date;
	var nextMonth_1st = new Date;
	
	if (now.getMonth() < 11) {
		nextMonth_1st.setMonth(now.getMonth() + 1, 1);
	} else  {
		nextMonth.setMonth(0, 1);
		nextMonth.setYear(now.getYear() + 1);
	}

	document.getElementById('ratesDialog_date_start').value = dateFormatting(nextMonth_1st);
	document.getElementById('ratesDialog_rate1').value = "";
	document.getElementById('ratesDialog_rate2').value = "";
	document.getElementById('ratesDialog_cond2').value = "";
	document.getElementById('ratesDialog_rate3').value = "";
	document.getElementById('ratesDialog_cond3').value = "";

	$('#ratesDialog_op').on("change", function() {
		$.getJSON("includes/getAppointmentsByOp.php",
		  {"opId": $(this).val()},
		function(data) {
			$('#ratesDialog_app').empty();
			if (data) {
				for (var key in data) {
					$('#ratesDialog_app').append( $('<option value="' + data[key][0] + '">' + data[key][1] + '</option>'));
				}
			} else { // Нет подходящих должностей
			}
			$('#ratesDialog_app').trigger('liszt:updated');
		});
	});
}

function deleteRate(rateId) {
	$('#deleteRateDialog').dialog('open');
	document.getElementById('deleteRatesDialog_id').value = rateId;
	
}

function checkForm() { // проверка заполненности полей
	var elWithError = Array();
	var check = true;
	
	fields = ["ratesDialog_op", "ratesDialog_app", "ratesDialog_app", "ratesDialog_date_start", "ratesDialog_rate1"];
	
	// перекрёстные сравнения
	if ($('#ratesDialog_cond2').val() != "") fields.push("ratesDialog_rate2");
	if ($('#ratesDialog_cond3').val() != "") fields.push("ratesDialog_rate3");
	if ($('#ratesDialog_rate2').val() != "") fields.push("ratesDialog_cond2");
	if ($('#ratesDialog_rate3').val() != "") fields.push("ratesDialog_cond3");
	
	for (key in fields) {
		if ($('#' + fields[key]).val() == "" || $('#' + fields[key]).val() == null) {
			if ( $('#' + fields[key]).hasClass("chzn-done") ) elWithError.push( fields[key] + '_chzn span' );
				else elWithError.push( fields[key] );
			check = false;
		}
	}
	markElements(elWithError);
	if (check) return true;
	else return false;
}