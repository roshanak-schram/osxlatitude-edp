<?php



//Checking remote svn version and comparing to local
$localrev 		= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
$localrev 		= str_replace("Last Changed Rev: ", "", $localrev);
$remoterev      = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
$remoterev      = str_replace("Last Changed Rev: ", "", $remoterev);
$number_updates = ($remoterev - $localrev);	
$_SESSION['remoterev'] 		= $remoterev;
$_SESSION['localrev'] 		= $localrev;
$_SESSION['number_updates'] = $number_updates;		


// Get Vars from config storage
$edpversion = getConfig('edpversion');	$_SESSION['edpversion'] = $edpversion;
$verbose    = getConfig('verbose');		$_SESSION['verbose'] 	= $verbose;
$ee         = getConfig('ee');			$_SESSION['ee'] 		= $ee;
$rootpath   = getConfig('rootpath');	$_SESSION['rootpath'] 	= $rootpath;
$slepath    = getConfig('slepath');		$_SESSION['slepath'] 	= $slepath;
$cachepath  = getConfig('cachepath');	$_SESSION['cachepath'] 	= $cachepath;
$incpath    = getConfig('incpath');		$_SESSION['incpath'] 	= $incpath;


// Generate multidimensional arrays - this is instead of re-writting all code and to keep support for console mode
// Audio
$stmt = $edp_db->query("SELECT * FROM audio order by id");
$stmt->execute(); $audiodb = $stmt->fetchAll();
$_SESSION['audiodb'] = $audiodb;

// Battery
$stmt = $edp_db->query("SELECT * FROM battery order by id");
$stmt->execute(); $batterydb = $stmt->fetchAll();
$_SESSION['batterydb'] = $batterydb;

// Ethernet
$stmt = $edp_db->query("SELECT * FROM ethernet order by id");
$stmt->execute(); $landb = $stmt->fetchAll();
$_SESSION['landb'] = $landb;

// Wifi
$stmt = $edp_db->query("SELECT * FROM wifi order by id");
$stmt->execute(); $wifidb = $stmt->fetchAll();
$_SESSION['wifidb'] = $wifidb;

// PS2
$stmt = $edp_db->query("SELECT * FROM ps2 order by id");
$stmt->execute(); $ps2db = $stmt->fetchAll();
$_SESSION['ps2db'] = $ps2db;
	

?>
