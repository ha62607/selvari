<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tapahtuma']);
$uid = set_safe($_SESSION['uid']);


$selite = get_selitte($uid, $tid);

echo($selite);

?>
