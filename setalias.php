<?php
session_start();
require_once ('config.php');

$ok = true;

$uid = set_safe($_SESSION['uid']);
$tila = set_safe($_POST['tila']);



if (!empty($tila) && $tila === 'true') $tila = '1';
else $tila = '0';


if(!empty($uid) && is_int(intval($uid)) )
{

    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "UPDATE account SET account.alias = '".$tila."' WHERE uid='".$uid."'";
    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    if ($tila === '1')
    {
        $_SESSION['alias'] = 'true';
    }
    else
    {
        $_SESSION['alias'] = 'false';
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
