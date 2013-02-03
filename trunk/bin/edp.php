<?
	//Include basic configuration
	include "config.inc.php";

	//Include the custom config
	if (is_file("$workpath/edpconfig.php")) { include "$workpath/edpconfig.php"; } else { include "$workpath/bin/edpconfig.php";}
	
	//Include functions
	include_once "functions.inc.php";
	
	//Include the model info 
	include "vendordb.inc.php";
	include "ps2db.inc.php";
	include "audiodb.inc.php";
	include "batterydb.inc.php";
	
	//Include different sections
	include "section.main.inc.php";
	include "section.kextconfig.inc.php";



	//Lets start by showing the main menu and get the results of the choice into $choice
	loadMainSystem();
	
		


	//Documention of functions
	
	// 	loadMainSystem();			Loads the main menu
	//	loadKextConfig();			Loads the Kext config system
	//	loadFixSystem();			Loads the fix system
 

?>

