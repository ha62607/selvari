<?php
require_once 'config.php';

$BANKTOKEN = "a971e5bd-3170-4d73-a792-b43ebff66338";
$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0b2tlbl90eXBlIjoiYWNjZXNzIiwiZXhwIjoxNjU5NTU5OTQ2LCJqdGkiOiJmYTkzZGVmMjgxMTU0OTAxYWIwYzZlMGYzYzU1MjM2ZiIsImlkIjoxMzgzOCwic2VjcmV0X2lkIjoiYWEyZWI3NGUtZTg0NC00ZTQ0LThiMDUtMjBiYWM3NWM0ODY3IiwiYWxsb3dlZF9jaWRycyI6WyIwLjAuMC4wLzAiLCI6Oi8wIl19.DncUzVEc6ILlO_MTgwn1kENDLa4Phz6419E2rgFz3ws";
$access_token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0b2tlbl90eXBlIjoiYWNjZXNzIiwiZXhwIjoxNjYwMzMwMDY1LCJqdGkiOiJjMjFkYjlmYzYxY2E0Nzg2OWJiMDkyODRmNmU5NzU0ZSIsImlkIjoxMzgzOCwic2VjcmV0X2lkIjoiYWEyZWI3NGUtZTg0NC00ZTQ0LThiMDUtMjBiYWM3NWM0ODY3IiwiYWxsb3dlZF9jaWRycyI6WyIwLjAuMC4wLzAiLCI6Oi8wIl19.X9DGro2qXda5cwxsV-iVGHIG5Sj9SwAJllLj1OifsQw";

$access_token = gettoken();

$BANKURL = "https://ob.nordigen.com/api/v2/requisitions/".$BANKTOKEN ."/";

$ACCOUNT = "020ea390-5fe6-4613-bbb7-ac4da800c0a9";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$BANKURL);


$request_headers = array(
    "accept: application/json",
    "Authorization: Bearer ".$access_token
);


curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);




//var_dump($server_output);

print "NORDIGEN BANK ACCOUNT:".  $server_output ;
print "<br/><br/>";

curl_close ($ch);

$bank_json = json_decode($server_output, true);

echo($bank_json['accounts'][0]."<br/><br/>");

echo($bank_json['reference']."<br/><br/>");

echo($bank_json['agreement']."<br/><br/>");

echo($bank_json['link']."<br/><br/>");

?>