<?php

$workpath = "/Extra";

//Check if db exists, and if not.. download it...
if (!file_exists("$workpath/bin/edp.sqlite3")) {
    system("curl -o $workpath/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php");

    if (!file_exists("$workpath/bin/edp.sqlite3")) {
        // Nope still no good use backup!
        system("cp $workpath/bin/edp.sqlite3 $workpath/bin/backup/edp.sqlite3");
    }
}

include_once "config.inc.php";
include_once "functions.inc.php";

$os_string = "";
$os = getVersion();

if ($os == "lion" || $os == "ml" || $os == "mav") {
	//EDP app is not being closed automatically after we click close, so we manually close this and open again when we launch it
	system("sudo killall EDP"); 
	//
    system("open /Extra/storage/apps/EDPweb.app");
} else {
    system("open http://127.0.0.1:11250/");
}
?>
