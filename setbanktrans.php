<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once ('config.php');

$account_ref = $_SESSION['acref'];



$get_account_url = str_replace('BANKIA', $account_ref, $BANKURL );


$request_headers = array(
    "accept: application/json",
    "Authorization: Bearer " . gettoken()
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $get_account_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);

$nordigen_output = json_decode($server_output, true);

//print "NORDIGEN BANK ACCOUNT:" . $nordigen_output['accounts'][0];
$bankid = $nordigen_output['accounts'][0];

//print "<br/><br/>";

curl_close($ch);
$uid = $_SESSION['uid'];
if (!empty($_SESSION['uid']))
{

    updateBank($uid, $bankid);

}

$vatid = $_SESSION['vatid'];

$account_url = str_replace('xxxxx', $bankid , $ACURL);
//error_log("TRURL:".$account_url);

//TRANSACTIONS

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $account_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$nordigen_output = curl_exec($ch);


$bank_json = json_decode($nordigen_output, true);
$booked = $bank_json['transactions']['booked'];
curl_close($ch);

//echo("SIZEOF_BOOKED:".sizeof($booked)."<BR/><BR/><BR/><BR/>");

$bdata = [];
$count = 0;
$booked = array_unique($booked, SORT_REGULAR);

$nodups = [];
for ($i = 0; $i < sizeof($booked); $i++)
{
    //$amount = (float) $booked[$i]['transactionAmount']['amount'];
    //print $booked[$i]['creditorName'];
    //print $booked[$i]['debtorName'];
    //print " " .  $amount;

    foreach (array_keys($booked[$i]) as $key) {

        $bkey = "";
        $bvalue = "";

        $go = true;


        if ($go)
        {

            //echo("KEYS:".sizeof($booked[$i])."<br/>");
            if ($key == 'transactionAmount' || $key == 'debtorAccount' || $key == 'creditorAccount')
            {
                foreach (array_keys($booked[$i][$key]) as $key2)
                {
                    //echo $key2 . " : " . $booked[$i][$key][$key2] . "<br />";
                    $bdata[$i][$key2] = $booked[$i][$key][$key2] ;
                }

            }
            // debtorAccount

            else
            {
                //echo $key . " : " . $booked[$i][$key] . "<br />";
                $bdata[$i][$key] = $booked[$i][$key];
            }

        }
    }

    // Save $bdata = ();
    //echo("Kenttä lkm:".sizeof($bdata[$i])."<br/>");
    /*
    foreach (array_keys($bdata[$i]) as $key) {

        if (is_array($bdata[$i][$key])) {
            //echo($key . ": IS ARRAY<br/>");
        } else {
           // echo($key . ":" . $bdata[$i][$key] . "<br/>");
        }
    }
    */
    $count = saveTrans($uid,$bdata[$i],$bankid,$count,$vatid);
    //echo("<br/><br/><br/>");
    unset($bdata);
    $bdata = [];

}
//$rivit = sizeof(getUserTrans($uid,false));
echo(true);
//saveTrans($uid,$bdata);

?>