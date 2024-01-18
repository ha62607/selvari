<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tapahtuma']);
$uid = set_safe($_SESSION['uid']);

$type = "json";

if ( !empty($_REQUEST['type']) )
{
    if ($_REQUEST['type'] == 'c')
    {
        $type = "count";
    }
}

echo kuitit($uid, $tid, $type);


?>
