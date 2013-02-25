<?php

	//SQLite stuff :)
	$edp_db = new PDO("sqlite:$workpath/bin/edp.sqlite3");
	$edp_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	function getConfig($name) {
		global $edp_db;
		$stmt = $edp_db->query("SELECT * FROM config where name = '$name'");
		$stmt->execute();
		return "$bigrow[0]['value']";
	}
		
	//Generate multidimensional arrays - this is instead of re-writting all code and to keep support for console mode
		//audio
		$stmt = $edp_db->query("SELECT * FROM audio order by id");
		$stmt->execute(); $audiodb = $stmt->fetchAll();
		//Battery
		$stmt = $edp_db->query("SELECT * FROM battery order by id");
		$stmt->execute(); $batterydb = $stmt->fetchAll();		
		//Ethernet
		$stmt = $edp_db->query("SELECT * FROM ethernet order by id");
		$stmt->execute(); $landb = $stmt->fetchAll();
		//wifi
		$stmt = $edp_db->query("SELECT * FROM wifi order by id");
		$stmt->execute(); $wifidb = $stmt->fetchAll();		
		//ps2
		$stmt = $edp_db->query("SELECT * FROM ps2 order by id");
		$stmt->execute(); $ps2db = $stmt->fetchAll();		
		
		
		


	//Path of EDP
	
	
	//Get Vars from config storage
	$edpversion = getConfig('edpversion');
	$verbose 	= getConfig('verbose');
	
	$workpath	= getConfig('workpath');
	$ee			= getConfig('ee');
	$rootpath	= getConfig('rootpath');
	$slepath	= getConfig('slepath');
	$cachepath	= getConfig('cachepath');
	$incpath	= getConfig('incpath');
	
	date_default_timezone_set('UTC');
	
	include_once "$workpath/bin/functions.inc.php";
	
	


	//Get system vars
	//$workpath	= getenv('PWD');  //Old detection.. not used anymore.. but we'll keep it around for now....
    $localrev	= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
    $localrev	= str_replace("Last Changed Rev: ", "", $localrev);
    $hibernatemode = exec("pmset -g | grep hibernatemode"); $hibernatemode = str_replace("hibernatemode", "", $hibernatemode); $hibernatemode = str_replace(" ", "", $hibernatemode);

    //OS version detection stuff
 	$os_string  = "";
	$os			= getVersion();	
	$version 	= "Rev: $localrev";
	if ($os == "") { echo "Unable to detect operating system, edptool has exited"; exit; }
	


?>
