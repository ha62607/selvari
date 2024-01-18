<?php session_start()?>
<?php
require_once ('config.php');

$SQL = "select tilinro, tilinimi,sanat from tilu order by tilinro asc";

error_log($SQL);

$conn= new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if($conn->connect_error){
    die("Unable to connect database: " . $db->connect_error);
}

$stmt = $conn->prepare($SQL);
$stmt->execute();

$result = $stmt->get_result();
$i = 0;

$lista = '';

echo "var tilit = {";

while ($row = $result->fetch_assoc())
{
    $tilinro = utf8_encode($row['tilinro']);
    $tilinimi = utf8_encode($row['tilinimi']);

    $lista .=  '"'.$tilinro.'": " ('.$tilinro.')   '.$tilinimi.'",';

}

$lista = rtrim($lista, ",");

echo($lista);

$stmt->close();
$conn->close();
echo("}");

?>