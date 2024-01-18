<?php
session_start();
require_once ('config.php');

$ok = true;

$uid = set_safe($_SESSION['uid']);
$tilikoodi = set_safe($_POST['tilikoodi']);
$tid = set_safe($_POST['tid']);

$sid = set_safe($_POST['sid']);

$alv = set_safe($_POST['alv']);

$amount = set_safe($_POST['amount']);

$amount = floatval($amount);

$pos = true;

if ($amount < 0)
{
    $pos = false;
}

error_log("Tili set 1.0 SID:".$sid);

if(!empty($uid) && is_int(intval($uid)) && is_float($amount) )
{

    error_log("Tili set 1.1");

    $key = $tid;

    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "";

    if(intval($sid) > 0)
    {
        $key = $tid.'0000'.$sid;
        $SQL = "INSERT INTO tika (id,tilinro,amount,uid,tid,sutid,alv) VALUES ('".$key."','".$tilikoodi."','".$amount."','".$uid."','".$tid."','".$sid."','".$alv."') ON DUPLICATE KEY UPDATE tilinro='".$tilikoodi."', alv='".$alv."'";

    }
    else
    {
        $SQL = "INSERT INTO tika (id,tilinro,amount,uid,tid,alv) VALUES ('".$key."','".$tilikoodi."','".$amount."','".$uid."','".$tid."','".$alv."') ON DUPLICATE KEY UPDATE tilinro='".$tilikoodi."', alv='".$alv."'  ";
    }


   // error_log($SQL);

    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $vastasumma  = 0;

    if ($amount < 0)
    {
        $vastasumma  = $amount * -1;
    }
    else
    {
        $vastasumma  = 0 - $amount;
    }

    if(intval($sid) > 0)
    {
        $SQL = "INSERT INTO vati (id,amount,uid,tid,sutid,alv) VALUES ('".$key."','".$vastasumma."','".$uid."','".$tid."','".$sid."','".$alv."') ON DUPLICATE KEY UPDATE amount='".$vastasumma."', alv='".$alv."'";

    }
    else
    {
        $SQL = "INSERT INTO vati (id,amount,uid,tid,alv) VALUES ('".$key."','".$vastasumma."','".$uid."','".$tid."','".$alv."') ON DUPLICATE KEY UPDATE amount='".$vastasumma."', alv='".$alv."'";

    }


  //  error_log($SQL);

    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }


    setTilit($uid, $tid);


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
