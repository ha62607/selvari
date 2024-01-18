<?php

$ENV = 0;

if (isset($_SERVER['SERVER_NAME']))
{
    if ($_SERVER['SERVER_NAME'] === 'tamkonto.com' )
    {
        $ENV = 3;
    }
    else if ($_SERVER['SERVER_NAME'] === 'www.tamkonto.com' )
    {
        $ENV = 3;
    }
}

$ACURL = "https://ob.nordigen.com/api/v2/accounts/xxxxx/transactions/";

$BANKURL = "https://ob.nordigen.com/api/v2/requisitions/BANKIA/";

$NEW_TOKEN = "https://ob.nordigen.com/api/v2/token/new/";

$REQ = "https://ob.nordigen.com/api/v2/requisitions/";

$TOKEN = "xxx";

$SELVARI = true;

$secret = [
    'secret_id' => 'aa2eb74e-e844-4e44-8b05-20bac75c4867',
    'secret_key' => 'f401077b4f4aa72f09e5532c7ac31c28b9668bf4f12dfec5e3174e1f1267926f7c5d22b1bd4858d60cbf1cb0d74d1dc1be44eb77f72115b36abd5344901db9dd'
];

$SITE = "http://localhost/selvari/";
$BANKRETURN = "http://localhost/selvari/bank.php";

$TARGETDIR = "/Users/ha62607/sites/selvari/kuitit/";
$FILEDIR = "/Users/ha62607/sites/selvari/uploads/";

$PDFMAXSIZE = 3000000;

$TEMPPATH = "/Users/ha62607/sites/selvari/temp/";

$SITEPATH = "/Users/ha62607/sites/selvari/";


$dbhost = "127.0.0.1";
$dbport = "3306";
$dbname = "selvari";
$dbuser = "root";
$dbpass = "root";

if ($ENV == 3)
{
    $SITE = "https://tamkonto.com/";

    $BANKRETURN = "https://tamkonto.com/bank.php";
    $TARGETDIR = "/var/www/KUITIT/";
    $FILEDIR = "/var/www/TIEDOSTOT/";

    $PDFMAXSIZE = 3000000;

    $TEMPPATH = "/var/www/html/tamkonto.com/temp/";

    $SITEPATH = "/var/www/html/tamkonto.com";

    $dbhost = "localhost";
    $dbport = "3306";
    $dbname = "tamkonto";
    $dbuser = "root";
    $dbpass = "JSRy5hebnghwGBVW3";

}



$danske = "DANSKEBANK_BUSINESS_DABAFIHH";
$handel = "HANDELSBANKEN_CORPORATE_HANDFIHH";
$nordea = "NORDEA_BUSINESS_NDEAFIHH";
$omasp = "OMASP_OMSAFI2S";
$op = "OP_OKOYFIHH";
$pop = "POPPANKKI_POPFFI22";
$saasto = "S_PANKKI_SBANFIHH";
$spankki = "SAASTOPANKKI_ITELFIHH";
$alands= "ÅLANDSBANKEN_AABAFI22";

$danske = "Danske Bank";
$handel = "Handelsbanken";
$nordea = "Nordea";
$omasp = "Oma Säästöpankki";
$op = "OP";
$pop = "POP Pankki";
$saasto = "Säästöpankki";
$spankki = "S-Pankki";
$alands= "Ålandsbanken";

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





function refreshtoken()
{
    error_log("Refresh token...");
    global $secret;
    global $NEW_TOKEN;
    global $TOKEN;

    $data =json_encode($secret);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$NEW_TOKEN);
    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


    $request_headers = array(
        "accept: application/json",
        "Content-Type: application/json"
    );



    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);
    $token_array = json_decode($server_output);

    $token = "";
    foreach($token_array  as $key => $value) {
        //echo "$key : $value <br/><br/>";
        if ($key === 'access') $token = $value;
    }

    //var_dump($token_array);

    //$token = $token_array['access'];



    //print "NORDIGEN:".  $token;

    $TOKEN = $token;

    curl_close ($ch);

    $_SESSION['token'] = $TOKEN;

    error_log("TOKEN-REFRESH:".$TOKEN);
    return $TOKEN;

}


function updatetoken($TOKEN)
{
    error_log("Update token...");

    global $dbhost, $dbuser, $dbpass, $dbname;

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname );

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $date = date('Y-m-d H:i:s');

    $SQL = $conn->prepare("UPDATE tamdata set value=?, aika=? WHERE tamdata.key=?");

    if ($SQL == false)
    {
        $error = $conn->errno . ' ' . $conn->error;
        error_log("Virhe..:".$error);
    }

    error_log("Aika..:".$date);
    error_log("Token..:".$TOKEN);
    $to = 'TOKEN';
    $ok = $SQL->bind_param('sss', $TOKEN,$date, $to);

    if ($ok == false)
    {
        $error = $SQL->errno . ' ' . $SQL->error;
        error_log("Virhe BIND PARAM..:".$error);
    }

    $ok = $SQL->execute();

    if ($ok == false)
    {
        $error = $SQL->errno . ' ' . $SQL->error;
        error_log("Virhe BIND PARAM..:".$error);
    }

    $SQL->close();
    $conn->close();

}

function gettoken($count = 0)
{
    error_log("Get token...:".$count);

    if ($count > 3)
    {
        error_log("Error with bank server...:".$count);

        return "Error with bank server...";
    }

    global $dbhost, $dbuser, $dbpass, $dbname, $TOKEN;


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare("SELECT value, aika FROM tamdata WHERE tamdata.key = 'TOKEN'");
    $query->execute();

    error_log("Token aaa ...".$TOKEN);


    $row  = $query->fetch();

    if( $query->rowCount() > 0 )
    {

        //error_log("A1");
        $aika = $row['aika'];
        //error_log("A2:".$aika);
        $time = strtotime($aika);
        $curtime =  date('Y-m-d H:i:s');
        //error_log("A3:".$curtime);
        $curtime = strtotime($curtime);


        $time = (int) $time;
        $curtime = (int) $curtime;


        //error_log("T1:".$time);
        //error_log("T2:".$curtime);
        //error_log("T3:".($curtime-$time));



        if(($curtime-$time) > 3600) {     //1800 seconds
            //vanha token uusitaan
            error_log("Old token needs renew...");
            $TOKEN = refreshtoken();
            updatetoken($TOKEN);
            $count = $count + 1;
            gettoken($count);
        }
        else
        {

            $TOKEN = $row['value'];
            error_log("Token is good...:".$TOKEN);
        }


    }

    $conn = null;
    return $TOKEN;

}

function saveBank($uid, $bankname, $account, $bankid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $bankdone = getBank($uid);
    if (!empty($bankdone) && $bankdone['account'])
    {
        error_log("Account for user:".$uid." exists");
        return;
    }

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $aika = gettime();
    $createip = getUserIP();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("INSERT INTO account (uid, bankname, account, bid, create_ip, created) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssssss", $uid, $bankname, $account, $bankid, $createip, $aika);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}


function updateBank($uid, $bankid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;


    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("UPDATE account set bid=? WHERE uid=?");
    $stmt->bind_param("ss", $bankid, $uid);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}



function getBank($uid)
{
    global $dbhost, $dbuser, $dbpass, $dbname, $TOKEN;
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT aid, uid, bankname, account, bid, create_ip, created from account WHERE uid=?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();

    $result = $stmt->get_result();
    $frow = [];

    $row  = $result->fetch_assoc();

    if( !empty($row) ) {
        $frow['aid'] = $row['aid'];
        $frow['uid'] = $row['uid'];
        $frow['bankname'] = $row['bankname'];
        $frow['account'] = $row['account'];
        $frow['bid'] = $row['bid'];
        $frow['create_ip'] = $row['create_ip'];
        $frow['created'] = $row['created'];

    }
    $stmt->close();
    $conn->close();

    return $frow;
}

function nar($ar,$key)
{
    //error_log("NARKEY:".$key);

    if ($ar && array_key_exists($key,$ar))
    {
        $val =  $ar[$key];
        if (empty($val))
        {
            return "";

        }
        else
        {
            error_log($val);
        }
        return $val;
    }
    else
    {
        return "";
    }
}

function getUserAliasList($reload=false)
{

    if ( !empty($_SESSION['aliaslist']) && !$reload)
    {
        return $_SESSION['aliaslist'];
    }
    else
    {
        global $dbhost, $dbuser, $dbpass, $dbname;
        $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
        $data = array();
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $cuid = set_safe($_SESSION['uid']);
        $SQL = "SELECT bban, alias from alias WHERE uid=?";


        $stmt = $conn->prepare($SQL);
        $rc = $stmt->bind_param("s",$cuid);
        $stmt->execute();

        $result = $stmt->get_result();
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $data[$i] = $row;
            $i++;
        }

        $_SESSION['aliaslist'] = $data;

        $stmt->close();
        $conn->close();


    }

    return $data;

}

function containsAlias($bban)
{
    if (!empty($_SESSION['alias']) && $_SESSION['alias'] === 'true' )
    {

        $aliaslist = getUserAliasList();

    for ($i = 0; $i < count($aliaslist); $i++)
    {

        if ($aliaslist[$i]['bban'] === $bban)
        {
            return $aliaslist[$i]['alias'];
        }
    }

    }

    return "";
}


function getUserTransFull($uid,$mon='0',$year='0',$cache=true)
{
    global $dbhost, $dbuser, $dbpass, $dbname;
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    //error_log("MON:".$mon." YEAR:".$year);

    $data = array();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }




    if($mon !== '0' && $year !== '0')
    {
        if (is_numeric($mon) && is_numeric($year))
        {

            $SQL = "SELECT tid,bookingDate,creditorName,debtorName,amount,remittanceInformationUnstructured,transactionId, status,selite,kuitit,bban,tilit from trans WHERE uid=? AND boomonth=? AND booyear=? ORDER BY boodate DESC";

            error_log($SQL);


            $stmt = $conn->prepare($SQL);
            $rc = $stmt->bind_param("sss",$uid,$mon,$year);
            $stmt->execute();

            $result = $stmt->get_result();
            $i = 0;
            while ($row = $result->fetch_assoc()) {
                $data[$i] = $row;
                $i++;
            }

            $stmt->close();
            $conn->close();


        }
    }
    else
    {
        $SQL = "SELECT tid,bookingDate,creditorName,debtorName,amount,remittanceInformationUnstructured,transactionId, status,selite,kuitit,bban,tilit from trans WHERE uid=? ORDER BY boodate DESC limit 0,200";



        $stmt = $conn->prepare($SQL);
        $rc = $stmt->bind_param("s",$uid);


        $stmt->execute();

        $result = $stmt->get_result();
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $data[$i] = $row;
            $i++;
        }

        $stmt->close();
        $conn->close();

    }

    return $data;


}

function getUserTrans($uid,$cache=true)
{

    if (!empty($_SESSION['transtime']) && $cache)
    {
        $transtime = (int) $_SESSION['transtime'];
        $time = time();

        if (($time - $transtime) < 5)
        {
            $data = $_SESSION['transdata'];
            error_log("Cachesta tapahtumat...");
            return $data;
        }
        else
        {
            error_log("Uusi cache tapahtumille...");
        }
    }

    $time = time();

    global $dbhost, $dbuser, $dbpass, $dbname;
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    $data = array();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $SQL = "SELECT distinct tamid from trans WHERE uid=?";
    $stmt = $conn->prepare($SQL);
    $rc = $stmt->bind_param("s",$uid);
    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        array_push($data,$row['tamid']);
    }

    $stmt->close();
    $conn->close();

    $_SESSION['transtime'] = $time;
    $_SESSION['transdata'] = $data;


    return $data;


}


function saveTrans($uid,$booked, $account, $count,$vatid="")
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $transactionId = nar($booked,'transactionId');
    $indata = getUserTrans($uid);

    if (in_array($transactionId , $indata))
    {
        return $count;
    }
    else
    {
        array_push($indata, $transactionId);
    }


    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


        $additionalInformation = nar($booked,'additionalInformation');
        $bookingDate = nar($booked,'bookingDate');
        $creditorName = nar($booked,'creditorName');
        $debtorName = nar($booked,'debtorName');
        $entryReference = nar($booked,'entryReference');
        $remittanceInformationUnstructured = nar($booked,'remittanceInformationUnstructured');

        $valueDate = nar($booked,'valueDate');
        $amount = nar($booked,'amount');
        $currency = nar($booked,'currency');

        $tdate = date_create($bookingDate );
        $boodate = date_format($tdate,"ymd");
        $boomon = date_format($tdate,"m");
        $booyear = date_format($tdate,"y");

        $boostamp= date_format($tdate,"U");

        $credit = nar($booked,'credit');
        $debit = nar($booked,'debit');

        $ta = (float) $amount;
        if ($amount > 0)
        {
            $credit = '0';
            $debit = '1';
        }
        else
        {
            $credit = '1';
            $debit = '0';
        }

        $tamid = $vatid."0000".$boodate."0000".$transactionId;

        $merchantCategoryCode = nar($booked,'merchantCategoryCode');;
        $bban = nar($booked,'bban');
        $aika =  date('Y-m-d H:i:s');
        $count++;
        //error_log('TID:'.$booked[$key]." C:".$count);
        $one = '1';
        $SQL = "INSERT INTO trans (uid, account, additionalInformation,bookingDate,creditorName,debtorName,entryReference,remittanceInformationUnstructured,transactionId,valueDate,amount,currency,credit,debit,merchantCategoryCode,bban,status,fetchdate,boodate,boostamp, boomonth, booyear,tamid) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($SQL);
        $rc = $stmt->bind_param("sssssssssssssssssssssss",
            $uid,
            $account,
            $additionalInformation ,
            $bookingDate,
            $creditorName,
            $debtorName ,
            $entryReference,
            $remittanceInformationUnstructured,
            $transactionId,
            $valueDate,
            $amount,
            $currency,
            $credit,
            $debit ,
            $merchantCategoryCode,
            $bban,
            $one,
            $aika,
            $boodate,
            $boostamp,
            $boomon,
            $booyear,
            $tamid);

        if ( false===$rc ) {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            error_log($stmt->error);
            //die('bind_param() failed: ' . htmlspecialchars($stmt->error));
        }


        $rc = $stmt->execute();
        if ( false===$rc ) {
            error_log($stmt->error);
            //die('execute() failed: ' . htmlspecialchars($stmt->error));
        }

        if ($stmt->affected_rows > 0)
        {
            $count++;
        }
        $stmt->close();
        $conn->close();
        return $count;

}

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  =  isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1';


    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function gettime()
{
    return date('Y-m-d H:i:s');
}

function startsWith($string, $startString) {
    return mb_strpos($string, $startString) === 0;
}

function validEmail($email)
{

    $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
    if (!preg_match($pattern, $email))
    {
        return false;
    }
    else
    {
        return true;
    }

}

function set_safe($val)
{
    /*
    $val =  addslashes($val);
    $danger = array("select","update","delete","drop","insert",";","table","database","tables","desc");
    $reps = array("","","","","","","","");
    $val = str_ireplace($danger, $reps, $val);
    return $val;
     */
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    $val =  str_replace($search, $replace, $val);
    return trim($val);
}



function getCount($sql)
{
    global $dbname;
    global $dbhost;
    global $dbuser;
    global $dbpass;

    $con = mysqli_connect($dbhost, $dbuser, $dbpass);
    mysqli_select_db($con, $dbname) or die(mysqli_error($con));
    $c = 0;
    $result = mysqli_query($con, $sql) or die(mysqli_error($con));

    while($row = mysqli_fetch_array( $result ))
    {
        $c = $row['c'];
    }

    mysqli_close($con);
    return $c;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function correctImageOrientation($filename) {
    if (function_exists('exif_read_data')) {
        $exif = exif_read_data($filename,0, true);

        if($exif && ( isset($exif['Orientation']) || isset($exif['IFD0']['Orientation']))) {
            $orientation = $exif['Orientation'];
            if(!$orientation)
            {
                $orientation = $exif['IFD0']['Orientation'];
            }

            if($orientation != 1){
                $img = imagecreatefromjpeg($filename);
                $deg = 0;
                switch ($orientation) {
                    case 3:
                        $deg = 180;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg) {
                    $img = imagerotate($img, $deg, 0);
                }
                // then rewrite the rotated image back to the disk as $filename
                imagejpeg($img, $filename, 95);
            } // if there is some rotation necessary
        } // if have the exif orientation info
    } // if function exists
    //error_log("");
}


function kuitit($uid, $tid, $type)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $SQL = "SELECT kid, tiedosto FROM kuitit WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' ";
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();


    $data = [];
    $i = 0;
    while ($row  = $query->fetch())
    {
        $kuvatiedosto = $row['tiedosto'];
        $array = explode('.', $kuvatiedosto);
        $extension = strtolower(end($array));
        $data[$i] = $row['kid'].":".$extension;
        $i++;
    }

    $conn = null;

    if ($i > 0)
    {
        if ($type == 'json')
        {
            return json_encode(array('data' => $data));
        }
        else
        {
            return sizeof($data);
        }
    }
    else
    {
        return '0';
    }
}


function get_selitte($uid, $tid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $SQL = "SELECT selite FROM trans WHERE uid = '" . $uid  . "' AND tid = '" . $tid. "' ";
    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $selite = "";

    while ($row  = $query->fetch())
    {
        $selite = $row['selite'];
    }

    $conn = null;

    return $selite;
}

function update_kuittistattus($uid, $tid, $status=1)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "UPDATE trans SET  kuitit='".$status."' WHERE tid='".$tid."' AND uid='".$uid."'";
    error_log($SQL);
    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $db->close();

}

function set_kuittistatus($uid, $tid)
{
    $status = 0;

    $selitteet = get_selitte($uid, $tid);
    {
        if (!empty($selitteet) && strlen($selitteet) > 9)
        {
            $status = 1;
        }
    }

    $kuitit = intval(kuitit($uid, $tid, 'c'));
    if($kuitit > 0)
    {
        $status = 2;
    }

    update_kuittistattus($uid, $tid, $status);

}

function get_kuittistatus($uid, $tid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;
    $status = 0;

    $selitteet = get_selitte($uid, $tid);
    {
        if (!empty($selitteet) && strlen($selitteet) > 9)
        {
            $status = 1;
        }
    }

    $kuitit = intval(kuitit($uid, $tid, 'c'));
    if($kuitit > 0)
    {
        $status = 2;
    }

    return $status;

}

function setTilit($uid, $tid, $value=1)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "UPDATE trans SET  tilit='".$value."' WHERE tid='".$tid."' AND uid='".$uid."'";
    error_log($SQL);
    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $db->close();

}

function getTilit($uid, $tid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $SQL = "SELECT tika.tilinro as nro, tilu.tilinimi as nimi, tika.alv as alv FROM tika, tilu WHERE tika.tilinro = tilu.tilinro  AND tika.tid='".$tid."'  AND uid='".$uid."'";

   // error_log($SQL);


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $til = [];

    while ($row  = $query->fetch())
    {
        array_push($til,$row['nro']);
        array_push($til,utf8_encode($row['nimi']));
        array_push($til,utf8_encode($row['alv']));

    }
   // error_log("NRO:".$til[0]);
   // error_log("NIMI:".$til[1]);
    $conn = null;

    return $til;
}


function getOsiot($uid, $tid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $SQL = "SELECT sutid, name, summa FROM subtrans WHERE tid = '".$tid."' AND uid='".$uid."' order by name";

    // error_log($SQL);


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $osiot = [];

    while ($row  = $query->fetch())
    {
        $os = [];
        array_push($os,$row['sutid']);
        array_push($os,$row['name']);
        array_push($os,$row['summa']);
        array_push($osiot,$os);

    }
    $conn = null;

    return $osiot;
}


function getOsionTilit($uid, $tid)
{
    global $dbhost, $dbuser, $dbpass, $dbname;


    $SQL = "SELECT sutid, tika.tilinro as nro, tilinimi, tika.alv as alv FROM tika,tilu WHERE tid = '".$tid."' AND uid='".$uid."'  AND tika.tilinro = tilu.tilinro";
    error_log($SQL);


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $tilit = [];

    while ($row  = $query->fetch())
    {
        $os = [];
        array_push($os,$row['sutid']);
        array_push($os,$row['nro']);
        array_push($os,utf8_encode($row['tilinimi']));
        array_push($os,$row['alv']);
        array_push($tilit,$os);

    }
    $conn = null;

    return $tilit;
}


function getTilitForExport($uid, $tidarr)
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $tidlist =  implode(', ', $tidarr);

    $SQL = "SELECT tilinro as nro, tid  FROM tika WHERE tid in (".$tidlist.")  AND uid='".$uid."'";

    // error_log($SQL);


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $til = [];

    while ($row  = $query->fetch())
    {
        array_push($til,$row['nro']);
        array_push($til,utf8_encode($row['nimi']));
    }
    // error_log("NRO:".$til[0]);
    // error_log("NIMI:".$til[1]);
    $conn = null;

    return $til;
}


function alv()
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $SQL = "SELECT kanta  FROM alv ORDER BY aid ASC";



    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $alv = [];

    while ($row  = $query->fetch())
    {
        array_push($alv,$row['kanta']);
    }

    $conn = null;

    return $alv;
}

function is_string_float(string $text)
{
    return $text === (string) (float) $text;
}

function getMyFiles()
{
    global $dbhost, $dbuser, $dbpass, $dbname;

    $uid = set_safe($_SESSION['uid']);
    $SQL = "SELECT file_name, aika,id  FROM tiedostot WHERE uid='".$uid."' ORDER BY aika DESC";
    error_log($SQL);


    $conn = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $query = $conn->prepare($SQL);
    $query->execute();

    $files = [];


    while ($row  = $query->fetch())
    {
        $fi = [];

        $file =  basename($row['file_name']);
        array_push($fi,$file);
        array_push($fi,$row['aika']);
        $ext = strtolower(pathinfo($row['file_name'], PATHINFO_EXTENSION));
        array_push($fi,$ext);
        array_push($fi,$row['id']);

        array_push($files,$fi);

    }

    $conn = null;

    return $files;
}

function mres($value)
{
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");

    return str_replace($search, $replace, $value);
}


/**
 * Function to sanitize a variable to prevent SQL injections.
 *
 * @param mixed $value The value to be sanitized.
 *
 * @return mixed The sanitized value.
 */
function sanitize_sql( $value)
{
    // If the value is an array, sanitize each element recursively.
    if (is_array($value)) {
        foreach ($value as $key => $val) {
            $value[$key] = sanitize_sql($val);
        }
    } // If the value is a string, sanitize it using the mysqli_real_escape_string function.
    elseif (is_string($value)) {
        $value = mres($value);
    } // If the value is a number, sanitize it using the intval function.
    elseif (is_numeric($value)) {
        $value = intval($value);
    } // If the value is a boolean, sanitize it using the boolval function.
    elseif (is_bool($value)) {
        $value = boolval($value);
    } // If the value is null, return it as is.
    elseif (is_null($value)) {
        return $value;
    } // If the value is an object, sanitize each property recursively.
    elseif (is_object($value)) {
        foreach ($value as $key => $val) {
            $value->$key = sanitize_sql($val);
        }
    }

    return $value;
}


/**
 * Generates a random safe alphanumeric password.
 *
 * @param int $length The length of the password to be generated.
 *
 * @return string The randomly generated password.
 */
function generatePassword($length)
{
    // Define the characters that can be used in the password
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    // Get the total number of characters
    $characterCount = strlen($characters);

    // Initialize an empty password string
    $password = '';

    // Generate the random password
    for ($i = 0; $i < $length; $i++) {
        // Get a random index within the range of characters
        $randomIndex = mt_rand(0, $characterCount - 1);

        // Append the randomly selected character to the password
        $password .= $characters[$randomIndex];
    }

    return $password;
}

function convertToInternationalVatFormat($vatId) {
    // Remove the hyphen from the VAT ID
    $vatId = str_replace('-', '', $vatId);

    // Add the country code "FI" to the beginning of the VAT ID
    $vatId = 'FI' . $vatId;

    return $vatId;
}


function separateZipcodeAndZiparea($input) {
    // Regular expression pattern to match the zipcode and ziparea.
    $pattern = '/^(\d{5})(.)([a-zA-Z0-9]+)$/';

    // Perform the regex match on the input string.
    if (preg_match($pattern, $input, $matches)) {
        // Extract the zipcode and ziparea from the matches.
        $zipcode = $matches[1];
        $ziparea = $matches[3];

        // Return the separated zipcode and ziparea as an array.
        return array($zipcode, $ziparea);
    } else {
        // Throw an exception if the input string does not match the pattern.
        throw new Exception("Invalid input format. Expected format: 5-digit zipcode followed by alphanumeric ziparea.");
    }
}

?>