<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг, контрольная панель';

include('includes/cerber.php');
require_once('includes/init.php');

// в массиве указать индексы станков, с которым проводятся работы
$machUnderCounstruction = array();
	
?>
<script type="text/javascript" src="js/plotVariants.js.php"></script>
<script type="text/javascript" src="js/module.monitoring.plotit.js?v1"></script>
<script type="text/javascript" src="js/module.monitoring.summary.js?v1"></script>
<script type="text/javascript" src="js/module.monitoring.cp.js?v1"></script>

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.json2.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasOverlay.min.js"></script>
<script type="text/javascript" src="js/jquery.tmpl.min.js"></script>

<!-- Для отрисовки названий осей, поворота делений на графике-->
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script language="javascript">
$(document).ready(function() {
	//$('#resolution').text(document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth);
	$(window).resize(function() {
    	 //$('#resolution').text( document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth );
	});
});
</script>


<link rel="stylesheet" href="css/style-monitoring.css" />
<link rel="stylesheet" href="js/jqplot/jquery.jqplot.css" />
<div id="resolution">
</div>

<?php $activeComponent = 'personal'; include('monitoring-common.php'); ?>

<div class="cpVidget_container" id="cpVidget_container">
	<div class="containerLoadingBar" id="containerLoadingBar">&nbsp;</div>
    <script id="vidgetTemplate" type="text/x-jquery-tmpl">
		<div class="cpVidget" id="${vidget_id}">
			<div class="cpLoadingBar"></div>
			<div class="cpVidgetMove" title="Переместить"></div>
			<div class="cpVidgetSettings" title="Редактировать параметры" onclick="javascript:editWidget('${database_id}');"></div>
			<span class="cpVidgetTitle">${title}</span>
			<div id="${id}" style="height: 100px; margin: 5px 0 0 -10px; padding: 0; border-top: 1px dotted; border-bottom: 1px dotted;"></div>
			<span class="cpVidgetContentTitle">${content}</span>
			<span class="cpVidgetContentValue" id="${vidget_id}_value"><img src="img/loading_small_2.gif" />${value}</span>
		</div>
	</script>
    
    <div class="cpVidget addNewVidget" id="addNewVidget" onclick="javascript: addWidget();">
    	<div class="addNewVidgetPlus"></div>
    </div>
</div>

<div style="clear: both;"></div>

<div id="editVidgetDialog" title="Редактировать виджет">
		<div class="cpLoadingBar" id="editDialogLoadingBar"></div>
		<table border="0" cellspacing="0" cellpadding="2" style="margin: 0 0 15px 0;">
		<tr>
			<td width="90">Станок:</td>
			<td>
            	<select id="editVidget_machine" style="width: 200px;">
<?php
	$machineNames = array('Лущение', 'Ножницы', 'Котельная', 'Сушилка', 'Опиловка', 'Шлифовка', 'Сращивание', 'Распиловка', 'Пресс', 'Раскряжёвка');
	//$machineNames = $MACHINES_IDS;

foreach ($machineNames as $key => $value) {
	echo "<option value='$key'>$value</option>";
}
				?>
                </select>
            </td>
		</tr>
		<tr>
			<td width="90">Показатель:</td>
			<td><select id="editVidget_variant" style="width: 200px;"></select></td>
		</tr>
		<tr>
			<td width="90">Период:</td>
			<td>
            <select id="editVidget_period" style="width: 200px;">
              <option value="8_hours">8 часов</option>
              <option value="16_hours">16 часов</option>
              <option value="1_day">1 сутки</option>
              <option value="2_days">2 суток</option>
            </select>
            <input type="hidden" id="editVidget_order"  />
            <input type="hidden" id="editVidget_database_id"  />
            </td>
		</tr>
        <tr>
        	<td colspan="2" align="center"><span class="asButtonRed" id="removeWidget" onclick="javascript: removeWidget()">Удалить</span></td>
        </tr>
		</table>
</div>

<div id="removeVidgetDialog" title="Удаление виджета"><p>Вы уверены, что хотите удалить этот виджет?</p></div>

<div id="testing"></div>

<!-- Служебные сообщения -->
<div id="loading">
	<!--<img align="middle" style="position: absolute; top: 40%; left: 48%; z-index: 999" src="img/loading.gif" />-->
	<div class="semiopacity"></div>
	
</div>