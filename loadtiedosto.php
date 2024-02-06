<?php
session_start();
require_once ('config.php');

$uid  = set_safe($_SESSION['uid']);
$tid  = set_safe($_REQUEST['tid']);
$vatid  = set_safe($_SESSION['vatid']);


if(empty($_SESSION['uid']))
{
    header('Location: ./index.php?logout=true');
}

if ($tid)
{
    $tiedosto = getCount("SELECT file_name as c from tiedostot where id = '$tid' AND uid='$uid'");

    $file_path = $COMPANYDIR . $vatid .'/tiedostot/'.$tiedosto;
//echo $file_path;

    if (file_exists($file_path))
    {
        $mime = get_mime_type($tiedosto);

        header('Content-type: '.$mime);
    
        header('Content-Disposition: inline; filename="' . $tiedosto . '"');
        header('Content-Length: ' . filesize($file_path));
          
       header('Content-Transfer-Encoding: binary');
          
       header('Accept-Ranges: bytes');
          
        // Read the file
        @readfile($file_path);

    }
}
else
{
    echo "No file rights...";
}
