<?php

$workpath = "/Extra/EDP";

// Check for Extra/include and model data folders
if(!is_dir("/Extra/include"))
   system("mkdir /Extra/include");
   
if(!is_dir("/Extra/include/Extensions"))
   system("mkdir /Extra/include/Extensions");
   
if(!is_dir("$workpath/model-data"))
   system("mkdir $workpath/model-data");
   
//
// Updating database on app start
//

echo "Updating EDP database, please wait...\n";

// backup and remove db if exists to update
if (file_exists("$workpath/bin/edp.sqlite3")) {
	system("rm -Rf $workpath/bin/backup/edp.sqlite3");
	system("cp $workpath/bin/edp.sqlite3 $workpath/bin/backup/edp.sqlite3");
	system("rm -Rf $workpath/bin/edp.sqlite3");
  }
    	
// download db
system("curl -o $workpath/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php");

// could not download then use backup!
if (!file_exists("$workpath/bin/edp.sqlite3")) {
	echo "Failed to update EDP database, using database from backup...\n";
    system("cp $workpath/bin/backup/edp.sqlite3 $workpath/bin/edp.sqlite3");
}
else {
	echo "Update success.\n";
}

include_once "html/edpconfig.inc.php";
include_once "html/functions.inc.php";

$os_string = "";
$os = getVersion();

if(getMacOSXVersion() >= "10.7") {
	// EDP app is not being closed automatically after we click close
	// so we manually close this and open again when we launch it
	system("sudo killall EDP"); 
    system("open $workpath/bin/EDPweb.app");
} else {
	// start EDP
    system("open http://127.0.0.1:11250/");
}

?>