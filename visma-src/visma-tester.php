<?php

require_once '../config.php';

$GLOBALS['partnerId'] = "ws-selvari";
$GLOBALS['secret'] = "sfbVCzbgXCQDuXdBfFyc9AatAXZTqgyY";

$partnerId = "ws-selvari";
$secret = "sfbVCzbgXCQDuXdBfFyc9AatAXZTqgyY";
$SITE = "https://api.fivaldi.net";


function generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body = null, $c_type = 'application/json')
{
    global $partnerId, $secret;
    $LF = chr(0x0A);
    //$LF = IntlChar::chr(0x0A);

    $bodyMD5 = '';
    $contentyType = '';

    if ($body != null) {
        $bodyMD5 = md5($body);
        $contentyType = $c_type;
    }

    $stringToSign = $httpMethod . $LF . $bodyMD5 . $LF . $contentyType . $LF;
    $stringToSign .= 'x-fivaldi-partner:' . $partnerId . $LF;
    $stringToSign .= 'x-fivaldi-timestamp:' . $epoch . $LF;

    // If the endpoint contains a ?, but has no params, remove it.
    if (strpos($endpoint, '?') !== false && strpos($endpoint, '?') == strlen($endpoint) - 1) {
        $endpoint = substr($endpoint, 0, strlen($endpoint) - 1);
    }

    // If the URL contains a querystring, remove everything after it, then push.
    if (strpos($endpoint, '?') !== false) {
        $stringToSign .= substr($endpoint, 0, strpos($endpoint, '?'));
    }
    // Otherwise just push it, as is.
    else {
        $stringToSign .= $endpoint;
    }

    // Get the query string of the URL, if it exists.
    $queryString = parse_url($endpoint, PHP_URL_QUERY);

    // If the query string exists, append it to the $stringToSign.
    if ($queryString) {
        $stringToSign .= $LF . $queryString;
    }

    //echo ($stringToSign);
    //echo ($LF);

    // Hash $stringToSign with the key.
    $signature = hash_hmac('sha256', $stringToSign, $secret, true);

    // Base64 encode the signature.
    $signature = "Fivaldi " . base64_encode($signature);

    return $signature;
}


function getdata($endpoint, $httpMethod = 'GET')
{
    global $partnerId, $secret, $SITE;

    $epoch = strval(time());
    $body = null;
    $partnerId = $GLOBALS['partnerId'];
    $secret = $GLOBALS['secret'];
    $signature = generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body);



    $ch = curl_init();

    if ($body != null) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
    }

    echo "Fetching data from endpoint: ".$SITE . $endpoint."\n\n";

    curl_setopt($ch, CURLOPT_URL, $SITE . $endpoint);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    echo ("\nSTATUS:".$httpStatus."\n\n");
    return $data;


}



function postdata($endpoint, $body = null, $httpMethod = 'POST')
{
    global $partnerId, $secret, $SITE;

    $epoch = strval(time());
    $partnerId = $GLOBALS['partnerId'];
    $secret = $GLOBALS['secret'];
    $signature = generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body);



    $ch = curl_init();

    if ($body != null) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
    } else {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
    }

    echo "Posting data to endpoint: ".$SITE . $endpoint."\n\n";

    curl_setopt($ch, CURLOPT_URL, $SITE . $endpoint);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $data = curl_exec($ch);

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    echo ("\nSTATUS:".$httpStatus."\n\n");
    return $data;


}

function uploadFile($endpoint,$filepath,$journalNo,$voucherType)
{
    global $partnerId, $secret, $SITE;


    if (file_exists($filepath))
    {
        echo "File on olemassa: ".$filepath. "\n\n";
        $realpath = realpath($filepath);
        echo $realpath . "\n\n";
    }
    else
    {
        echo "File EI OE olemassa: ".$filepath. "\n\n";
        return;

    }
    

    $epoch = strval(time());
    $partnerId = $GLOBALS['partnerId'];
    $secret = $GLOBALS['secret'];
    $httpMethod = 'POST';
    $filedata = fopen($realpath, 'rb');
    $size =  filesize($realpath);
    $contents = fread($filedata, $size);
    fclose($filedata);
    $body = array( 'file' => base64_encode($contents));
    $body = json_encode($body);

    $content_length = $size; 

    $signature = generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body,'multipart/form-data');

    echo "Signature: ".$signature."\n\n";




    $upload_url = $SITE . $endpoint . "?journalNo=".$journalNo."&voucherType=".$voucherType;

    echo $upload_url . "\n\n";

    $header =  array("Content-Type: multipart/form-data;","Content-Length: $content_length","Content-Disposition: form-data; name='file'; filename='nainen.jpg'", "X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature");
    print_r($header);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_URL, $upload_url );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);
    echo ("\nSTATUS:".$httpStatus."\n\n");
    return $data;

}

//https://api.fivaldi.net/customer/api/companies/4CAB3A6D24485BA1E0531100820AA764/bookkeeping/vouchers?startDate=2023-11-01&endDate=2023-11-30

//Data endpoint

$data_endpoint = "/customer/api/companies/4CAB3A6B56375BA1E0531100820AA764/bookkeeping/vouchers";

$code_endpoint = "/customer/api/companies/4CAB3A6B56375BA1E0531100820AA764/bookkeeping/vatCodes";


$file_endpoint = "/customer/api/companies/4CAB3A6B56375BA1E0531100820AA764/bookkeeping/vouchers/addAttachments";


/*
{
  "voucherTypeId": 0,
  "originalVoucherNumber": "string",
  "voucherDate": "string",
  "bookkeepingMonth": 209912,
  "vatType": "CALCULATED",
  "validateEntrySum": true,
  "voucherEntries": [
    {
      "amount": 0,
      "accountNumber": "string",
      "balanceCode": "st",
      "dimensionValue1": "string",
      "dimensionValue2": "string",
      "dimensionValue3": "string",
      "dimensionValue4": "string",
      "description": "string",
      "vat": {
        "vatCode": "string",
        "vatAmount": 0,
        "account": "string",
        "account2": "string"
      }
    }
  ]
}*/


$ar = array();

$ar['voucherTypeId'] = 8;
$ar['originalVoucherNumber'] = "8231100008";
$ar['voucherDate'] = "2023-11-30";
$ar['bookkeepingMonth'] = 202311;
$ar['vatType'] = "CALCULATED";
$ar['validateEntrySum'] = true;


//Entries

$entry1 = array();
$entry1['amount'] = 150;
$entry1['accountNumber'] = "1081";
$entry1['balanceCode'] = "st";
$entry1['description'] = "Selite 1";
$entry1_vat = array();
$entry1_vat['vatCode'] = "v";
$entry1_vat['vatAmount'] = 29.03;
$entry1_vat['account'] = "2939";
$entry1_vat['account2'] = "";
$entry1['vat'] = $entry1_vat;

$entry2 = array();
$entry2['amount'] = 450;
$entry2['accountNumber'] = "1081";
$entry2['balanceCode'] = "st";
$entry2['description'] = "Selite 2";
$entry1_vat = array();
$entry2_vat['vatCode'] = "v";
$entry2_vat['vatAmount'] = 87.1;
$entry2_vat['account'] = "2939";
$entry2_vat['account2'] = "";
$entry2['vat'] = $entry2_vat;


$entry3 = array();
$entry3['amount'] = 99.75;
$entry3['accountNumber'] = "1081";
$entry3['balanceCode'] = "st";
$entry3['description'] = "Selite 3";
$entry3_vat = array();
$entry3_vat['vatCode'] = "v";
$entry3_vat['vatAmount'] = 19.31;
$entry3_vat['account'] = "2939";
$entry3_vat['account2'] = "";
$entry3['vat'] = $entry3_vat;


$entry4 = array();
$entry4['amount'] = 50.25;
$entry4['accountNumber'] = "1081";
$entry4['balanceCode'] = "st";
$entry4['description'] = "Selite 4";
$entry3_vat = array();
$entry4_vat['vatCode'] = "v";
$entry4_vat['vatAmount'] = 9.72;
$entry4_vat['account'] = "2939";
$entry4_vat['account2'] = "";
$entry4['vat'] = $entry4_vat;


$entry5 = array();
$entry5['amount'] = -750;
$entry5['accountNumber'] = "1081";
$entry5['balanceCode'] = "st";
$entry5['description'] = "Selite 5";
$entry3_vat = array();
$entry5_vat['vatCode'] = "v";
$entry5_vat['vatAmount'] = -145.16;
$entry5_vat['account'] = "2939";
$entry5_vat['account2'] = "";
$entry5['vat'] = $entry5_vat;



$ar['voucherEntries'][0] = $entry1;
$ar['voucherEntries'][1] = $entry2;
$ar['voucherEntries'][2] = $entry3;
$ar['voucherEntries'][3] = $entry4;
$ar['voucherEntries'][4] = $entry5;




$data = json_encode($ar);

echo "\n\n";
$ok = false;
if ($data && $ok)
{
    print_r($data);
    $data = postdata($data_endpoint, $data);
   // $data = get_object_vars($data);
    echo "Got: ".$data;
}

echo "\n\n";

//echo postdata($data_endpoint, $data);
//Let's add the file

$file = "../kuitit/nainen.jpg";


$filenames = array($file);

//$file_data = base64_encode($file); 

//echo $file_data;

$journalNo = "8231100008";
//$journalNo = "8989196";

$voucherType  = 8;

$ok = true;



if($ok)
{
    $res = uploadFile($file_endpoint,$file,$journalNo,$voucherType);

    echo $res;
    
}


//$file_endpoint
//$data 
/*

$codes = getdata($code_endpoint);

print_r($codes);
*/