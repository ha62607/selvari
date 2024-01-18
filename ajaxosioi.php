<?php
session_start();
require_once ('config.php');

$ok = true;

$uid = set_safe($_SESSION['uid']);
$tid = set_safe($_POST['tid']);
$transid = set_safe($_POST['transid']);
$osioname = set_safe($_POST['osioname']);
$osionsumma = set_safe($_POST['osionsumma']);
$osionsumma = floatval($osionsumma);
$osionsumma = round($osionsumma, 2);
$aika = gettime();
$ip = getUserIP();


error_log("Jaa jaa...:".$osioname);


if(!empty($osioname) && strlen($osioname) > 2)
{
    error_log("Jepulis...");
    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "INSERT INTO subtrans (tid,uid,transactionId,name,summa,create_ip) VALUES ('".$tid."','".$uid."','".$transid."','".$osioname."','".$osionsumma."','".$ip."')";
    error_log($SQL);
    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $db->close();


}

if (!$ok)
{
    echo '0';
}
elseif ($ok)
{
    echo '1';
}
?>
