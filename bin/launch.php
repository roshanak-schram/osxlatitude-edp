<?php

//
// create must directories
//

// check for EDP folder
if(is_dir("/Extra/EDP")) {
$workpath = "/Extra/EDP";
}
else { $workpath = "/Extra"; };

// check for Extra/include folders
if(!is_dir("/Extra/include"))
   system("mkdir /Extra/include");
   
if(!is_dir("/Extra/include/Extensions"))
   system("mkdir /Extra/include/Extensions");
   
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

include_once "config.inc.php";
include_once "functions.inc.php";

$os_string = "";
$os = getVersion();

if ($os == "lion" || $os == "ml" || $os == "mav") {
	// EDP app is not being closed automatically after we click close
	// so we manually close this and open again when we launch it
	system("sudo killall EDP"); 
    system("open $workpath/apps/EDPweb.app");
} else {
	// start EDP
    system("open http://127.0.0.1:11250/");
}
?>