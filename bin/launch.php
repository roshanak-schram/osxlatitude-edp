<?php
$workpath = "/Extra";

//Check if db exists, and if not.. download it... 
if (!is_file("$workpath/bin/edp.sqlite3")) { system("curl -o /Extra/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php"); }


include_once "config.inc.php";
include_once "functions.inc.php";
$os_string = "";
$os        = getVersion();

if ($os == "ml" || $os == "lion") { system("open /Extra/storage/apps/EDPweb.app"); }
else { system("open http://127.0.0.1:11250/"); }

?>
