<?php
session_start();
/*
$uid  = set_safe($_SESSION['uid']);
$tid  = set_safe($_REQUEST['tid']);
$vatid  = set_safe($_REQUEST['vatid']);


if(empty($_SESSION['uid']))
{
    header('Location: ./index.php?logout=true');
}

if ($tid)
{
   // $tiedosto = getCount("SELECT file_name as c where id = '$tid' AND uid='$uid'");

   // $file_path = $COMPANYDIR . $vatid .'/tiedostot/'.$tiedosto;
/*
    if (file_exists($file_path))
    {
        $mime = get_mime_type($tiedosto);
        header("'Content-Type: ".$mime."; Content-disposition: attachment; filename=".$tiedosto);
        readfile($file_path);

    }
 
}
else
{
    echo("No file rights...");
}
   */



