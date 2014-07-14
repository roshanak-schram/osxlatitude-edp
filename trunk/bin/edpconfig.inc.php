<?php

// Main vars
if(is_dir("/Extra/EDP")) {
  $workpath = "/Extra/EDP";
}
else {
  $workpath = "/Extra";
}


// SQLite stuff :) which is accessed globally by every php file
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

//Populate cpufixes array
$stmt = $edp_db->query("SELECT * FROM pmfixes order by id");
$stmt->execute(); $cpufixdb = $stmt->fetchAll();

//Populate fixes array
$stmt = $edp_db->query("SELECT * FROM genfixes order by id");
$stmt->execute(); $fixesdb = $stmt->fetchAll();

//Populate Chameleon mods array
$stmt = $edp_db->query("SELECT * FROM chammods order by id");
$stmt->execute(); $chamdb = $stmt->fetchAll();

//Populate Optional packs array
$stmt = $edp_db->query("SELECT * FROM optionalpacks order by id");
$stmt->execute(); $optdb = $stmt->fetchAll();


$localrev 	= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
$localrev 	= str_replace("Last Changed Rev: ", "", $localrev);


// Include general functions and classes
require_once "$workpath/bin/functions.inc.php";

// Set timezone to UTC
date_default_timezone_set('UTC');

$hibernatemode = exec("pmset -g | grep hibernatemode");
$hibernatemode = str_replace("hibernatemode", "", $hibernatemode);
$hibernatemode = str_replace(" ", "", $hibernatemode);

//OS version detection stuff
$os_string = "";
$os        = getVersion();
$version   = "Rev: $localrev";
if ($os == "") { echo "Unable to detect operating system, edptool has exited"; exit; }

$donateurl = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=mail%40r2x2%2ecom&lc=US&item_name=OSXlatitude%20Donation&item_number=OSXLatitude%20Donation&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHostedGuest";


// funtion that reads path details from config table
function getConfig($name) {
    global $edp_db;
    
    $stmt = $edp_db->query("SELECT * FROM config where name = '$name'");
    $stmt->execute();
    $cfgrow = $stmt->fetchAll();

    return $cfgrow[0]['value'];
}

?>