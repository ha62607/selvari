<?php
require_once ('config.php');
$ip_now = getUserIP();

$email = set_safe($_POST['email']);
$fullname = set_safe($_POST['firstname']. " ".$_POST['lastname']);
$firstname = set_safe($_POST['firstname']);
$familyname = set_safe($_POST['familyname']);
$zipcode = set_safe($_POST['zipcode']);
$ziparea = set_safe($_POST['ziparea']);
$address = set_safe($_POST['address']);

$company_name = set_safe($_POST['company']);
$company_vat = set_safe($_POST['vatid']);
$pass = set_safe($_POST['pass']);

$vatid = "FI".str_replace("-","",$company_vat );



error_log($vatid);

if (strlen($vatid) !== 10 && validEmail($email))
{
    echo 'false';
}
else
{
    $timestamp = 'CURRENT_TIMESTAMP' ;

    $data = [
        'email' => $email,
        'fullname' => $fullname,
        'company_name' => $company_name,
        'company_vat' => $company_vat,
        'created' => 'CURRENT_TIMESTAMP',
        'lastvisit' => 'CURRENT_TIMESTAMP',
        'lastvisit_ip' => $ip_now,
        'create_ip' => $ip_now,
        'pass' => $pass,
        'vatid' => $vatid
       ];

    $date = date('Y-m-d H:i:s');

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO puser (email, fullname, company_name, company_vat, created, lastvisit, lastvisit_ip, create_ip, pass, vatid, firstname, lastname, zipcode, ziparea, company_address ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

    if ( false===$stmt  ) {
        // again execute() is useless if you can't bind the parameters. Bail out somehow.
        error_log('bind_param() failed: ' . htmlspecialchars($conn->error));
        //die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    }

    $rc = $stmt->bind_param("sssssssssssssss", $email, $fullname,$company_name, $company_vat,$date ,$date ,$ip_now,$ip_now,$pass,$vatid, $firstname, $lastname,$zipcode, $ziparea, $address);

    if ( false===$rc ) {
        // again execute() is useless if you can't bind the parameters. Bail out somehow.
        error_log('bind_param() failed: ' . htmlspecialchars($stmt->error));
        //die('bind_param() failed: ' . htmlspecialchars($stmt->error));
    }

    $stmt->execute();


    error_log("Jorma menee");

    $stmt->close();
    $conn->close();
    echo 'true';
}
?>