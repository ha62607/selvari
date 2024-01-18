<?php
$SITEPATH = "/Users/ha62607/sites/tamkonto/";

echo(strlen($SITEPATH));

$KUVAPATH = "/Users/ha62607/sites/tamkonto/temp/jepjep.pmg";

$jep = substr($KUVAPATH,strlen($SITEPATH));

echo("<br/>");

echo($jep);