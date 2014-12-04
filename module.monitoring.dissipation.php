<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Мониторинг потерь';

include('includes/cerber.php');
require_once('includes/init.php');

?>

<script type="text/javascript" src="js/plotVariants.js.php"></script>
<script type="text/javascript" src="js/module.monitoring.dissipation.js?v1"></script>

<?php $activeComponent = 'dissipation'; include('monitoring-common.php'); ?>

<div class="cpLoadingBar" id="cpLoadingBar"></div>

<h2>Степень использования сыря на участке</h2>

<!--<div id="underconstuction" style="width: 100%; border: #F00 1 px solid; background-color: #FFAC59; font-family: Tahoma, Geneva, sans-serif; font-size: 14px; text-align: center; font-weight: bold; padding: 5px 0 5px 0;">
Данный интерфейс является проектом и пока что не отражает точных сведений о производстве.
</div>-->
      
<div class="dissipNavi">
  <input type="text" id="dateBegin" size="13" style="margin: 0;" />&nbsp;—&nbsp;
  <input type="text" id="dateEnd" size="13" style="margin: 0;" />&nbsp;<a class="asButton" href="javascript:drawDissipations()">Показать</a>
</div>
<div align="center" class="dissipContainer">
	<img src="img/dissipation_bg.png" />
  <div class="dissip-saw" id="dissip-saw" style="visibility: hidden;">
    	<span class="dissip-title">распиловка</span><span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2">(0%)</span>
    </div>
  <div class="dissip-shell" id="dissip-shell">
    	<span class="dissip-title">раскройка</span><span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
  <div class="dissip-cutter" id="dissip-cutter">
    	<span class="dissip-title">нарезка на листы</span><span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
  <div class="dissip-pencil" id="dissip-pencil">
    	<span class="dissip-title">канардаш</span>&nbsp;<span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
  <div class="dissip-warmer-in" id="dissip-warmer-in">
    	<span class="dissip-title">подано на сушку</span>&nbsp;<span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
  <div class="dissip-warmer-out" id="dissip-warmer-out">
    	<span class="dissip-title">выход с сушки</span>&nbsp;<span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
  <div class="dissip-merger" id="dissip-merger" style="visibility: hidden;">
    	<span class="dissip-title">сращивание</span>&nbsp;<span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2">(0%)</span>
    </div>
  <div class="dissip-lopping" id="dissip-lopping">
    	<span class="dissip-title">опиловка</span>&nbsp;<span class="dissip-percent-1"></span>&nbsp;<span class="dissip-percent-2"></span>
    </div>
</div>