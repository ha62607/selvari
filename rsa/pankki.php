<?php
include 'vendor/autoload.php';

use \Firebase\JWT\JWT;
// https://www.tamkonto.com/rsa/enabank.php
// http://localhost/tamkonto/rsa/enabank.php


$AUTH_URL = "https://api.enablebanking.com/auth";

$redirect_url = "https://www.tamkonto.com/";

$redirect_url = "http://localhost/tamkonto/";

$redirect_url = "http://localhost/tamkonto/rsa/enabank.php";


function request($url, $headers=[], $post='')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if(!empty($post))
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if(count($headers))
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    return ['body' => curl_exec($ch), 'status' => curl_getinfo($ch, CURLINFO_HTTP_CODE)];
}


// Reading and parsing config file
$config_file = file_get_contents(dirname(__FILE__) . '/config.json');
$config = json_decode($config_file);

// Loading RSA private key
$key_path = $config->keyPath;
$rsa_key = file_get_contents(dirname(__FILE__) . '/' . $key_path);

// Creating JWT
$jwt_header = [ 'typ' => 'JWT', 'alg' => 'RS256', 'kid' => $config->applicationId ];
$payload = [
    'iss' => 'enablebanking.com',
    'aud' => 'api.enablebanking.com',
    'iat' => time(),
    'exp' => time() + 3600
];
$jwt = JWT::encode($payload, $rsa_key, 'RS256', $config->applicationId);
echo($jwt);
echo("<br/><br/><br/><br/><br/><br/><br/>");


$headers = [
    'Authorization: Bearer ' . $jwt,
    'Content-Type: application/json',
];

// Requesting application details
$r = request('https://api.enablebanking.com/application', $headers);
if($r['status'] === 200)
{
    $app = json_decode($r['body']);
    echo '<h3>Application details:</h3>';
    print_r($app);

    echo("<br/><br/><br/><br/><br/><br/><br/>");

} else {
    exit('Error response #' . $r['status'] . ':' . $r['body']);
}


// Requesting available ASPSPs
$r = request('https://api.enablebanking.com/aspsps', $headers);
if($r['status'] === 200)
{
    $aspsps = json_decode($r['body']);
    echo '<h3>Available ASPSPs:</h3>';
    print "<pre>";
    print_r($aspsps);
    print "</pre>";
    echo("<br/><br/><br/><br/><br/><br/><br/>");
} else {
    exit('Error response #' . $r['status'] . ':' . $r['body']);
}


// Starting authorization
$valid_until = time() + 2 * 7 * 24 * 60 * 60;
$body = [
    'access' => [ 'valid_until' => date('c', $valid_until) ],
    'aspsp' => [ 'name' => 'Mock ASPSP', 'country' => 'FI' ], // { name: aspsps[0]['name'], country: aspsps[0]['country'] },
    'state' => 'random',
    'redirect_url' => $redirect_url,
    'psu_type' => 'business',
    "credentials_autosubmit": true,
"language": "fi",
];

$r = request('https://api.enablebanking.com/auth', $headers, json_encode($body));
if($r['status'] == 200)
{
    $auth_url = json_decode($r['body'])->url;
    echo '<h3>To authenticate open URL </h3>' . $auth_url . PHP_EOL;
    echo("<br/><br/><br/><br/><br/><br/><br/>");
} else {
    exit('Error response 1.o #' . $r['status'] . ':' . $r['body']);
}



/*

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$TURL);


$request_headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Host: api.enablebanking.com",
    "Authorization: Bearer ".$jwt
);


curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);

echo($server_output);
*/
?>