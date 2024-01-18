<?php
require_once ('config.php');

$vatid = $_REQUEST['vatid'];

$conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
$query = $conn->prepare("SELECT company_vat FROM puser WHERE company_vat = '" . $vatid. "'");
$query->execute();

if( $query->rowCount() > 0 ){
    echo 'false';
}
else{
    echo 'true';
}
?>


