document.title = 'Редактирование пользователей — Система статистики Хардвуд трейдинг';

$(document).ready(function() {
	$('#addUserDialog_hasAccess').chosen();
	$('#editUserDialog_hasAccess').chosen();
	$('#addUserDialog_url').chosen();
	$('#editUserDialog_url').chosen();
	$('#editUserDialog_group').chosen();
	$('#addUserDialog_group').chosen();
	
	$('#usersTable tr').mouseover(function() {
		$('#editBar_' + this.id).css('visibility', 'visible');
	});
	$('#usersTable tr').mouseout(function() {
		$('#editBar_' + this.id).css('visibility', 'hidden');
	})
	
	// Обработчик нажатий на кнопки выбора прав доступа
	$('td.permitionSelect span').on("click", function() {
		action = $(this).attr("data-action");
		module = $(this).parents('td.permitionSelect').attr("data-module");
		
		// перекрашиваем все кнопки для данного модуля в исходный цвет
		$('td.permitionSelect[data-module=' + module + '] span').css("background-image", "url(img/perm-default.png)");
		
		if (action == "no") {
			$(this).css("background-image", "url(img/perm-no.png)");
			$('input[data-module=' + module + ']').val(0);
		} else if (action == "read") {
			$(this).css("background-image", "url(img/perm-read.png)");
			$('input[data-module=' + module + ']').val(1);
		} else if (action == "write") {
			$(this).css("background-image", "url(img/perm-write.png)");
			$('input[data-module=' + module + ']').val(2);
		}
	});
	$('td.permitionSelect input[data-func=permition]').on("change", function() {
		action = $(this).val();
		module = $(this).attr("data-module");
		//alert('action = ' + action + ', module = ' + module);
		
		obj = $('td.permitionSelect[data-module=' + module + '] ');
		
		// перекрашиваем все кнопки для данного модуля в исходный цвет
		$(obj).find('span[data-action]').css("background-image", "url(img/perm-default.png)");
		
		if (action == 0) {
			$(obj).find('span[data-action=no]').css("background-image", "url(img/perm-no.png)");
		} else if (action == 1) {
			$(obj).find('span[data-action=read]').css("background-image", "url(img/perm-read.png)");
		} else if (action == 2) {
			$(obj).find('span[data-action=write]').css("background-image", "url(img/perm-write.png)");
		}
	});
	
	$('#editUserDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 600,
		buttons: {
			"Сохранить": function() {
				
				permitions = {};
				$('.editUserPermitions input').each(function(index, element) {
					num = $(element).attr("data-module");
					if (element.value == "") permitions[num] = 0;
						else permitions[num] = element.value;
                });
				$.get("includes/editUser.php", {
					userId: document.getElementById('editUserDialog_id').value,
					login: document.getElementById('editUserDialog_login').value,
					name: document.getElementById('editUserDialog_name').value,
					group: document.getElementById('editUserDialog_group').value,
					hasAccess: $.toJSON( permitions ),
					url: document.getElementById('editUserDialog_url').value
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$('#editUsersPassDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 400,
		buttons: {
			"Сохранить": function() {
				$.get("includes/editUser.php", {
					userId: document.getElementById('editUsersPassDialog_id').value,
					password: document.getElementById('editUsersPassDialog_newPass').value
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		}
	});
	$('#removeUserDialog').dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Удалить": function() {
				$.get("includes/removeUser.php", {
					userId: document.getElementById('removeUserDialog_id').value,
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
	$('#addUserDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 500,
		height: 600,
		buttons: {
			"Добавить пользователя": function() {
				permitions = {};
				$('.addUserPermitions input').each(function(index, element) {
					num = $(element).attr("data-module");
					if (element.value == "") permitions[num] = 0;
						else permitions[num] = element.value;
                });
				$.get("includes/editUser.php", {
					userId: 'new',
					login: document.getElementById('addUserDialog_login').value,
					name: document.getElementById('addUserDialog_name').value,
					group: document.getElementById('addUserDialog_group').value,
					hasAccess: $.toJSON( permitions ),
					url: document.getElementById('addUserDialog_url').value,
					password: document.getElementById('addUserDialog_pass').value
				}, function() {
					window.location.reload(true);
				});
			},
			"Отмена": function() {
				$( this ).dialog( "close" );
			}
		},
	});
})


function editUser(userId) {
	$('#editUserDialog_loading').show();
	$('#editUserDialog').dialog('open');
	document.getElementById('editUserDialog_id').value = userId;
	$.getJSON("includes/getUserInfo.php",
	  {"userId": userId},
	function(data) {
		document.getElementById('editUserDialog_login').value = data['login'];
		document.getElementById('editUserDialog_name').value = data['name'];
		document.getElementById('editUserDialog_group').value = data['group'];
		document.getElementById('editUserDialog_url').value = data['redirect_url'];
		
		// перекрашиваем все кнопки редактирования прав в исходный цвет
		$('td.permitionSelect span').css("background-image", "url(img/perm-default.png)");
		
		hasAccess= $.evalJSON( data['hasAccess'] );
		for (var key in hasAccess) {
			//alert (hasAccess[key]);
			$('td.editUserPermitions input[data-module=' + key + ']').val(hasAccess[key]);
			$('td.editUserPermitions input[data-module=' + key + ']').trigger("change");
		}
		$('#editUserDialog_loading').hide();
	});
}
function editUsersPass(userId) {
	$('#editUsersPassDialog').dialog('open');
	document.getElementById('editUsersPassDialog_id').value = userId;
	$.getJSON("includes/getUserInfo.php",
	  {"userId": userId},
	function(data) {
		$('#editUsersPassDialog span').html(data[0]);
		document.getElementById('editUsersPassDialog_newPass').value = '';
	})
}
function addUser() {
	$('td.addUserPermitions input').each(function(index, element) {
        element.value = 1;
		$(element).trigger("change");
    });
	$('#addUserDialog').dialog('open');
}
function removeUser(userId) {
	$('#removeUserDialog').dialog('open');
	document.getElementById('removeUserDialog_id').value = userId;
	$.getJSON("includes/getUserInfo.php",
	  {"userId": userId},
	function(data) {
		$('#removeUserDialog span').html(data[0]);
	})
}