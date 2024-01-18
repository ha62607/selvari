<?php

include 'vendor/autoload.php';

use \Firebase\JWT\JWT;

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

echo("<h3>JWT:</h3>");
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
    print_r($aspsps);
    echo("<br/><br/><br/><br/><br/><br/><br/>");
} else {
    exit('Error response #' . $r['status'] . ':' . $r['body']);
}

// Starting authorization
$valid_until = time() + 2 * 7 * 24 * 60 * 60;
$body = [
    'access' => [ 'valid_until' => date('c', $valid_until) ],
    'aspsp' => [ 'name' => 'Danske Bank', 'country' => 'FI' ], // { name: aspsps[0]['name'], country: aspsps[0]['country'] },
    'state' => 'random',
    'redirect_url' => $app->redirect_urls[0],
    'psu_type' => 'business'
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

// Reading auth code and creating user session
$auth_code = readline('Enter value of code parameter from the URL you were redirected to: ');

$body = json_encode([ 'code' => $auth_code ]);
$r = request('https://api.enablebanking.com/sessions', $headers, $body);
if($r['status'] === 200)
{
    $session = json_decode($r['body']);
    echo '<h3>New user session has been created:</h3>';

    print_r($session);
    echo("<br/><br/><br/><br/><br/><br/><br/>");
} else {
    exit('Error response #' . $r['status'] . ':' . $r['body']);
}
$account_uid = $session->accounts[0]->uid;

// Retrieving account balances
$r = request('https://api.enablebanking.com/accounts/' . $account_uid . '/balances', $headers);
if($r['status'] === 200)
{
    $balances = json_decode($r['body'])->balances;
    echo '<h3>Balances: </h3>h3>';
    print_r($balances);
    echo("<br/><br/><br/><br/><br/><br/><br/>");
} else {
    exit('Error response #' . $r['status'] . ':' . $r['body']);
}

// Retrieving account transactions (since yesterday)
$continuation_key = null;
do
{
    $params = '?date_from=' . date('Y-m-d', strtotime('-1 day', time()));
    if($continuation_key)
    {
        $params .= '&continuation_key=' . $continuation_key;
    }
    $r = request('https://api.enablebanking.com/accounts/' . $account_uid . '/transactions' . $params, $headers);
    if($r['status'] === 200)
    {
        $rest_data = json_decode($r['body']);
        $transactions = $rest_data->transactions;
        echo '<h3>Transactions:</h3>';
        print_r($transactions);
        if(isset($rest_data->continuation_key) && $rest_data->continuation_key)
        {
            $continuation_key = $rest_data->continuation_key;
            echo 'Going to fetch more transaction with continaution key ' . $continuation_key;
        } else {
            echo 'No continuation key. All transactions were fetched' . PHP_EOL;
            break;
        }
    } else {
        exit('Error response #' . $r['status'] . ':' . $r['body']);
    }
}
while(true);
