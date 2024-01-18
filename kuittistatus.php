<?php
session_start();
require_once ('config.php');
$uid = set_safe($_SESSION['uid']);
$tid = set_safe($_REQUEST['tid']);
echo(get_kuittistatus($uid, $tid));

