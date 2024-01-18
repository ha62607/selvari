<?php
require_once ('config.php');

$email = $_REQUEST['email'];

$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$query = $conn->prepare("SELECT email FROM puser WHERE email = '" . $email . "'");
$query->execute();

if( $query->rowCount() > 0 ){
    echo 'false';
}
else{
    echo 'true';
} ?>


