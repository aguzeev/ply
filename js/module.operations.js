document.title = 'Редактирование перечня операций — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	$('#editOperationDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		buttons: {
			"Сохранить": function() {
				$.get("includes/editOperation.php", {
					operationId: document.getElementById('editOperationDialog_id').value,
					operation: document.getElementById('editOperationDialog_op').value,
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
	$('#removeOperationDialog').dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Удалить": function() {
				$.get("includes/editOperation.php", {
					act: "remove",
					operationId: document.getElementById('removeOperationDialog_id').value,
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
	
	$('#addOperationDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 400,
		buttons: {
			"Добавить операцию": function() {
				$.get("includes/editOperation.php", {
					operationId: 'new',
					operation: document.getElementById('addOperationDialog_op').value,
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


function editOperation(operationId) {
	$('#editOperationDialog').dialog('open');
	document.getElementById('editOperationDialog_id').value = operationId;
	$.getJSON("includes/getOperationInfo.php",
	  {"operationId": operationId},
	function(data) {
		document.getElementById('editOperationDialog_op').value = data[0];
	})
}
function addOperation() {
	$('#addOperationDialog').dialog('open');
}
function removeOperation(operationId) {
	$('#removeOperationDialog').dialog('open');
	document.getElementById('removeOperationDialog_id').value = operationId;
	$.getJSON("includes/getOperationInfo.php",
	  {"operationId": operationId},
	function(data) {
		$('#removeOperationDialog span').html(data[0]);
	})
}