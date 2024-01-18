<?php
session_start();
$uid = $_SESSION['uid'];

if(!empty($uid) && intval($uid) > 0 )
{
    echo '1';
}
?>