document.title = 'Редактирование перечня должностей — Система статистики Хардвуд трейдинг';


$(document).ready(function() {
	$('#editAppointmentDialog_op').chosen();
	$('#addAppointmentDialog_op').chosen();
	$('#editAppointmentDialog_dept').chosen();
	$('#addAppointmentDialog_dept').chosen();
	$('.selectOp').chosen();
	
	$('#editAppointmentDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 400,
		buttons: {
			"Сохранить": function() {
				$.get("includes/editAppointment.php", {
					appId: document.getElementById('editAppointmentDialog_id').value,
					appointment: document.getElementById('editAppointmentDialog_app').value,
					department: document.getElementById('editAppointmentDialog_dept').value,
					allowedOpDef: $('#editAppointmentDialog_op').val(),
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
	$('#removeAppointmentDialog').dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Удалить": function() {
				$.get("includes/editAppointment.php", {
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
	
	$('#addAppointmentDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 350,
		buttons: {
			"Добавить должность": function() {
				$.get("includes/editAppointment.php", {
					appId: 'new',
					appointment: document.getElementById('addAppointmentDialog_app').value,
					department: document.getElementById('addAppointmentDialog_dept').value,
					allowedOpDef: $('#addAppointmentDialog_op').val()
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


function editAppointment(appId) {
	$('#editAppointmentDialog').dialog('open');
	document.getElementById('editAppointmentDialog_id').value = appId;
	$.getJSON("includes/getAppointmentInfo.php",
	  {"appId": appId},
	function(data) {
		// data[0] — название дожности, data[1][x] — id и название подразделения, data[2] — массив допустимых операций
		document.getElementById('editAppointmentDialog_app').value = data[0];
		
		$('#editAppointmentDialog_dept option[value=' + data[1][0] + ']').attr('selected', 'yes').trigger('liszt:updated');
		
		// сначала снимаем выбор с тех пунктов, которые могут быть выбраны ранее
		$('#editAppointmentDialog_op option').removeAttr('selected').trigger('liszt:updated');
		// а теперь отмечаем только нужные
		for (var key in data[2]) {
			var sel = data[2][key];
			$('#editAppointmentDialog_op option[value=' + sel + ']').attr('selected', 'yes').trigger('liszt:updated');
		}
	})
}
function addAppointment() {
	$('#addAppointmentDialog').dialog('open');
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