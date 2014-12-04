document.title = 'Редактирование перечня должностей — Система статистики Хардвуд трейдинг';


$(document).ready(function() {
	$('#ratesDialog_sector').chosen().change( function() { renewApps(); });
	$('#ratesDialog_app').chosen();
	$('#ratesDialog_date_start').datepicker();
	
	$('#ratesDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 400,
		buttons: {
			"Сохранить": function() {
				$.get("includes/editFixedprice.php", {
					act: "edit-or-add",
					fixedpriceId: document.getElementById('ratesDialog_id').value,
					app: document.getElementById('ratesDialog_app').value,
					price: $('#ratesDialog_price').val(),
					dateStart: strToMYSQLDate($('#ratesDialog_date_start').val())
				}, function(data) {
					window.location.reload(true);
					if (data.result == "error") { alert(data.text); }
				}, "json");
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$('#deleteRateDialog').dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Удалить": function() {
				$.get("includes/editFixedprice.php", {
					act: "remove",
					appId: document.getElementById('removeAppointmentDialog_id').value,
				}, function(data) {
					window.location.reload(true);
					if (data.result == "error") { alert(data.text); }
				}, "json");
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
})


function editRate(rateId) {
	$('#ratesDialog').dialog('open');
	
	$.getJSON("includes/getFixedPriceInfo.php",
	  {"fixedpriceId": rateId},
	function(data) {
		$('#ratesDialog_id').val(data['id']);
		// сначала снимаем выбор с тех пунктов, которые могут быть выбраны ранее
		//$('#ratesDialog_sector option').removeAttr('selected').trigger('liszt:updated');
		$('#ratesDialog_sector option[value=' + data['sector_id'] + ']').attr('selected', 'yes').trigger('liszt:updated');
		renewApps(data['app_id']);
		$('#ratesDialog_price').val(data['price']);
		$('#ratesDialog_date_start').val(data['date_start']);
		//установку активной должности смотри в функции renewApps()
	})
}
function addAppointment() {
	$('#ratesDialog').dialog('open');
	$('#ratesDialog_id').val('new');
}
function removeAppointment(appId) {
	$('#removeAppointmentDialog').dialog('open');
	document.getElementById('removeAppointmentDialog_id').value = appId;
	$.getJSON("includes/getAppointmentInfo.php",
	  {"appId": appId},
	function(data) {
		$('#removeAppointmentDialog span').html(data[0]);
	})
}

function renewApps(active_app) {
	dept = ($("#ratesDialog_sector").val() != null) ? $("#ratesDialog_sector").val(): 0;
	$.getJSON("includes/getAppointmentsInDept.php",
	{"dept": dept.toString()},
	function(data) {
		$( "#ratesDialog_app" ).empty();
		$( "#ratesTemplate" ).tmpl( data ).appendTo( "#ratesDialog_app" );
		$( "#ratesDialog_app" ).trigger('liszt:updated');
		
		// установка активной должности
		if (typeof(active_app) != "undefined") $('#ratesDialog_app option[value=' + active_app + ']').attr('selected', 'yes').trigger('liszt:updated');
	});
}
