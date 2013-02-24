<?php
	$workpath	= "/Extra";
	$edpversion = "5.1";
	
	include_once "$workpath/bin/functions.inc.php";
	
	$verbose 	= "true";



	//Get system vars
	//$workpath	= getenv('PWD');
	$edpmode	= "";
	$ee			= "$workpath/Extensions";
	$strip		= str_replace("/", "", "$workpath");		
	$rootpath	= str_replace("Extra", "", "$workpath");
	$slepath	= "".$rootpath."System/Library/Extensions";
	$cachepath	= "".$rootpath."System/Library/Caches/com.apple.kext.caches/Startup";
	$incpath	= "$workpath/include";
	
    $localrev	= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
    $localrev	= str_replace("Last Changed Rev: ", "", $localrev);
    $hibernatemode = exec("pmset -g | grep hibernatemode"); $hibernatemode = str_replace("hibernatemode", "", $hibernatemode); $hibernatemode = str_replace(" ", "", $hibernatemode);
 
 	$os_string  = "";
	$os			= getVersion();	
	$verbose 	= "1";


	$version 	= "Rev: $localrev";
	$header 	= "-- EDP v$edpversion - Rev: $localrev ------------------------------------------------------------------";
	$footer		= "---------------------------------------------------------------------------- O S X L A T I T U D E . C O M --";
	

	$edp_db = new PDO('sqlite:/Extra/bin/edp.sqlite3');
	$edp_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		    
	//Generate multi-dims - this is instead of re-writting all code and to keep support for console mode
		//audio
		$stmt = $edp_db->query("SELECT * FROM audio order by id");
		$stmt->execute(); $audiodb = $stmt->fetchAll();
		//Battery
		$stmt = $edp_db->query("SELECT * FROM battery order by id");
		$stmt->execute(); $batterydb = $stmt->fetchAll();		
		//Ethernet
		$stmt = $edp_db->query("SELECT * FROM ethernet order by id");
		$stmt->execute(); $landb = $stmt->fetchAll();		
		//ps2
		$stmt = $edp_db->query("SELECT * FROM ps2 order by id");
		$stmt->execute(); $ps2db = $stmt->fetchAll();		
		
		
				
	
	if ($os == "") { echo "Unable to detect operating system, edptool has exited"; exit; }

?>
