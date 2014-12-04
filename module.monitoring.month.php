<?php
header('Cache-Control: no-cache');

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг за месяц';

include('includes/cerber.php');
require_once('includes/init.php');

?>

<?php $activeComponent = 'month'; include('monitoring-common.php'); ?>

<script type="text/javascript" src="js/plotVariants.js.php"></script>

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.json2.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasOverlay.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/module.monitoring.month.js"></script>


<div class="month-heading">
    <div class="month-heading-machine" data-tab="0" data-machine=""><a href="javascript:void(0);">Лущение&nbsp;(вх)</a></div>
    <div class="month-heading-machine" data-tab="1" data-machine="raute_cutter"><a href="javascript:void(0);">Лущение&nbsp;(вых)</a></div>
    <div class="month-heading-machine" data-tab="2" data-machine=""><a href="javascript:void(0);">Сушка (вх)</a></div>
    <div class="month-heading-machine" data-tab="3" data-machine="warmer_out"><a href="javascript:void(0);">Сушка (вых)</a></div>
    <div class="month-heading-machine" data-tab="4" data-machine="lopping"><a href="javascript:void(0);">Опиловка</a></div>
    <div class="month-heading-machine" data-tab="5" data-machine="grinding"><a href="javascript:void(0);">Шлифовка</a></div>
    <div class="month-heading-machine" data-tab="6" data-machine="merger"><a href="javascript:void(0);">Сращивание (нов)</a></div>
    <div class="month-heading-machine" data-tab="7" data-machine="merger_old"><a href="javascript:void(0);">Сращивание (стар)</a></div>
    <div class="month-heading-machine" data-tab="8" data-machine="press"><a href="javascript:void(0);">Пресс</a></div>
</div>
<div class="month-detailed">
    <div align="center" style="text-align: center; margin: 5px auto -5px auto; padding: 5px 0 0 0;">
    	<div class="monthLegend" style="background-color: #4BB2C5"></div><span> — I смена</span>
    	<div class="monthLegend" style="background-color: #EAA228"></div><span> — II смена</span>
        <div class="monthLegend" style="background-color: #C5B47F"></div><span> — III смена (до 1.10.2014)</span>
    	<select id="monthSelect" style="margin-left: 20px;"></select><select id="yearSelect"></select>&nbsp;<a href="javascript:plotMonthGraph()">показать</a>
    </div>
    <div id="monthCompleteContainer"></div>
  	<div class="month-dates"></div>
</div>