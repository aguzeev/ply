<?php
header("Content-type: application/json; charset=utf-8");

require_once('../includes/monitoring/plotVariants.php');
echo 'machineNames = ' . json_encode($machineNames) . '; ';
echo 'plotVariants = ' . json_encode($plotVariants);

?>