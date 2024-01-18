<?php
session_start();
require_once ('config.php');

$tid = set_safe($_REQUEST['removetapahtuma']);
$kid = set_safe($_REQUEST['removekuitti']);

$uid = set_safe($_SESSION['uid']);

if (!empty($tid) && !empty($kid) && !empty($uid) )
{
$SQL = "SELECT tiedosto FROM kuitit WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' AND kid = '" . $kid. "'";

//error_log("POISTAKUVA:".$SQL);
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$query = $conn->prepare($SQL);
$query->execute();

$tiedosto = "";

while ($row  = $query->fetch())
{
    $tiedosto = $row['tiedosto'];
}

if (!empty($tiedosto))
{
    $filename = $TARGETDIR . $tiedosto;
    if (file_exists($filename))
    {
        error_log("POISTAKUVA:".$filename);
        unlink($filename);
        $SQL = "DELETE  FROM kuitit WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' AND kid = '" . $kid. "'";
        $query = $conn->prepare($SQL);
        $query->execute();
        set_kuittistatus($uid, $tid);

    }
}

$conn = null;

echo("1");
}



?>