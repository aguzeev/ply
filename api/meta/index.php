<?php

// get warehouse common metadata

header('Content-Type: text/json; charset=utf-8');

require_once('../../includes/warehouse/warehouse_dictionary.php');

$ACCESSED_MODULE = 12;
$_LOGGING = false;
//include('../cerber.php');


// *************************
// ***** get meta info *****
// *************************

$meta = array();

$meta["grade"] = $wh_ply_grade;
$meta["type"] = $wh_ply_types;
$meta["sading"] = $wh_ply_sanding;
$meta["sort_1"] = $wh_ply_sorts;
$meta["sort_2"] = $wh_ply_sorts;

$checksum = md5( serialize($meta) );

echo json_encode( array("result" => "ok", "meta" => $meta, "checksum" => $checksum), JSON_UNESCAPED_UNICODE );