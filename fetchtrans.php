<?php
require_once 'config.php';
$BANKTOKEN = "a971e5bd-3170-4d73-a792-b43ebff66338";
$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0b2tlbl90eXBlIjoiYWNjZXNzIiwiZXhwIjoxNjU5ODAzODU2LCJqdGkiOiIwMzM4MWQ0NTFjNzk0NGZlOTVmZjYyMWJkMWEwMzQ3YiIsImlkIjoxMzgzOCwic2VjcmV0X2lkIjoiYWEyZWI3NGUtZTg0NC00ZTQ0LThiMDUtMjBiYWM3NWM0ODY3IiwiYWxsb3dlZF9jaWRycyI6WyIwLjAuMC4wLzAiLCI6Oi8wIl19.RyG1VHTU_PAYqoJdl7kWjmQfBhfn9WncOOi6bD3EmWY";

$ACCOUNT = "020ea390-5fe6-4613-bbb7-ac4da800c0a9";

$BANKURL = "https://ob.nordigen.com/api/v2/accounts/".$ACCOUNT."/transactions/";

$access_token = gettoken();


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $BANKURL);


$request_headers = array(
    "accept: application/json",
    "Authorization: Bearer " . $access_token
);


curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);


//var_dump($server_output);

print "NORDIGEN BANK ACCOUNT:" . $server_output;
print "<br/><br/>";

curl_close($ch);


$bank_json = json_decode($server_output, true);

print sizeof($bank_json);


print "<br/><br/>";

$booked = $bank_json['transactions']['booked'];

print sizeof($booked);

print "<br/><br/>";

for ($i = 0; $i < sizeof($booked); $i++)
{
    //$amount = (float) $booked[$i]['transactionAmount']['amount'];
    print "<br/><br/><br/>";
    //print $booked[$i]['creditorName'];
    //print $booked[$i]['debtorName'];
    //print " " .  $amount;

    foreach (array_keys($booked[$i]) as $key) {

        if ($key == 'transactionAmount' || $key == 'debtorAccount' || $key == 'creditorAccount')
        {
            foreach (array_keys($booked[$i][$key]) as $key2)
            {
                echo $key2 . " : " . $booked[$i][$key][$key2] . "<br />";
            }

        }
        // debtorAccount



        else
        {
            echo $key . " : " . $booked[$i][$key] . "<br />";
        }
    }


}

/*
$bank_json = json_decode($server_output, true);

echo($bank_json['accounts'][0] . "<br/><br/>");

echo($bank_json['reference'] . "<br/><br/>");

echo($bank_json['agreement'] . "<br/><br/>");

echo($bank_json['link'] . "<br/><br/>");

*/

?>