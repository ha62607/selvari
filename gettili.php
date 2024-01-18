<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tapahtuma']);
$uid = set_safe($_SESSION['uid']);

$ar = getTilit($uid, $tid);
$json = json_encode($ar);

//error_log("TJOSN:".$json);

echo( $json );
?>
