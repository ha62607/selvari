<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tapahtuma']);
$uid = set_safe($_SESSION['uid']);

$ar = getOsiot($uid, $tid);
$json = json_encode($ar);

error_log("OSIO LISTA:".$json);

echo( $json );
?>
