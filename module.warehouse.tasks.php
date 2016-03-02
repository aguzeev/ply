<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 12;
$LOG_TITLE = 'Склад';

include('includes/cerber.php');
require_once('includes/init.php');
require_once("includes/warehouse/warehouse_dictionary.php");

$activeComponent = 'warehouse';

?>

<script language="javascript" src="js/jquery.tmpl.min.js"></script>
<script language="javascript" src="js/jquery.validate.min.js"></script>
<script language="javascript" src="js/messages_ru.min.js"></script>
<script language="javascript" src="js/module.warehouse.tasks.js"></script>

 <link rel="stylesheet" href="css/animation.css"><!--[if IE 7]><link rel="stylesheet" href="css/fontello-ie7.css"><![endif]-->

<div id="tasksCommonBlock">
  <ul>
    <li><a href="#loppingTasks">Опиловка</a></li>
    <li><a href="#packingTasks">Упаковка</a></li>
  </ul>
  <div id="loppingTasks" data-isEdited="false" class="tasksPane packingTasksLopping" data-isEdited="false">
  </div>
  <div id="packingTasks" data-isEdited="false" class="tasksPane packingTasksPacking" data-isEdited="false">
  </div>
</div>

<div>
    <button class="addButton" id="addTaskButton"><i class='isIcon icon-plus-circle'>&#xe805;</i>&nbsp;Добавить задание</button>
    <button class="saveButton" id="saveEditedButton"><i class="isIcon icon-floppy">&#xe80A;</i>&nbsp;Сохранить изменения</button>
    <button class="cancelButton" id="cancelEditedButton"><i class="isIcon icon-cancel-circled">&#xe806;</i>&nbsp;Отменить</button>
</div>




<script id="taskEditingTmpl" type="text/x-jquery-tmpl">
<tr id="rowID_${taskData.id}" class="notSavedYet {{if isNewRow}}isNewRow{{/if}}">
	<td class="whReadyDate taskNumber">${taskData.id}</td>
	<td class="whType" data-value="${taskData.type}">
		<div>
			<label><input type="radio" name="whTypeRadio_${taskData.id}" value="F" {{if taskData.type == "F"}} checked{{/if}}/>ФСФ</label><br>
			<label><input type="radio" name="whTypeRadio_${taskData.id}" value="V" {{if taskData.type == "V"}} checked{{/if}}/>ФБВ</label>
		</div>
	</td>
	<td class="whLength"><input type="text" size="6" maxlength="4" required value="${taskData.length}" /></td>
	<td class="whWidth"><input type="text" size="6" maxlength="4" required value="${taskData.width}" /></td>
	<td class="whThickness"><input type="text" size="6" maxlength="4" required value="${taskData.thickness}" /></td>
	<td class="whSort1">
		<select class="whSortSelect whSort1Select">
			<option value="0" {{if taskData.sort_1 == 0}} selected{{/if}} disabled>-/-</option>
			<option value="1" {{if taskData.sort_1 == 1}} selected{{/if}}>1</option>
			<option value="2" {{if taskData.sort_1 == 2}} selected{{/if}}>2</option>
			<option value="3" {{if taskData.sort_1 == 3}} selected{{/if}}>3</option>
			<option value="4" {{if taskData.sort_1 == 4}} selected{{/if}}>4</option>
		</select>
	</td>
	<td class="whSort2">
		<select class="whSortSelect whSort2Select">
			<option value="0" {{if taskData.sort_2 == 0}} selected{{/if}} disabled>-/-</option>
			<option value="1" {{if taskData.sort_2 == 1}} selected{{/if}}>1</option>
			<option value="2" {{if taskData.sort_2 == 2}} selected{{/if}}>2</option>
			<option value="3" {{if taskData.sort_2 == 3}} selected{{/if}}>3</option>
			<option value="4" {{if taskData.sort_2 == 4}} selected{{/if}}>4</option>
		</select>
	</td>
	<td class="whSanding" data-value="${sanding}">
		<select class="whSandingSelect">
			<option value="0" {{if taskData.sanding == 0}} selected{{/if}}>н/ш</option>
			<option value="2" {{if taskData.sanding == 2}} selected{{/if}}>Ш2</option>
		</select>
	</td>
	<td class="whQuantity"><input type="text" size="6" maxlength="4" required value="${taskData.quantity}" /></td>
	<td class="whComment"><input type="text" size="20" maxlength="200" required value="${taskData.comment}" /></td>
	<td class="whReadiness">${taskData.readyness}</td>
	<td class="whEditColumn">
	{{if isEditable}}
		<i class="isIcon icon-attention noSuchFormatIcon" title="В справочнике нет такого формата">&#xe809;</i>
		<i class="isIcon icon-spin5 processIcon animate-spin" title="В справочнике нет такого формата">&#xe800;</i>
		<div class="editBar" id="editBar_${taskData.id}">
			<i class="isIcon icon-trash isPointer" data-id="${taskData.id}">&#xe80B;</i>
		</div>
	{{else}}
		<div align="right"><i class="isIcon icon-lock-filled">&#xe807;</i></div>
	{{/if}}
	</td>
</tr>
</script>


 
<script id="taskCompleteTableTmpl" type="text/x-jquery-tmpl">
<table class="whTasks whEditable" id="taskTable_${taskType}" data-taskType="${taskType}">
	<thead>
      <tr>
        <td class="whDate">&nbsp;</td>
        <td class="whType">Марка</td>
        <td class="whLength">Длина</td>
        <td class="whWidth">Ширина</td>
        <td class="whThickness">Толщина</td>
        <td class="whSort1">Сорт 1</td>
        <td class="whSort2">Сорт 2</td>
        <td class="whSanding">Шлифов.</td>
        <td class="whQuantity">Листов</td>
        <td class="whComment">Комментарий к заданию</td>
        <td class="whReadiness">Статус выполнения</td>
        <td class="whEditColumn"></td>
      </tr>
    </thead>
{{each(index, value) data}}
		{{each(indexInner, valueInner) tasksData}}
			{{if indexInner == 0}}
		<tbody data-date="${date}" data-shift="${shift}" {{if !isEditable}} class='currentDayTask'{{/if}}>
			<tr>
				<td colspan='12' class='whDate whUnhoverable'>${date_text}, ${shift} смена</td>
			</tr>
			{{/if}}
			<tr id="rowID_${valueInner.id}">
			<td class="whReadyDate taskNumber" data-value="${dateReady}">${id}</td>
			<td class="whType" data-value="${type}">${type_text}</td>
			<td class="whLength">${length}</td>
			<td class="whWidth">${width}</td>
			<td class="whThickness">${thickness / 10}</td>
			<td class="whSortSelect whSort1">{{if tasksData.sort_1 > 0}}${sort_1}{{else}}—{{/if}}</td>
			<td class="whSortSelect whSort2">{{if tasksData.sort_2 > 0}}${sort_2}{{else}}—{{/if}}</td>
			<td class="whSanding" data-value="${sanding}">${sanding_text}</td>
			<td class="whQuantity">${quantity}</td>
			<td class="whComment">${comment}</td>
			<td class="whReadiness">${readyness}</td>
			<td class="whEditColumn">
			{{if isEditable}}
				<div class="editBar" id="editBar_${id}">
					<i title="Редактировать" class="isIcon icon-edit isPointer" data-id="${id}">&#xe801;</i>
					<i title="Скопировать в задания на упаковку" class="isIcon icon-export isPointer">&#xe808;</i>
					<i title="Удалить" class="isIcon icon-trash isPointer" data-id="${id}">&#xe80B;</i>
				</div>
			{{else}}
				<div align="right"><i class="isIcon icon-lock-filled">&#xe807;</i></div>
			{{/if}}
			</td>
		</tr>

		{{/each}}
		<tr class="lastRow {{if isEditable}} addNewRow{{/if}}" data-date="${date}" data-shift="${shift}">
			<td colspan='12'>{{if isEditable}}<i class='isIcon icon-plus-circle isPointer'><span>&#xe805;</span></i>{{/if}}</td>
		</tr>
	</tbody>
{{/each}}
</table>
</script>


<div id="addTaskDateSelectorDialog" title="Укажите дату задания" align="center">
<div id="plyReadyDate"></div>
<div id="plyReadyShift">
    <label for="radio_shift_1"><input type="radio" name="plyReadyShiftRadio" id="radio_shift_1" value="1" checked>1 смена</label>
    <label for="radio_shift_2"><input type="radio" name="plyReadyShiftRadio" id="radio_shift_2" value="2">2 смена</label>
</div>
</div>



<div id="deleteTaskDialog" title="Удаление задания">
<p>Вы действительно хотите удалить задание #<span id="taskToDeleteIdText"></span>?</p>
<input type="hidden" id="taskToDeleteId">
</div>