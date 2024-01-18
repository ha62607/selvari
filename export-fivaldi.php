<?php
include_once ('./config.php');
$ROW = getUserTransFull($_SESSION['uid']);

$tidarray = array();

foreach ($ROW as $row) {

    $tid = $row['tid'];
    array_push($tidarray,$tid);

}



echo "PVM,TILI,TOSITE,SK1,SK2,SUMMA,SELITE,VEROKANTA,VEROSUMMA".PHP_EOL;;
foreach ($ROW as $row) {

    $boodate = $row['bookingDate'];
    $boodate = preg_replace("/[^a-zA-Z0-9]+/", "", $boodate);
    echo $boodate.",";



    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";
    echo $row[''].",";

}

/*
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=fivaldi.csv");
$fp = fopen('php://output', 'w');

foreach ($ROW as $row) {
fputcsv($fp, $row);
}
fclose($fp);
*/