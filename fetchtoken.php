<?php

include('config.php');


$NEW_TOKEN = "https://ob.nordigen.com/api/v2/token/new/";

$BANK = "https://ob.nordigen.com/api/v2/institutions/?country=fi";

$REQ = "https://ob.nordigen.com/api/v2/requisitions/";

$post = [
    'secret_id' => 'aa2eb74e-e844-4e44-8b05-20bac75c4867',
    'secret_key' => 'f401077b4f4aa72f09e5532c7ac31c28b9668bf4f12dfec5e3174e1f1267926f7c5d22b1bd4858d60cbf1cb0d74d1dc1be44eb77f72115b36abd5344901db9dd'
];

$data =json_encode($post);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$NEW_TOKEN);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


$request_headers = array(
    "accept: application/json",
    "Content-Type: application/json"
);



curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);




//var_dump($server_output);

print "NORDIGEN:".  $server_output ;

curl_close ($ch);

print "<br/><br/>";

$json = json_decode($server_output, true);

print "ACCESS:<br/>".$json['access'];
$access_token = $json['access'];

$ch = curl_init();

$request_headers = array(
    "accept: application/json",
    "Authorization: Bearer ".$access_token
);

curl_setopt($ch, CURLOPT_URL,$BANK);

curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

print "<br/><br/>";

print "BANK:".  $server_output ;

print "<br/><br/>";

$bank_json = json_decode($server_output, true);

print "<br/><br/>";

print sizeof($bank_json);
print "<br/><br/>";

$fin_banks = array('DANSKEBANK_BUSINESS_DABAFIHH','HANDELSBANKEN_CORPORATE_HANDFIHH','NORDEA_BUSINESS_NDEAFIHH','OMASP_OMSAFI2S','OP_OKOYFIHH','POPPANKKI_POPFFI22','S_PANKKI_SBANFIHH','SAASTOPANKKI_ITELFIHH','ÅLANDSBANKEN_AABAFI22');

foreach($bank_json as $value) {
    //echo "$key = $val<br>";
    $curr_bank_json = json_decode($value, true);
    //print sizeof($curr_bank_json);

    $bank_name = $value['name'];
    $bank_id = $value['id'];

    if(in_array($bank_id, $fin_banks))
    {
    echo "".$value['id']."<br/>";
    echo "".$value['name']."<br/>";
    echo "<img src='".$value['logo']."' style='max-width:150px;'/><br/><br/>";
    }
}

print "<br/><br/>";

curl_close ($ch);

/* BANK LINK */

$post = [
    'redirect' => 'http://localhost/tamkonto/fetchbank.php',
    'institution_id' => 'DANSKEBANK_BUSINESS_DABAFIHH',
    'user_language' => 'FI'
    ];

$data =json_encode($post);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$REQ);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


$request_headers = array(
    "accept: application/json",
    "Content-Type: application/json",
    "Authorization: Bearer ".$access_token
);



curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);




//var_dump($server_output);

print "NORDIGEN BANK LINK:".  $server_output ;

curl_close ($ch);


?>