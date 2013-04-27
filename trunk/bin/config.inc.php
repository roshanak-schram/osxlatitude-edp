<?php

// Main vars
$workpath = "/Extra";

//Check if db exists, and if not.. download it... 
if (!is_file("$workpath/bin/edp.sqlite3")) { system("curl -o /Extra/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php"); }

// SQLite stuff :)
$edp_db = new PDO("sqlite:/$workpath/bin/edp.sqlite3");
$edp_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$edpversion = getConfig('edpversion');
$verbose    = getConfig('verbose');	
$ee         = getConfig('ee');			
$rootpath   = getConfig('rootpath');
$slepath    = getConfig('slepath');	
$cachepath  = getConfig('cachepath');
$incpath    = getConfig('incpath');




//Populate Audio array
$stmt = $edp_db->query("SELECT * FROM audio order by id");
$stmt->execute(); $audiodb = $stmt->fetchAll();

//Populate Battery array
$stmt = $edp_db->query("SELECT * FROM battery order by id");
$stmt->execute(); $batterydb = $stmt->fetchAll();

//Populate Ethernet array
$stmt = $edp_db->query("SELECT * FROM ethernet order by id");
$stmt->execute(); $landb = $stmt->fetchAll();
$_SESSION['landb'] = $landb;

//Populate Wifi array
$stmt = $edp_db->query("SELECT * FROM wifi order by id");
$stmt->execute(); $wifidb = $stmt->fetchAll();

//Populate PS2 array
$stmt = $edp_db->query("SELECT * FROM ps2 order by id");
$stmt->execute(); $ps2db = $stmt->fetchAll();

//Populate Fakesmc array
$stmt = $edp_db->query("SELECT * FROM fakesmc order by id");
$stmt->execute(); $fakesmcdb = $stmt->fetchAll();

//Populate Optional array
$stmt = $edp_db->query("SELECT * FROM optionalpacks order by id");
$stmt->execute(); $optdb = $stmt->fetchAll();


$localrev 		= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
$localrev 		= str_replace("Last Changed Rev: ", "", $localrev);
//$remoterev      = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
//$remoterev      = str_replace("Last Changed Rev: ", "", $remoterev);
//$number_updates = ($remoterev - $localrev);	
//$_SESSION['remoterev'] 		= $remoterev;

// Include general functions
require_once "$workpath/bin/functions.inc.php";


//Set timezone to UTC
date_default_timezone_set('UTC');

//Get system vars
//$workpath	= getenv('PWD');  //Old detection.. not used anymore.. but we'll keep it around for now....


$hibernatemode = exec("pmset -g | grep hibernatemode");
$hibernatemode = str_replace("hibernatemode", "", $hibernatemode);
$hibernatemode = str_replace(" ", "", $hibernatemode);

//OS version detection stuff
$os_string = "";
$os        = getVersion();
$version   = "Rev: $localrev";
if ($os == "") { echo "Unable to detect operating system, edptool has exited"; exit; }

$donateurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mail%40r2x2%2ecom&lc=US&item_name=OSXlatitude%20Donation&item_number=OSXLatitude%20Donation&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest";


//Configuration functions
function getConfig($name) {
    global $edp_db;
    
    $stmt = $edp_db->query("SELECT * FROM config where name = '$name'");
    $stmt->execute();
    $bigrow = $stmt->fetchAll();

    return $bigrow[0]['value'];
}


?>