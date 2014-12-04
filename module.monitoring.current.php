<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг текущий';

include('includes/cerber.php');
require_once('includes/init.php');

// в массиве указать индексы станков, с которым проводятся работы
$machUnderCounstruction = array();
	
$activeComponent = 'current'; include('monitoring-common.php');
?>

<script type="text/javascript" src="js/plotVariants.js.php"></script>
<script type="text/javascript" src="js/module.monitoring.current.js?v5"></script>

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.meterGaugeRenderer.min.js"></script>
<script type="text/javascript" src="js/jquery.tmpl.min.js"></script>

<h3 align="center">Производительность за последний час</h3>
<div id="meterContainer" class="meterContainer" align="center"></div>

<h3 align="center">Выработка за сегодня</h3>
<table width="50%" border="0" align="center" class="current-valueTable">
  <tr>
    <td>&nbsp;</td>
    <td align="center"><strong>1-я смена</strong></td>
    <td align="center"><strong>2-я смена</strong></td>
    <!--<td align="center"><strong>3-я смена</strong></td>-->
    <td align="center"><strong>с начала суток</strong></td>
    <td align="center"><strong>с начала месяца</strong></td>
  </tr>
  <tr data-machine="0">
    <td>Лущение</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="1">
    <td>Сухой шпон</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="2">
    <td>Опиловка</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="3">
    <td>Шлифовка</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="4">
    <td>Сращивание (нов)</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="5">
    <td>Сращивание (стар)</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
  <tr data-machine="6">
    <td>Пресс</td>
    <td align="center" data-field="0">&nbsp;</td>
    <td align="center" data-field="1">&nbsp;</td>
    <!--<td align="center" data-field="2">&nbsp;</td>-->
    <td align="center" data-field="3">&nbsp;</td>
    <td align="center" data-field="4">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<h3 align="center">Лучше смены</h3>
<div id="maxesContainer" class="maxesContainer" align="center">
</div>
<p align="center">При подсчёте не учитывались скорретированные вручную показания.</p>

<script id="leaderShiftTemplate" type="text/x-jquery-tmpl">
	<a href="${link}" class="leaderShift">
		<div class="leaderShiftWrap {{if isNew}} newLeader{{/if}}">
			<p class="machineName">${title}</p>
			<p class="value">${value}${units}</sup></p>
			<p class="date">{{html date}}</p>
		</div>
	</a>
</script>