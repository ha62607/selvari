<?php
session_start();
require_once ('config.php');
$tid = set_safe($_REQUEST['tid']);
$uid = $_SESSION['uid'];

if (!empty($uid))
{
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare("SELECT * FROM trans WHERE tid = '" . $tid . "' AND uid =  '".$uid."'");
    $query->execute();
    $data = array();

    if ($query->rowCount() > 0){
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $data['tid'] = $row['tid'];
        $data['tamid'] = $row['tamid'];
        $data['selite'] = $row['selite'];
        $data['additionalInformation'] = $row['additionalInformation'];
        $data['bookingDate'] = $row['bookingDate'];
        $data['creditorName'] = $row['creditorName'];
        $data['debtorName'] = $row['debtorName'];
        $data['entryReferenc'] = $row['entryReferenc'];
        $data['selite'] = $row['selite'];
        $data['selite'] = $row['selite'];
        $data['selite'] = $row['selite'];
        $data['selite'] = $row['selite'];
        $data['selite'] = $row['selite'];
        $data['selite'] = $row['selite'];

    }



    echo json_encode($data);


} ?>