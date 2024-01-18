<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once ('config.php');

gettoken();

//echo $TOKEN;

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

if (!empty($rbank))
{

    /* BANK LINK */
    $user_email = $_SESSION['email'];
    $post = [
        'redirect' => $BANKRETURN,
        'institution_id' => $rbank,
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
        "Authorization: Bearer ".$TOKEN
    );



    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);


    $bank_json = json_decode($server_output, true);

//var_dump($bank_json);

    //print "<br/><br/>NORDIGEN BANK ID:". $bank_json['id']."<br/>";

    //$_SESSION['bid'] = $bank_json['id'];

    error_log($server_output);

    $_SESSION['bid'] = $bank_json['id'];
    $_SESSION['link'] = $bank_json['link'];
    $_SESSION['bankname'] = $ubank;

    print $bank_json['link']."";

    curl_close ($ch);



}

?>