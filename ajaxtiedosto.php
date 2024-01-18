<?php
session_start();
require_once ('config.php');
if(empty($_SESSION['uid']))
{
    header('Location: ./index.php?logout=true');
}

$uid = set_safe($_SESSION['uid']);
$aika = gettime();
$ip = getUserIP();

//$img = $_FILES["image"]["name"];
//$tmp = $_FILES["image"]["tmp_name"];
//$errorimg = $_FILES["image"]["error"];

$valid_extensions = array('jpeg', 'jpg', 'png','pdf' , 'doc', 'docx', 'xls' , 'ppt', 'txt'); // valid extensions
$path = $FILEDIR; // upload directory

if(!empty($_POST['name']) || !empty($_POST['email']) || $_FILES['image'])
{
    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

// get uploaded file's extension
    $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

// can upload same image using rand function
    $final_image = rand(1000,1000000)."_".$img;

// check's valid format
    if(in_array($ext, $valid_extensions))
    {
        $path = $path.strtolower($final_image);

        if(move_uploaded_file($tmp,$path))
        {
            //echo "<img src='$path' />";
            $name = set_safe($_SESSION['fullname']);
            $email = set_safe($_SESSION['email']);

            $filu =  basename($path);

            $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $stmt = $conn->prepare("INSERT tiedostot (name,email,file_name,uid,aika,ip) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssssss", $name, $email, $filu, $uid, $aika, $ip);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            echo('1');
        }
    }
    else
    {
        echo 'invalid';
    }
}
