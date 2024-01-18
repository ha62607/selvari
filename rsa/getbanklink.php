<?php
include 'vendor/autoload.php';

include '../config.php';

use \Firebase\JWT\JWT;


$AUTH_URL = "https://api.enablebanking.com/auth";

$redirect_url = "https://www.tamkonto.com/";

$redirect_url = "https://www.tamkonto.com/rsa/readbank.php";


$rbank  = "";

if (!empty($_REQUEST['bank']))
{
    $rbank = $_REQUEST['bank'];
    $ubank = $_REQUEST['bank'];

    if ($rbank == 'danske' )
    {
        $rbank = $danske;
    }

    elseif ($rbank == 'handel' )
    {
        $rbank = $handel;
    }

    elseif ($rbank == 'nordea' )
    {
        $rbank = $nordea;
    }

    elseif ($rbank == 'omasp' )
    {
        $rbank = $omasp;
    }

    elseif ($rbank == 'op' )
    {
        $rbank = $op;
    }

    elseif ($rbank == 'pop' )
    {
        $rbank = $pop;
    }

    elseif ($rbank == 'saasto' )
    {
        $rbank = $saasto;
    }

    elseif ($rbank == 'spankki' )
    {
        $rbank = $spankki;
    }

    elseif ($rbank == 'alands' )
    {
        $rbank = $alands;
    }

}

if (!empty($rbank)) {


// Reading and parsing config file
    $config_file = file_get_contents(dirname(__FILE__) . '/config.json');
    $config = json_decode($config_file);

// Loading RSA private key
    $key_path = $config->keyPath;
    $rsa_key = file_get_contents(dirname(__FILE__) . '/' . $key_path);

// Creating JWT
    $jwt_header = ['typ' => 'JWT', 'alg' => 'RS256', 'kid' => $config->applicationId];
    $payload = [
        'iss' => 'enablebanking.com',
        'aud' => 'api.enablebanking.com',
        'iat' => time(),
        'exp' => time() + 3600
    ];
    $jwt = JWT::encode($payload, $rsa_key, 'RS256', $config->applicationId);

    $_SESSION['jwt'] = $jwt;


    $headers = [
        'Authorization: Bearer ' . $jwt,
        'Content-Type: application/json',
    ];


// Starting authorization
    $valid_until = time() + 2 * 7 * 24 * 60 * 60;
    $body = [
        'access' => ['valid_until' => date('c', $valid_until)],
        'aspsp' => ['name' => $rbank, 'country' => 'FI'], // { name: aspsps[0]['name'], country: aspsps[0]['country'] },
        'state' => 'random',
        'redirect_url' => $redirect_url,
        'psu_type' => 'business',
        'credentials_autosubmit' => 'true',
        'language' => 'fi'
    ];

    $r = request('https://api.enablebanking.com/auth', $headers, json_encode($body));
    if ($r['status'] == 200) {
        $auth_url = json_decode($r['body'])->url;
        echo $auth_url . PHP_EOL;
    } else {
        exit('Error response 1.o #' . $r['status'] . ':' . $r['body']);
    }

}

?>