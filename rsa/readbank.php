<?php
include 'vendor/autoload.php';
include '../config.php';
use \Firebase\JWT\JWT;

if (isset($_REQUEST['code']))
{
    $auth_code = set_safe($_REQUEST['code']);
    $body = json_encode([ 'code' => $auth_code ]);

    $jwt = '';
    if (isset($_SESSION['jwt'])) $jwt = set_safe($_SESSION['jwt']);


    $headers = [
        'Authorization: Bearer ' . $jwt,
        'Content-Type: application/json',
    ];


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
}
?>