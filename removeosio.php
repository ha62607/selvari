<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['tid']);
$sid = set_safe($_REQUEST['sid']);
$uid = set_safe($_SESSION['uid']);

if (!empty($tid) && !empty($sid) && !empty($uid) )
{

    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);

    $SQL = "DELETE  FROM subtrans WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' AND sutid = '" . $sid. "'";
    $query = $conn->prepare($SQL);
    $query->execute();

    $SQL = "DELETE  FROM tika WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' AND sutid = '" . $sid. "'";
    $query = $conn->prepare($SQL);
    $query->execute();

    $SQL = "DELETE  FROM vati WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' AND sutid = '" . $sid. "'";
    $query = $conn->prepare($SQL);
    $query->execute();


    $conn = null;
    echo("1");
}

