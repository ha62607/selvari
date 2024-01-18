<?php
session_start();

$nologin = true;

include 'vendor/autoload.php';
include '../config.php';

use \Firebase\JWT\JWT;


$account_uid = null;
$headers = [];

?>
<a href="https://tamkonto.com/">Tamkonto.com</a><br/><br/><hr/>
<?php

$jwt = '';
if (isset($_SESSION['jwt'])) $jwt = set_safe($_SESSION['jwt']);

if (isset($_REQUEST['code']))
{
    $auth_code = $_REQUEST['code'];






    echo("<h3>Auth code:</h3>");
    echo($auth_code);
    echo("<hr/><hr/><br/><br/>");


    $headers = [
        'Authorization: Bearer ' . $jwt,
        'Content-Type: application/json',
    ];


    // Reading auth code and creating user session
    //$auth_code = readline('Enter value of code parameter from the URL you were redirected to: ');

    $body = json_encode([ 'code' => $auth_code ]);
    $r = request('https://api.enablebanking.com/sessions', $headers, $body);

    $account_uid = null;
    echo("<pre>");
    print_r($r);
    echo("</pre>");
    if($r['status'] === 200)
    {
        $session = json_decode($r['body']);
        echo '<h3>New user session has been created:</h3>';

        echo("<pre>");
        print_r($session);
        echo("</pre>");

        echo("<hr/><br/><br/><br/>");
    } else {
        exit('Error response #' . $r['status'] . ':' . $r['body']);
    }

    $account_uid = $session->accounts[0]->uid;
    echo("<hr/><br/>AU:".$account_uid."<br/><hr/>");

}

$account_uid = '4e82fba7-df3e-4f60-91b6-7d05c677723c';

$headers = [
    'Authorization: Bearer ' . $jwt,
    'Content-Type: application/json',
];



if($account_uid)
{
//AU:    c10df6b6-1bc1-41a6-af22-dff9a9938048
// Retrieving account balances
    $r = request('https://api.enablebanking.com/accounts/' . $account_uid . '/balances', $headers);
    if($r['status'] === 200)
    {
        $balances = json_decode($r['body'])->balances;
        echo '<h3>Balances: </h3>h3>';
        echo("<pre>");
        print_r($balances);
        echo("</pre>");

        echo("<hr/><br/><br/><br/>");
    } else {
        exit('Error response #' . $r['status'] . ':' . $r['body']);
    }

// Retrieving account transactions (since yesterday)
    $continuation_key = null;
    $i = 0;
    do
    {
        $params = '?date_from=' . date('Y-m-d', strtotime('-30 day', time()));
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
            echo("<pre>");
            print_r($transactions);
            echo("</pre>");
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
        $i++;
    }
    while(true);




}


?>