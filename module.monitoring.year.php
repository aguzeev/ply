<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг за год';

include('includes/cerber.php');
require_once('includes/init.php');

?>

<?php $activeComponent = 'year'; include('monitoring-common.php'); ?>

<script type="text/javascript" src="js/plotVariants.js.php"></script>
<script type="text/javascript" src="js/module.monitoring.year.js"></script>

<script type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.pointLabels.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.cursor.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.json2.min.js"></script>
<script type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>

<p align="center" class="yearSummaryTitle">Сводка по месяцам за&nbsp;<select id="yearSelect"></select>&nbsp;год&nbsp;<a href="javascript:plotYearGraph( document.getElementById('yearSelect').value )"><img src="img/reload.png" alt="Обновить сводку" /></a></p>
<div id="yearCompleteContainer"></div>