<?php
session_start();
require_once ('config.php');

$ok = true;

$uid = set_safe($_SESSION['uid']);
$alias = set_safe($_POST['alias']);
$bban = set_safe($_POST['bban']);


error_log("Alias set 1.0");

if(!empty($uid) && is_int(intval($uid)) )
{

    error_log("Alias set 1.1");


    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    /*
    INSERT INTO t1 (a,b,c) VALUES (1,2,3) ON DUPLICATE KEY UPDATE c=c+1; UPDATE t1 SET c=c+1 WHERE a=1;

    INSERT INTO alias (uid,bban,alias) VALUES ('".$uid."','".$bban."','".$alias."') ON DUPLICATE KEY UPDATE alias='".$alias."'
    */


    $SQL = "INSERT INTO alias (uid,bban,alias) VALUES ('".$uid."','".$bban."','".$alias."') ON DUPLICATE KEY UPDATE alias='".$alias."'";

    error_log($SQL);

    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $db->close();


    getUserAliasList(true);
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
