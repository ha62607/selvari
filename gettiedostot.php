<?php
session_start();
require_once ('config.php');

$ar = getMyFiles();
$json = json_encode($ar);

error_log("FILUT:".$json);

echo( $json );
?>
