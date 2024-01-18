<?php
session_start();
require_once ('config.php');



function resize_image($newfilename, $file, $w, $h, $crop=false) {



    list($width, $height) = getimagesize($file);
    $image_info = getimagesize($file);


    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }

    //Get file extension
    $exploding = explode(".",$file);
    $ext = strtolower(end($exploding));



    switch($ext){
        case "png":
            $image_p = imagecreatetruecolor($newwidth, $newheight);
            $image = imagecreatefrompng($file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagepng($image_p, $newfilename);
            break;
        case "jpeg":
        case "jpg":
            $image_p = imagecreatetruecolor($newwidth, $newheight);
            $image = imagecreatefromjpeg($file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($image_p, $newfilename);
            //echo("<h2>JEES</h2>");
            break;
        case "gif":
            $image_p = imagecreatetruecolor($newwidth, $newheight);
            $src = imagecreatefromgif($file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagegif($image_p, $newfilename);
            break;
        default:
            $image_p = imagecreatetruecolor($newwidth, $newheight);
            $image = imagecreatefromjpeg($file);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagejpeg($image_p, $newfilename);
            break;
    }

}



function compress($source, $destination, $quality) {
    //Get file extension
    $exploding = explode(".",$source);
    $ext = end($exploding);


    switch($ext){
        case "png":
            $src = imagecreatefrompng($source);
            break;
        case "jpeg":
        case "jpg":
            $src = imagecreatefromjpeg($source);
            break;
        case "gif":
            $src = imagecreatefromgif($source);
            break;
        default:
            $src = imagecreatefromjpeg($source);
            break;
    }

    switch($ext){
        case "png":
            imagepng($src, $destination, $quality);
            break;
        case "jpeg":
        case "jpg":
            imagejpeg($src, $destination, $quality);
            break;
        case "gif":
            imagegif($src, $destination, $quality);
            break;
        default:
            imagejpeg($src, $destination, $quality);
            break;
    }

    return $destination;
}


function doImage($filepath,$kid,$tid,$ind,$yrit)
{

    global $TARGETDIR, $TEMPPATH, $PDFMAXSIZE;

    $target_dir = $TEMPPATH;
    $des_dir = $TARGETDIR;
    $image_size_error = true;
    $uploadOk = 0;
    $image_load_error = true;
    $kuva = '';

    $curtime =  date('Y-m-d');
    error_log("KUVA-AIKA:".$curtime);

    $sepr = $curtime."_".$ind;

    $date = date_create();
    $stamp = date_timestamp_get($date);


    if(!empty($filepath))
    {

        $name = $yrit."_".$curtime."_".$stamp."_" .$kid."_".$tid;
        $target_file  = $target_dir . basename($filepath);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if($imageFileType === 'jpg' || $imageFileType === 'jpeg' || $imageFileType === 'gif' || $imageFileType === 'png' )
        {
                    if($imageFileType === 'jpg' || $imageFileType === 'jpeg')
                    {
                        correctImageOrientation($target_file);
                    }

                    $filename = $target_file;

                    $exploding = explode(".",$target_file);
                    $ext = end($exploding);

                    $resizedFilename = $target_dir.$name."_resized.".$ext;
                    $compressFilename = $target_dir.$name."_compress.".$ext;

                    //8 285 242
                    $filesize = filesize( $filepath);
                    //echo("<br/>FILESIZE:".$_FILES[$fname]["size"]."<br/>" );
                    $compress = 10;

                    $smallfile = false;


                    if( $filesize > 3000000 ) $compress = 20;
                    else if( $filesize > 2000000 ) $compress = 30;
                    else if( $filesize > 1000000 ) $compress = 30;
                    else if( $filesize > 500000 ) $compress = 40;
                    else if( $filesize > 300000 ) $compress = 50;
                    else if( $filesize > 100000 ) $compress = 60;
                    else
                    {
                        $smallfile = true;

                    }


                    if ($filesize > 100000)
                    {
                        compress($target_file,$compressFilename,$compress);
                    }
                    else
                    {
                        $compressFilename = $target_file;
                    }

                    if(!$smallfile)
                    {
                        //thumbnails big
                        resize_image($compressFilename, $compressFilename, 800, 800);
                    }

                    //copy($resizedFilename,$des_dir.$name."-thumb.".$ext);

                    copy($compressFilename,$des_dir.$name.".".$ext);
                    $kuva = $name.".".$ext;

                    //Lets delete uploaded files
                    if (file_exists($filename))
                    {
                        unlink($filename);
                    }

                    if (file_exists($resizedFilename))
                    {
                        unlink($resizedFilename);
                    }

                    if (file_exists($compressFilename))
                    {
                        unlink($compressFilename);
                    }

        }
        elseif($imageFileType === 'pdf')
        {
            //	3,000,000
            $filesize = filesize( $filepath);

            if($filesize < $PDFMAXSIZE)
            {
                copy($target_file ,$des_dir.$name.".pdf");
                unlink($target_file);
                return $name.".pdf";
            }
            else
            {
                return "pdf-max";
            }
        }

        else
        {
            $image_type_error = true;
            $uploadOk = 0;
        }



    }


    return $kuva;


}



$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf'); // valid extensions
$path = $TEMPPATH; // upload directory
$ok = true;

$uid = set_safe($_SESSION['uid']);
$tid = set_safe($_POST['tid']);
$transid = set_safe($_POST['transid']);
$selite = set_safe($_POST['selite']);
$yrit = set_safe($_SESSION['vatid']);


$curcount = kuitit($uid, $tid, 'c');

if (!empty($curcount ) && intval($curcount) >= 3 )
{
    echo("max");
}

$aika = gettime();
$ip = getUserIP();

$kuvat = ["kuva1","kuva2"];

$suc = "";

$ind = 0;

foreach ($kuvat as $kuva) {

    if ($_FILES[$kuva]) {
        $img = $_FILES[$kuva]['name'];
        $tmp = $_FILES[$kuva]['tmp_name'];

// get uploaded file's extension
        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));

// can upload same image using rand function
        $final_image = rand(1000, 1000000) . "_" . $img;

// check's valid format
        if (in_array($ext, $valid_extensions)) {
            error_log($kuva . "...");
            $path = $path . strtolower($final_image);

            if (move_uploaded_file($tmp, $path)) {

                $jep = substr($path, strlen($SITEPATH));
                error_log($kuva . " path: " . $path);

                $filepath = $path;

                $kuva_up = doImage($filepath,$tid,$transid, $ind,$yrit);

                error_log($kuva . " Uploaded to path: " . $kuva_up);

                //echo "<img src='$jep' />";

//include database configuration file
                $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
                if ($db->connect_error) {
                    die("Unable to connect database: " . $db->connect_error);
                }
//insert form data in the database
                $insert = $db->query("INSERT kuitit (uid,tid,transactionId,tiedosto,aika,ip) VALUES ('" . $uid . "','" . $tid . "','" . $transid . "','" . $kuva_up . "','" . $aika . "','" . $ip . "')");
                if (!$insert) {
                    $ok = false;
                    error_log($db->error);
                }

                $insert = $db->query("UPDATE trans set kuitit='2' where tid='" . $tid . "' AND uid = '" . $uid . "' ");
                if (!$insert) {
                    $ok = false;
                    error_log($db->error);
                }


                $db->close();



//echo $insert?'ok':'err';
            }
        } else {
            echo 'invalid';
        }

    }
    $ind++;
}
if(!empty($selite))
{
    //include database configuration file
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if($db->connect_error){
        die("Unable to connect database: " . $db->connect_error);
    }

    $SQL = "UPDATE trans SET trans.key = '".$selite."' WHERE tid='".$tid."'";
    error_log($SQL);
    $insert = $db->query($SQL);

    if (!$insert)
    {
        error_log($db->error);
        $ok = false;
    }

    $db->close();


}

if (!$ok)
{
    echo '0';
}
elseif ($ok)
{
    echo '1';
}
?>
