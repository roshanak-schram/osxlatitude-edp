<?php
include_once "../functions.inc.php";
include_once "../config.inc.php";


//Fetch the show type
$showtype 	= $_GET['showtype']; if (!$showtype) { $showtype = $_POST['showtype']; }
$action 	= $_POST['action'];

//Check if Action is set to "yes" (aka copy modules)
if ($action == "update") {
	$chamModConfig = array(
    	"ACPICodec" => $_POST['ChamModuleACPICodec'],
    	"FileNVRAM" => $_POST['ChamModuleFileNVRAM'],     	
    	"KernelPatcher" => $_POST['ChamModuleKernelPatcher'],
    	"Keylayout" => $_POST['ChamModulekeylayout'],
    	"klibc" => $_POST['ChamModuleklibc'],	
    	"Resolution" => $_POST['ChamModuleResolution'],
    	"Sata" => $_POST['ChamModuleSata'],	
    	"uClibcxx" => $_POST['ChamModuleuClibcxx'],	
  	    	    	    		
    );

    copyChamModules($chamModConfig);
    $chamModConfig = "";
}




//-------------------------------------------------------------------------------------------------> Code for showing info -----------------------------------------

//If the showtype is set to standalone we asume that its being called from the sidebar menu under configuration
if ($showtype == "standalone") {

	//Get the current config
	$chamModConfig = chamModGetConfig();
	
	//Write out the header
	include "header.inc.php";
	
	//Write out the top menu
	echo "<div class='pageitem_top'><img src='icons/installer.png'><span>Chameleon modules</span></div>\n";
	echo "<div class='pageitem_bottom'>\n";
	echo "<br>Use this to configure wich modules you want to be used by chameleon - these settings might be overwritten when you do a new build...<br><br>";
	
	//Write out the form header
	echo "<form action='module.configuration.chameleonmods.php' method='post'>\n";
}


//Write out the config - we will use the current config based on the - this HTML is common for all show types
echo "<ul class='pageitem'>";
	checkbox("ACPICodec.dylib:", "ChamModuleACPICodec", $chamModConfig['ACPICodec']);	
	checkbox("FileNVRAM.dylib:", "ChamModuleFileNVRAM", $chamModConfig['FileNVRAM']);	
	checkbox("KernelPatcher.dylib:", "ChamModuleKernelPatcher", $chamModConfig['KernelPatcher']);
	checkbox("Keylayout.dylib:", "ChamModulekeylayout", $chamModConfig['Keylayout']);
	checkbox("klibc.dylib:", "ChamModuleklibc", "yes");
	checkbox("Resolution.dylib:", "ChamModuleResolution", $chamModConfig['Resolution']);
	checkbox("Sata.dylib:", "ChamModuleSata", $chamModConfig['Sata']);
	checkbox("uClibcxx.dylib:", "ChamModuleuClibcxx", "yes");	
		
echo "</ul><br>";



					
//Finish html for standalone mode
if ($showtype == "standalone") {
	echo "</div>";
	echo "<input type='hidden' name='action' value='update'>";
	echo "<input type='hidden' name='showtype' value='standalone'>";	
	echo "<ul class='pageitem'>\n";
	echo "	<li class='button'><input name='Submit input' type='submit' value='Save changes' /></li>\n";
	echo "</ul></form>\n";
	
}


//-------------------------------------------------------------------------------------------------> Code for showing info -----------------------------------------




	
	
?>

