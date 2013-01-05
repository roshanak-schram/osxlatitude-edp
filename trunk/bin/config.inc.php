<?
	include_once "functions.inc.php";
	
	$verbose 	= "true";



	//Get system vars
	$workpath	= getenv('PWD');
	$ee			= "$workpath/Extensions";
	$strip		= str_replace("/", "", "$workpath");		
	$rootpath	= str_replace("Extra", "", "$workpath");
	$slepath	= "".$rootpath."System/Library/Extensions";
	$cachepath	= "".$rootpath."System/Library/Caches/com.apple.kext.caches/Startup";

    $localrev	= exec("cd $workpath; svn info --username osxlatitude-edp-read-only --non-interactive | grep -i \"Last Changed Rev\"");
    $localrev	= str_replace("Last Changed Rev: ", "", $localrev);
    $hibernatemode = exec("pmset -g | grep hibernatemode"); $hibernatemode = str_replace("hibernatemode", "", $hibernatemode); $hibernatemode = str_replace(" ", "", $hibernatemode);
 
	$os			= getVersion();  	
	$verbose 	= "1";


	$version 	= "Rev: $localrev";
	$header 	= "-- OSXLatitude E.D.P Version #4 ($version) ------------------------------------------------------------------";
	$footer		= "---------------------------------------------------------------------------- O S X L A T I T U D E . C O M --";
	


	
	if ($os == "") { echo "Unable to detect operating system, edptool has exited"; exit; }

?>
