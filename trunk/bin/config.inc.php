<?php

// Main path
$workpath = "/Extra";

// SQLite stuff :)
$edp_db = new PDO("sqlite:/$workpath/bin/edp.sqlite3");
$edp_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



//Initiate preloader
session_start();
$remoterev 		= $_SESSION['remoterev'];
$number_updates = $_SESSION['number_updates'];
$audiodb		= $_SESSION['audiodb'];
$batterydb		= $_SESSION['batterydb'];		
$landb			= $_SESSION['landb'];
$wifidb			= $_SESSION['wifidb'];
$ps2db			= $_SESSION['ps2db'];

$edpversion		= $_SESSION['edpversion'];
$verbose		= $_SESSION['verbose'];
$ee				= $_SESSION['ee'];
$rootpath		= $_SESSION['rootpath'];				
$slepath		= $_SESSION['slepath'];
$cachepath		= $_SESSION['cachepath'];
$incpath		= $_SESSION['incpath'];


// Include general functions
require_once "$workpath/bin/functions.inc.php";

//Checking if ONE of the pre-loaded vars is set, if not we will include loader.php to load them
if ($remoterev == "" || $rootpath == "") {
	include "html/loader.php";
}




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

?>