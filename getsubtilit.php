<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tid']);
$uid = set_safe($_SESSION['uid']);

$ar = getOsionTilit($uid, $tid);
$json = json_encode($ar);

error_log("OSIO LISTA TILIKOODIT:".$json);

echo( $json );
?>
