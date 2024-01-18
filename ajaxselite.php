<?php
session_start();
require_once ('config.php');

$ok = true;

$uid = set_safe($_SESSION['uid']);
$tid = set_safe($_POST['tid']);
$transid = set_safe($_POST['transid']);
$selite = set_safe($_POST['selite']);
$yrit = set_safe($_SESSION['vatid']);


$aika = gettime();
$ip = getUserIP();



if(!empty($selite) && strlen($selite) > 9 )
{
    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "UPDATE trans SET trans.selite = '".$selite."', kuitit='1' WHERE tid='".$tid."'";
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
