<?php

$workpath = "/Extra";

//Check if db exists, and if not.. download it... 
if (!file_exists("$workpath/bin/edp.sqlite3")) {
    system("curl -o $workpath/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php");
}

include_once "config.inc.php";
include_once "functions.inc.php";

$os_string = "";
$os = getVersion();

if ($os == "lion" || $os == "ml" || $os == "mav") {
    system("open /Extra/storage/apps/EDPweb.app");
} else {
    system("open http://127.0.0.1:11250/");
}
?>
