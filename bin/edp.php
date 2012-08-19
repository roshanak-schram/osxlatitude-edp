<?
	//Include basic configuration
	include "config.inc.php";

	
	//Include functions
	include_once "functions.inc.php";
	
	//Include the model info 
	include "modelinfo.inc.php";
	include "ps2db.inc.php";
	include "audiodb.inc.php";
	include "batterydb.inc.php";
	
	//Include different sections
	include "section.main.inc.php";
	include "section.kextconfig.inc.php";
	include "section.fixes.inc.php";
	include "section.installer.inc.php";

	//Lets start by showing the main menu and get the results of the choice into $choice
	loadMainSystem();
	
		


	//Documention of functions
	
	// 	loadMainSystem();			Loads the main menu
	//	loadKextConfig();			Loads the Kext config system
	//	loadFixSystem();			Loads the fix system
 

?>

