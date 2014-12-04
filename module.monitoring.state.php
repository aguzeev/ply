<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Состояние системы';

include('includes/cerber.php');
require_once('includes/init.php');

// в массиве указать индексы станков, с которым проводятся работы
$machUnderCounstruction = array();
	
?>

<script type="text/javascript" src="js/module.monitoring.state.js"></script>

<link rel="stylesheet" href="styleMonitoring.css" />
<link rel="stylesheet" href="js/jqplot/jquery.jqplot.css" />

<?php $activeComponent = 'state'; include('monitoring-common.php'); ?>

<div style="width: 90%; margin: 0 auto;">
    <div>
        <h1>Warmer-in</h1>
        <p>Socket state: <span id="state_in">wait...</span></p>
        <p>Ping-pong delay: <span id="pp_delay_in">wait...</span></p>
        <p>Last square: <span id="sq_span_in">wait...</span></p>
        <p>CPU temperature: <span id="cpu_temp_span_in">wait...</span></p>
    </div>
    
    <div>
        <h1>Warmer-out</h1>
        <p>Socket state: <span id="state_out">wait...</span></p>
        <p>Ping-pong delay: <span id="pp_delay_out">wait...</span></p>
        <p>30 min koef: <span id="koef30min_span_out">wait...</span></p>
        <!--<p>width: <span id="width_span_out">wait...</span></p>
        <p>height: <span id="height_span_out">wait...</span></p>-->
        <p>CPU temperature: <span id="cpu_temp_span_out">wait...</span></p>
    </div>
</div>