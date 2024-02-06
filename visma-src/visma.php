<?php

require_once '../config.php';

$GLOBALS['partnerId'] = "ws-selvari";
$GLOBALS['secret'] = "sfbVCzbgXCQDuXdBfFyc9AatAXZTqgyY";

$partnerId = "ws-selvari";
$secret = "sfbVCzbgXCQDuXdBfFyc9AatAXZTqgyY";
$SITE = "https://api.fivaldi.net";



function saveCompany($data)
{
    global $dbhost, $dbname, $dbuser, $dbpass;

    $f_cuid = $data['cuid'];
    $f_cuid = sanitize_sql($f_cuid);
    $f_companyId = sanitize_sql($data['companyId']);
    $f_databaseId = sanitize_sql($data['databaseId']);
    $f_customerId = sanitize_sql($data['customerId']);
    $f_changeTime = sanitize_sql($data['changeTime']);

    $email = sanitize_sql($data['email']);
    $pass = generatePassword(mt_rand(8, 12));
    $fullname = sanitize_sql($data['name1']);
    $firstname = "Asiakas";
    $lastname = "Pluscom";
    $company_name = sanitize_sql($data['name1']);

    //businsseId to universal format

    $it_vatid = convertToInternationalVatFormat($data['businessId']);

    $vatid = sanitize_sql($it_vatid);
    $company_vat = sanitize_sql($data['businessId']);
    $company_address = sanitize_sql($data['streetAddress']);
    $zipdata = separateZipcodeAndZiparea($data['postalAddress']);
    $zipcode = sanitize_sql($zipdata[0]);
    $ziparea = sanitize_sql($zipdata[1]);

    $ip_now = getUserIP();
    $created = 'CURRENT_TIMESTAMP';
    $lastvisit = 'CURRENT_TIMESTAMP';
    $lastvisit_ip = $ip_now;
    $create_ip = $ip_now;

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname );

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if cuid already exists in the database
    $query = "SELECT f_cuid FROM puser WHERE f_cuid = '$f_cuid'";
    $result = mysqli_query($conn, $query);

    // If cuid already exists, return false
    if (mysqli_num_rows($result) > 0) {
        mysqli_close($conn);
        return true;
    }

    // Insert the values into the database
    $query = "INSERT INTO puser (f_cuid, f_companyId, f_databaseId, f_customerId, f_changeTime, email, pass, fullname, firstname, lastname, company_name, vatid, company_vat, company_address, zipcode, ziparea, lastvisit_ip, create_ip  ) 
              VALUES ('$f_cuid','$f_companyId', '$f_databaseId', '$f_customerId', '$f_changeTime' ,'$email', '$pass', '$fullname', '$firstname', '$lastname', '$company_name', '$vatid', '$company_vat', '$company_address', '$zipcode', '$ziparea', '$lastvisit_ip', '$create_ip')";

    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return true;
    } else {
        mysqli_close($conn);
        return false;
    }


}


function saveVoucher($VOUCHER, $uid)
{
    global $dbhost, $dbname, $dbuser, $dbpass;

    //$entries = $voucher_data['voucherEntries'][0];
    //$accountnum = get_object_vars($entries);

    print_r($VOUCHER);
    //."_".$VOUCHER['invoiceNumber']
    $tamid = $VOUCHER['voucherNumber'];
    $account = "fivaldi";


    echo "\n TAMID: ".$tamid."\n";
    echo "\n UID: ".$uid."\n";

    $bookingDate = $VOUCHER['voucherDate'];
    echo "\n DATE: ".$bookingDate."\n";

    $date = new DateTime($bookingDate);
    $boodate = $date->format('ymd');
    $boomonth = $date->format('m');
    $booyear = $date->format('y');
    $bootimestamp = $date->getTimestamp();


    $entry = get_object_vars($VOUCHER['voucherEntries'][0]);

    $name = $entry['description'];
    $accountNumber = $entry['accountNumber'];

    $invoiceNumber = $VOUCHER['invoiceNumber'];

    $amount = $entry['amount'];

    echo "\n NAME: ".$name."\n";

    $uniquekey = $tamid. "X".$invoiceNumber;


    //creditorName


    //debtorName

    //entryReference

    //remittanceInformationUnstructured

    //transactionId

    //valueDate

    //amount   -  voucherEntries
    $amount = $entry['amount'];
    echo "\n AMOUNT: ".$amount."\n";


    //credit

    //debit

    //boodate

    //boostamp

    //boomonth

    //booyear




    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname );

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT tamid FROM trans WHERE tamid = '$uniquekey'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        mysqli_close($conn);
        return true;
    }

    $test = (float) $amount;

    $debit = 0;
    $credit = 0;
    if($test < 0) $debit = 1;
    else $credit = 0;

    $creditorName = '';
    $debtorName = '';
    if($test < 0) $creditorName = $name;
    else $debtorName = $name;


    // Insert the values into the database
    $query = "INSERT INTO trans ( uid, tamid, additionalInformation, bookingDate, creditorName, debtorName, entryReference,remittanceInformationUnstructured,transactionId, valueDate, amount, credit, debit, boodate, boostamp, boomonth, booyear, status ) 
              VALUES ('$uid', '$uniquekey', '$invoiceNumber', '$boodate', '$creditorName', '$debtorName', '$tamid', '$name', '$uniquekey', '$boodate', '$amount', '$credit', '$debit', '$boodate', '$bootimestamp', '$boomonth', '$booyear','0')";




    if (mysqli_query($conn, $query)) {
        mysqli_close($conn);
        return true;
    } else {
        mysqli_close($conn);
        return false;
    }

}

function get_company_uid($cuid)
{
    $company_cuid = sanitize_sql($cuid);
    $sql = "select uid as c from puser where f_cuid = '$company_cuid'";
    return getCount($sql);
}

function generate_signature($partnerId, $secret, $epoch, $httpMethod, $endpoint, $body = null)
{
    global $partnerId, $secret;
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

function first_day()
{
    $month_ini = new DateTime("2023-11-01 00:00:00");
    return $month_ini->format('Y-m-d'); // 2012-02-01
}


function last_day()
{
    $month_end = new DateTime("2024-01-31 00:00:00");
    return $month_end->format('Y-m-d'); // 2012-02-29
}

function get_admin()
{
    $customerId = null;
    $endpoint = '/customer/api/customers';
    $data = getdata($endpoint);
    $data = json_decode($data);
    if ($data[0]->customerId) $customerId = $data[0]->customerId;

    return $customerId;
}


function get_companies($customerId)
{


    $endpoint = '/customer/api/companies?customerId='.$customerId;
    echo "\n\n" . $endpoint . "\n\n";
    $data = getdata($endpoint);
    $data = json_decode($data);

    //var_dump($data);


    $companies = array();
    for ($i = 0; $i < sizeof($data); $i++)
    {
        if (!empty($data[$i]->companyId))array_push($companies, $data[$i]);

    }

    //print_r($companies);

    return $companies;



}


function get_vouchers($cuid)
{

    $first_day = first_day();
    $last_day = last_day();


    $endpoint = '/customer/api/companies/'.$cuid.'/bookkeeping/vouchers?startDate='.$first_day.'&endDate='.$last_day;
    echo "\n\n" . $endpoint . "\n\n";
    $data = getdata($endpoint);
    $data = json_decode($data);

    echo "----------------\n";
    var_dump($data);
    // if ($data[0]->customerId) $customerId = $data[0]->customerId;
    echo "-\n---------------\n";

    //return $customerId;

    return $data;
}





$customer = "PLUSCOM";

$company = "";

$ticket = "1777";


$short_options = "a::c::t::";
$long_options = ["admin::", "company::", "ticket::"];
$options = getopt($short_options, $long_options);

if(isset($options["a"]) || isset($options["admin"])) {
    $customer = isset($options["a"]) ? $options["a"] : $options["admin"];
    echo("Customer: ".$customer."\n\n");

}

if(isset($options["c"]) || isset($options["company"])) {
    $company = isset($options["c"]) ? $options["c"] : $options["company"];
    echo("Company: ".$company."\n\n");
}

if(isset($options["t"]) || isset($options["ticket"])) {
    $company = isset($options["t"]) ? $options["t"] : $options["ticket"];
    echo("Ticket: ".$ticket."\n\n");
}

$CUSTOMER_ID = get_admin();

try {
    if (!empty($customer) && $customer !== $CUSTOMER_ID)
    {
        throw new Exception("customerId: $CUSTOMER_ID is is not found on Visma API ... script execution stopped");

    }
}
catch(Exception $e) {
    echo $e->getMessage();
    exit();
}

echo "CustomerId: ".get_admin();

//Let's get the companies
$COMPANY_ARRAY = get_companies($CUSTOMER_ID);

//print_r($COMPANY_ARRAY);

$vouchers = array();

foreach ($COMPANY_ARRAY as $COMPANY) {
    echo "Saving company: ".$COMPANY->name1 . "\n\n";
    $company_data = get_object_vars($COMPANY);
    if(saveCompany($company_data))
    {
        // 1777 Selvittelytili
        $company_cuid = $COMPANY->cuid;
        echo "Saving company vouchers with cuid: ". $company_cuid . "\n\n";
        $ALL_VOUCHERS = get_vouchers($company_cuid);
        $uid = get_company_uid($company_cuid);
        foreach ($ALL_VOUCHERS as $VOUCHER) {
            $voucher_data = get_object_vars($VOUCHER);
            //Let's loop entries
            $entrysize = sizeof($voucher_data['voucherEntries']);

            for ($i = 0; $i < $entrysize; $i++)
            {
                $entries = $voucher_data['voucherEntries'][$i];
                $accountnum = get_object_vars($entries);
                //echo "\n\n Num: ".$accountnum['accountNumber']."\n\n";
                if (intval($accountnum['accountNumber']) === 1777)
                {
                    saveVoucher($voucher_data,$uid);
                    //print_r($voucher_data);

                }
            }

        }



    }
    else
    {
        echo "\n\n Error... \n\n";
    }
}


/*
for ($i = 0; $i < sizeof($COMPANY_ARRAY); $i++)
{
    $voucher_cuid = $COMPANY_ARRAY[$i]->cuid;
    echo  "\n Let's get company vouchers with cuid: ".$voucher_cuid."\n";
    $VOUCHERS = get_vouchers($voucher_cuid);

    print_r($VOUCHERS);
}
*/

echo("\n\nDONE\n\n");

