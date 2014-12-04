<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг детальный';

include('includes/cerber.php');
require_once('includes/init.php');

// в массиве указать индексы станков, с которым проводятся работы
$machUnderCounstruction = array();
	
?>

<script type="text/javascript" src="js/plotVariants.js.php"></script>
<script type="text/javascript" src="js/module.monitoring.plotit.js?v1"></script>
<script type="text/javascript" src="js/module.monitoring.summary.js"></script>
<script type="text/javascript" src="js/module.monitoring.period.js?v2"></script>

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.json2.min.js"></script>

<!-- Для отрисовки названий осей, поворота делений на графике-->
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>


<!--
<script src="js/jquery.tmpl.min.js"></script>
<script language="javascript" src="js/scripts.js"></script>
-->

<link rel="stylesheet" href="styleMonitoring.css" />
<link rel="stylesheet" href="js/jqplot/jquery.jqplot.css" />

<?php $activeComponent = 'period'; include('monitoring-common.php'); ?>

<div class="timeBoardNav">
	<div class="timeBoardNavInner">
    	<table border="0">
          <tr>
            <td align="right" width="120">Станок:&nbsp;</td>
            <td><select name="plotMachine" size="1" id="plotMachine" class="plotMachine" style="width: 230px;">
            </select></td>
          </tr>
          <tr>
            <td align="right">Параметр работы:&nbsp;</td>
            <td><select id="plotField" name="plotField" class="plotField" multiple="multiple" style="width: 230px;"></select></td>
          </tr>
        </table>
	</div>
	<div class="timeBoardNavInner">
		<label for="dateBegin">за период</label>&nbsp;&nbsp;<input type="text" id="dateBegin" name="dateBegin" size="18" />
		<label for="dateBegin">&#8212;</label>&nbsp;<input type="text" id="dateEnd" name="dateEnd" size="18" />
        <p class="under" style="text-align: center;">
        или&nbsp; <a href="javascript:plotPreset('today')" title="Начало рабочих суток в 8:00.">за сегодня</a>,
		<a href="javascript:plotPreset('yesterday')" title="Начало рабочих суток в 8:00.">за вчера</a>,
		с начала <a href="javascript:plotPreset('week')" title="С 8 часов утра понедельника.">недели</a> или <a href="javascript:plotPreset('month')" title="С 8 часов утра 1-го числа.">месяца</a>&nbsp;&nbsp;<br />
		</p>
		<div style="padding-top: 3px; text-align: center;">
			<input id="mx" name="mx" type="hidden" value="1" />
			<span class="under hasTip" title="При большей детализации можно отследить кратковеменные остановки и пики, а при меньшей — быстро получить обобщённые данные.">
            детализация:</span>
			<span class="under"><a href="javascript:changeSliderVal('dec')">меньше</a></span>&nbsp;&nbsp;
			<div id="mxSlider" style="width: 70px; display: inline-block;"></div>
            &nbsp;&nbsp;<span class="under"><a href="javascript:changeSliderVal('inc')" title="Для отображения потребуется больше времени.">больше</a></span>
		</div>
	</div>
    <div class="timeBoardNavInner" style="text-align: center;">
		<!--<a class="asButton" href="javascript:checkOnline(this)">&nbsp;&nbsp;показать&nbsp;&nbsp;</a>-->
        <a class="asButton" href="javascript:plotIt2()" id="startPlotButton">&nbsp;&nbsp;показать&nbsp;&nbsp;</a>
        <a class="asButton isOnline" style="display: none" href="javascript:stopOnline()" id="stopOnlineButton">&nbsp;&nbsp;остановить&nbsp;&nbsp;</a>
		<div style="margin-top: -5px;">
        	<!--<input title="Показать данные в виде графика" name="showGraph" id="showGraph" type="checkbox" checked="checked" />
        	<label for="showGraph"><span class="under" style="border-bottom: 1px dotted;">показать график</span></label><br />-->
            
			<input title="Данные будут обновляться автоматически каждые 10 минут" name="onlineBox" id="onlineBox" type="checkbox" value="" />&nbsp;
            <label for="onlineBox" class="onlineBox"><span title="Данные будут обновляться автоматически каждые 10 минут" class="under hasTip">автообновление</span></label>
			<br />
            <a id="todayLink" class="todayLink" href="#" title="Вы можете просто обновлять страницу, чтобы видеть актуальный график работы">график на сегодня</a>
		</div>
	</div>
</div>
<div style="margin: 5px 15px 20px 15px; font-size: 11px;" align="right">Ссылка на этот график: <input type="text" id="shareLink" size="20" style="height: 12px;" placeholder="укажите все параметры"></div>

<?php
	/*if ( isset($_GET['mach']) ) $mach = $_GET['mach'];
	if ( in_array($mach, $machUnderCounstruction)  ) { 
	  echo'
	  <div id="underconstuction" style="width: 100%; border: #F00 1 px solid; background-color: #FFAC59; font-family: Tahoma, Geneva, sans-serif; font-size: 14px; text-align: center; font-weight: bold; padding: 5px 0 5px 0;">
	  В системе статистики по этому станку проводятся технические работы, данные могут отображаться неверно.
	  </div>';
  	}*/
?>


<div id="graphs">
	<div id="chart1"></div>
	<div id="chart2"></div>
    <div id="profilingResult"></div>
</div>
<div id="summaryCantainer">
	<p style="font-size: 18px; margin: 0 15px;">Сводка:</p>
    <div id="summaryValues"></div>
	<p style="font-size: 12px; color: #a6a495; margin: 10px 0 0 10px;">Цифры со знаком * означают, что данные были скорректированы вручную</p>
</div>
<div class="summaryVal" id="summaryUnder" style="margin-top: 20px;"></div>


<!-- Служебные сообщения -->
<div id="loading">
	<!--<img align="middle" style="position: absolute; top: 40%; left: 48%; z-index: 999" src="img/loading.gif" />-->
	<div class="semiopacity"></div>
	
</div>
<div id="tooLongPeriod" title="Слишком длинный период">
	<p>Вы выбрали построение графика за очень большой период времени. Пожалуйста, воспользуйтесь кладками «<a href="index.php?act=monitoring.month">за месяц</a>» или «<a href="index.php?act=monitoring.year">за год</a>» для доступа к выбранным данным</p>
</div>