<?php
session_start();
require_once ('config.php');

$email = $_POST['email'];
$pass = $_POST['pass'];

$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$query = $conn->prepare("SELECT uid, email, company_name, company_vat,vatid, fullname FROM puser WHERE email = '" . $email . "' AND pass = '" . $pass . "' ");
$query->execute();

error_log("Logging...");


$row  = $query->fetch();

if( $query->rowCount() > 0 )
{

    $_SESSION['logged'] = '1';
    $_SESSION['email'] = $row['email'];
    $_SESSION['company_vat'] = $row['company_vat'];
    $_SESSION['vatid'] = $row['vatid'];
    $_SESSION['company'] = $row['company_name'];
    $_SESSION['uid'] = $row['uid'];
    $_SESSION['fullname'] = $row['fullname'];
    error_log("SESSION:".$_SESSION['logged']);
    error_log("SESSION:".$_SESSION['vatid']);

    echo 'true';

    $query = $conn->prepare("SELECT alias FROM account WHERE uid = '" . set_safe($_SESSION['uid']) . "'");
    $query->execute();



    $row  = $query->fetch();

    if( $query->rowCount() > 0 )
    {
        $s = $row['alias'];
        if ($s === '1')
        {
            $_SESSION['alias'] = 'true';
        }
        else
        {
            $_SESSION['alias'] = 'false';
        }

    }



}


else{
    echo 'false';
} ?>
