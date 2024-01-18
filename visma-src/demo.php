<?php

function generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body = null)
{
    $LF = chr(0x0A);
    //$LF = IntlChar::chr(0x0A);

    $bodyMD5 = '';
    $contentyType = '';

    if ($body != null) {
        $bodyMD5 = md5($body);
        $contentyType = 'application/json';
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

    echo ($stringToSign);
    echo ($LF);

    // Hash $stringToSign with the key.
    $signature = hash_hmac('sha256', $stringToSign, $secret, true);

    // Base64 encode the signature.
    $signature = "Fivaldi " . base64_encode($signature);

    return $signature;
}

//$partnerId = getenv('PARTNER_ID');
//$secret = getenv('PARTNER_SECRET');



//TESTI 3
$partnerId = "ws-selvari";
$secret = "sfbVCzbgXCQDuXdBfFyc9AatAXZTqgyY";

$SITE = "https://api.fivaldi.net";
$endpoint = '/customer/api/ping';


$endpoint = '/customer/api/customers';


//$SITE = "https://isvapi.netvisor.fi";
//$endpoint = '/customerlist.nv';



$httpMethod = 'GET';
$body = null;

// Unix timestamp.
$epoch = strval(time());

// Generate the signature.
$signature = generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body);



echo($signature);

echo("\n\n");

// Initialize the cURL request.
$ch = curl_init();

// Setup POST request and/or the headers.
if ($body != null) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
} else {
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Fivaldi-Partner: $partnerId", "X-Fivaldi-Timestamp: $epoch", "Authorization: $signature"));
}

// Set the URL.
curl_setopt($ch, CURLOPT_URL, $SITE . $endpoint);

// Setting CURLOPT_RETURNTRANSFER to true will  return the transfer as a string of the return value of curl_exec() instead of outputting it directly.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request.
$data = curl_exec($ch);

// Get the HTTP status code.
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//Close the cURL request.
curl_close($ch);

//Print the response.
echo ("STATUS:".$httpStatus."\n\n\n");
var_dump($data);

