<?php
$ENV = 0;

$SITE = "http://localhost/selvari/";
$BANKRETURN = "http://localhost/selvari/bank.php";

$TARGETDIR = "/Users/ha62607/sites/selvari/kuitit/";
$FILEDIR = "/Users/ha62607/sites/selvari/uploads/";

$PDFMAXSIZE = 3000000;

$TEMPPATH = "/Users/ha62607/sites/selvari/temp/";

$SITEPATH = "/Users/ha62607/sites/selvari/";


$dbhost = "127.0.0.1";
$dbport = "3306";
$dbname = "selvari";
$dbuser = "root";
$dbpass = "root";

$SELVARI = true;



if ($ENV == 3) // AWS TEST
{
    $SITE = "https://tamkontoselvari.hannuah.com/";

    $BANKRETURN = "https://tamkontoselvari.hannuah/bank.php";
    $TARGETDIR = "/var/www/KUITIT/";
    $FILEDIR = "/var/www/TIEDOSTOT/";

    $PDFMAXSIZE = 3000000;

    $TEMPPATH = "/var/www/html/tamkonto.com/temp/";

    $SITEPATH = "/var/www/html/tamkonto.com";

    $dbhost = "localhost";
    $dbport = "3306";
    $dbname = "tamkonto";
    $dbuser = "root";
    $dbpass = "JSRy5hebnghwGBVW3";

}


