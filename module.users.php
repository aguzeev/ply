<?php

defined('_EXEC') or die;
$category = 5;
$ACCESSED_MODULE = 1;
require_once('includes/init.php');
include('includes/cerber.php');

?>
<script type="text/javascript" src="js/module.users.js"></script>

<div style="width: 98%; text-align: right">
	<?php if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ): ?>
	<input type="button" class="addButton" value="Добавить пользователя" onclick="javascript:addUser();" />
    <?php endif; ?>
</div>

<table width="96%" style="margin: 0 2% 0 2%" border="0" cellspacing="0" cellpadding="3" id="statCommonTable" class="editable hoverable">
  <thead>
  <tr>
    <th width="3%" align="left" class="columnIndent borderB">Id</th>
    <th width="10%" align="left" class="borderB">Логин</th>
    <th width="22%" align="left" class="borderB">ФИО</th>
    <th width="13%" align="left" class="borderB">Группа</th>
    <th width="35%" align="left" class="borderB">Доступные разделы</th>
    <th width="17%" align="left" class="borderB">Адрес перенаправления</th>
  </tr>
  </thead>
<?php
	$query = "SELECT `id`, `login`, `name`, `group`, `redirect_url`, `hasAccess` FROM `users`";
	$result = mysql_query($query, $connection_stat) or die(mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<tr id='rowID_" . $row['id'] . "'><td class='columnIndent'>" . $row['id'] . "</td>\n";
		echo "<td>" . $row['login'] . "</td>\n";
		echo "<td>" . $row['name'] . "</td>\n";
		echo "<td>" . getGroupName($row['group']) . "</td>\n";
		echo "<td>";
		
		$hasAccess = json_decode( $row['hasAccess'] );
		if ($hasAccess != "") {
			foreach ($hasAccess as $module => $permition) {
				if ($permition == 1) { // доступ на просмотр
					echo "<div class='displayPermissionsList'><span style='background-image: url(img/perm-read.png);'>Просм</span>";
					echo "<span>" . $allModules[$module] . "</span></div>";
				} else if ($permition == 2) { // доступ на просмотр
					echo "<div class='displayPermissionsList'><span style='background-image: url(img/perm-write.png);'>Ред</span>";
					echo "<span>" . $allModules[$module] . "</span></div>";
				}
			}
		} else {
			echo "<span>Не задано<span>";
		}
		
		echo "</td>\n";
		echo "<td class='tdRightEdge'>" . $allModulesUrls[$row['redirect_url']] . "\n";
		if ( checkPermition($_SESSION['v2_user_id'], $ACCESSED_MODULE) == 2 ) {
			echo "<table width='60' border='0' cellspacing='0' cellpadding='0' class='editBar' id='editBar_rowID_" . $row['id'] . "'>
			<tr>
			  <td><a href='javascript:editUser(" . $row['id'] . ")'><img src='img/edit.png' width='16' height='16' title='Редактировать' /></a></td>
			  <td><a href='javascript:editUsersPass(" . $row['id'] . ")'><img src='img/password.png' width='16' height='16' title='Изменить пароль' /></a></td>
			  <td><a href='javascript:removeUser(" . $row['id'] . ")'><img src='img/remove.png' width='16' height='16' title='Удалить' /></a></td>
			</tr>
			</table>";
		}
	echo "</td>		
	</tr>\n";
	}
?>
</table>






<div id="editUserDialog" title="Редактирование пользователя">
<div class="cpLoadingBar" id="editUserDialog_loading"></div>
  <form id="editUserDialogForm" action="editUser.php">
	<input type="hidden" id="editUserDialog_id" name="userId" />
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="editUserDialog_login">Логин: </label></td>
    <td><input id="editUserDialog_login" name="login" type="text" style="width: 250px;" /></td>
  </tr>
  <tr>
    <td><label for="editUserDialog_name">ФИО: </label></td>
    <td><input id="editUserDialog_name" name="name" type="text" style="width: 250px;" /></td>
  </tr>
  <tr>
    <td><label for="editUserDialog_group">Группа: </label></td>
    <td><select name="group" id="editUserDialog_group" style="width: 255px;"><?php $query = "SELECT `id`, `group_name` FROM `groups`";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
	echo '<option value="' . $row['id'] . '">' . $row['group_name'] . '</option>';
}
 ?></select></td>
  </tr>
  <tr>
    <td><label for="editUserDialog_hasAccess">Имеет<br />доступ к: </label></td>
    <td>
    	<select id="editUserDialog_hasAccess" name="hasAccess" multiple="multiple" data-placeholder="Выберите модули" style="width: 255px;">
<?php
	$sql = "SELECT `id`, `moduleTitle` FROM `modules`";
	$result = mysql_query($sql, $connection_stat) or die(mysql_error());
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "<option value='" . $row['id'] . "'>" . $row['moduleTitle'] . "</option>";
	}
	mysql_free_result($result);	
?>
        </select>
    </td>
  </tr>
  <tr>
    <td><label for="editUserDialog_url">Права доступа: </label></td>
    <td>
    <table class="editPermissions" width="250" border="0">
<?php
	foreach	($allModules as $key => $value) {
		echo "<tr>
        
        <td class='permitionSelect editUserPermitions' data-module='" . $key . "'>
			<div><span class='noAccess' data-action='no'>Нет</span><span class='read' data-action='read'>Просм</span><span class='write' data-action='write'>Ред</span></div><input data-func='permition' data-module='" . $key . "' type='hidden' />
		</td>
		<td>$value</td>
      </tr>";
	}
?>
    </table>
	</td>
  </tr>
  <tr>
    <td><label for="editUserDialog_url">Адрес<br />перенаправления: </label></td>
    <td>
    <select id="editUserDialog_url" name="url" type="text" style="width: 250px;">
<?php
	foreach	($allModulesUrls as $key => $value) {
		echo "<option value='$key'>$value</option>";
	}
?>
    </select>
    </td>
  </tr>
</table>
</form>
</div>

<div id="editUsersPassDialog" title="Редактирование пароля">
<form id="editUsersPassDialogForm" action="editUser.php">
	<input type="hidden" id="editUsersPassDialog_id" name="userId" />
    <p>Пароль для пользователя <span style="color: #F00;"></span></p>
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="editUsersPassDialog_newPass">Новый&nbsp;пароль: </label></td>
    <td><input id="editUsersPassDialog_newPass" name="passwod" type="password" /></td>
  </tr>
</table>
</form>
</div>

<div id="addUserDialog" title="Добавление пользователя">
<form id="addUserDialogForm" action="editUser.php">
	<input type="hidden" id="addUserDialog_id" name="userId" value="new" />
    <p>Пароль для пользователя <span style="color: #F00;"></span></p>
	<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td><label for="addUserDialog_login">Логин: </label></td>
    <td><input id="addUserDialog_login" name="login" type="text" style="width: 250px;" /></td>
  </tr>
  <tr>
    <td><label for="addUserDialog_name">ФИО: </label></td>
    <td><input id="addUserDialog_name" name="name" type="text" style="width: 250px;" /></td>
  </tr>
  <tr>
    <td><label for="addUserDialog_group">Группа: </label></td>
    <td><select name="group" id="addUserDialog_group" style="width: 255px;"><?php $query = "SELECT `id`, `group_name` FROM `groups`";
$result = mysql_query($query, $connection_stat) or die(mysql_error());
while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
	echo '<option value="' . $row['id'] . '">' . $row['group_name'] . '</option>';
}
 ?></select></td>
  </tr>
  <tr>
    <td><label for="editUserDialog_url">Права доступа: </label></td>
    <td>
    <table class="editPermissions" width="250" border="0">
<?php
	foreach	($allModules as $key => $value) {
		echo "<tr>
        
        <td class='permitionSelect addUserPermitions' data-module='" . $key . "'>
			<div><span class='noAccess' data-action='no'>Нет</span><span class='read' data-action='read'>Просм</span><span class='write' data-action='write'>Ред</span></div><input data-func='permition' data-module='" . $key . "' type='hidden' />
		</td>
		<td>$value</td>
      </tr>";
	}
?>
    </table>
	</td>
  </tr>
  <tr>
    <td><label for="addUserDialog_url">Адрес<br />перенаправления: </label></td>
    <td>
    <select id="addUserDialog_url" name="url"style="width: 250px;">
<?php
	foreach	($allModulesUrls as $key => $value) {
		echo "<option value='$key'>$value</option>";
	}
?>
    </select>
    </td>
  </tr>
  <tr>
    <td><label for="addUserDialog_pass">Пароль: </label></td>
    <td><input id="addUserDialog_pass" name="passwod" type="password" style="width: 250px;" /></td>
  </tr>
</table>
</form>
</div>

<div id="removeUserDialog" title="Удаление пользователя">
<input type="hidden" id="removeUserDialog_id" name="userId" />
<p>Вы действительно хотите удалить пользователя <span style="color: #F00;"></span>?</p>
</div>