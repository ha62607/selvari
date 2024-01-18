<?php
session_start();
require_once ('config.php');
if(empty($_SESSION['uid']))
{
    header('Location: ./index.php?logout=true');
}

$kid = set_safe($_REQUEST['kuitti']);
$uid = set_safe($_SESSION['uid']);

$SQL = "SELECT tiedosto FROM kuitit WHERE uid = '" . $uid  . "' AND kid = '" . $kid. "'";
error_log($SQL);
$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$query = $conn->prepare($SQL);
$query->execute();

error_log("Logging... UID:".$uid." KID:".$kid);

$kuvatiedosto = "";
while ($row  = $query->fetch())
{
    $kuvatiedosto = $row['tiedosto'];

}

error_log("KUVA:".$kuvatiedosto);

$conn = null;

$array = explode('.', $kuvatiedosto );
$extension = strtolower(end($array));
if($extension === 'jpg' || $extension  === 'jpeg')
{
    header('Content-Type: image/jpeg');
    $empty = false;

}
else if($extension === 'png')
{
    header('Content-Type: image/png');
    $empty = false;
}

else if($extension === 'pdf')
{
    header('Content-Type: application/pdf');
    $empty = false;
}

echo(file_get_contents($TARGETDIR . $kuvatiedosto));

 ?>
