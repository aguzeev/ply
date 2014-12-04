<link rel="stylesheet" href="css/style-monitoring.css" />
<div class="monitoringCommonNav">
<a href="index.php?act=monitoring.cp"><span class="option<?php if ($activeComponent == 'personal') echo " active" ?>">персональный</span></a><a href="index.php?act=monitoring.current"><span class="option<?php if ($activeComponent == 'current') echo " active" ?>">текущий</span></a><a href="index.php?act=monitoring.period"><span class="option<?php if ($activeComponent == 'period') echo " active" ?>">детальный за период</span></a><a href="index.php?act=monitoring.month"><span class="option<?php if ($activeComponent == 'month') echo " active" ?>">за месяц</span></a><a href="index.php?act=monitoring.year"><span class="option<?php if ($activeComponent == 'year') echo " active" ?>">за год</span></a><a href="index.php?act=monitoring.dissipation"><span class="option<?php if ($activeComponent == 'dissipation') echo " active" ?>">использование сырья</span></a><a href="index.php?act=monitoring.state"><span class="option<?php if ($activeComponent == 'state') echo " active" ?>">состояние системы</span></a>

<?php
	require_once('includes/monitoring/getLastSync.php');
?>
</div>
<!--<div style="width: 100%; background: #FCE5CF; border-top: 2px #FECEA8 solid; border-bottom: 2px #FECEA8 solid; text-align: center; padding: 5px 0; margin: 0 auto 10px; color: #AB7B6F;">По-видимому, с 12:30 субботы на заводе отсутствует соединение с интернетом.</div>-->