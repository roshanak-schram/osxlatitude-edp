<?php

  //Include builder class
  include_once "html/classes/builder.php";
  include_once "html/classes/chamModules.php";
  include_once "html/classes/edp.php";
  include_once "html/classes/nvram.php";
  include_once "html/classes/kexts.php";  
  
  
  
//------------------> EDPweb functions -----------------------------------------------------------------------------------------------

function checkbox($title, $formname, $status) {
	if ($status == "yes") { $c = "checked"; }
	echo "<li class='checkbox'><span class='name'>$title</span><input name='$formname' type='checkbox' $c/> </li>\n";
}

//Writes out the html for the pageitemtop
function echoPageItemTOP($icon, $text) {
	echo "<div class='pageitem_top'><img src='$icon'><span><b>$text</span></div></b>\n";
}


//<-----------------------------------------------------------------------------------------------------------------------------------

//--- Get Value from Key in SMbios.plist
include_once __DIR__ . '/vendor/CFPropertyList/CFPropertyList.php';

function getValueFromSmbios($key, $default = null) {
    global $workpath;

    $file = $workpath . '/smbios.plist';

    if (file_exists($file)) {
        $plist = new CFPropertyList\CFPropertyList($file, CFPropertyList\CFPropertyList::FORMAT_XML);
        $dict  = $plist->toArray();

        if (array_key_exists($key, $dict)) {
            return $dict[$key];
        }
    }

    return $default;
}
//---

//--- Get EDP builder Model / Vendor / Serie values for the user to select from
function builderGetVendorValues() {
    global $edp_db;

    $result = $edp_db->query('SELECT DISTINCT vendor FROM models ORDER BY vendor');
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['vendor'] . '">&nbsp;&nbsp;' . $row['vendor'] . '</option>';
    }

    return $return;
}
function builderGetSerieValues($vendor) {
    global $edp_db;

    $result = $edp_db->query('SELECT DISTINCT serie, vendor FROM models WHERE vendor = "' . $vendor . '" ORDER BY serie');
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['serie'] . '">&nbsp;&nbsp;' . $row['vendor'] . ' ' . $row['serie'] . '</option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select serie...</option>' . $return;
}
function builderGetModelValues($vendor, $serie) {
    global $edp_db;

    $result = $edp_db->query('SELECT * FROM models WHERE vendor = "' . $vendor . '" AND serie = "' . $serie . '" ORDER BY type');
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['id'] . '">&nbsp;&nbsp;' . $row[desc] . ' (' . $row['type'] . ')</option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select model...</option>' . $return;
}
//---

			
function updateCham() {
    // Note: Overtime we will add a function to make sure that the user have the latest version 
    // of cham distrobuted with EDP - until then, we will force the update on each build    
    echo "  Updating Chameleon to latest versions from EDP \n";
     system_call("cp -f $workpath/boot /");
}


function EDPdoBuild() {
	global $modeldb; global $modelID; global $workpath; global $rootpath;

	//Start by defining our log file and cleaning it..
	$log = "$workpath/build.log";
	if (is_file("$log")) { 
		system_call("rm -Rf $log"); 
		system_call("<br>echo Building....<br><br> >$log");
	}
		
	//Check if myhack is up2date and ready for combat
	myHackCheck();
			
	//Step 1
	writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
	copyEssentials();

	//Step 2
	writeToLog("$workpath/build.log", "<br><b>Step 3) Preparing kexts for myHack.kext </b><br>");
	copyKexts();
			
	//Step 3
	writeToLog("$workpath/build.log", "<br><br><b>Step 4) Applying Chameleon settings.. </b><br>");
	updateCham();
	writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
	copyChamModules($modeldb[$modelID]);
			
	writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
				lastMinFixes();
					
	//Step 4
	writeToLog("$workpath/build.log", "<br><b>Step 6) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
	system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
	writeToLog("$workpath/build.log", "<a name='myfix'></a>");
				
	echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";

	exit;
        		
}



function checkSVNrevs() {
    global $localrev, $workpath;

    $remoterev = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
    $remoterev = str_replace("Last Changed Rev: ", "", $remoterev);

    if ($localrev < $remoterev) {
        echo "\n   ---------------------------------------------------------------------------------------\n";
        echo "        !!! There is an update of EDP, please run option 2 to download the update !!!\n";
        echo "   ---------------------------------------------------------------------------------------\n\n";
    }
}
	
/**
 * Patch AHCI
 * @see http://www.insanelymac.com/forum/topic/280062-waiting-for-root-device-when-kernel-cache-used-only-with-some-disks-fix/page__st__60#entry1851722
 */
function patchAHCI() {
	global $workpath,$slepath, $ee;
    system_call("cp -R $slepath/IOAHCIFamily.kext $ee");
    //system_call("perl /Extra/EDP/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
    system_call("perl $workpath/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
}


/**
 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 */
function kextpackLoader($name) {
	global $edp_db, $workpath, $edp;
	if ($name != "") {
		$workfolder = "$workpath/kpsvn/$name";
		if (is_dir("$workfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force update $workfolder");
		}
		else {
			system_call("mkdir $workfolder; cd $workfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$name . >>$workpath/kpload.log");
		}
	}
} 

 
/**
 * Function to check if the model is allready checked out
 * if the model is not checked out it will check it out
 */
function svnModeldata($model) {
    global $workpath; global $edp;
    $modelfolder = "$workpath/model-data/$model";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force update $modelfolder");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/model-data/$model .");
    }
}
	
/**
 * Function to check if myhack.kext exists in ale, and if it dosent for some weird reason... copy it there...
 */
function myHackCheck() {
    global $workpath, $slepath;

    if (!is_dir("$slepath/myHack.kext")) {
        $myhackkext = "$workpath/myHack.kext";
        system_call("rm -Rf `find -f path \"$myhackkext\" -type d -name .svn`");
        system_call("cp -R \"$workpath/myHack.kext\" $slepath");
    }
    if (!is_file("/usr/sbin/")) {
        system_call("cp \"$workpath/bin/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix");
    }
}
	
function edpCleaner() {
    global $slepath;
    
    if ($slepath != "") {
        if (!is_dir("$slepath/0EDP.kext")) {
            system_call("rm -Rf $slepath/0EDP.kext");
        }
    }
}


	
/**
 * Apply GMA950brightnessfix
 */
function GMA950brightnessfixCheck() {
    global $os, $workpath, $ee, $modeldb, $modelID;
    
    $needfix = $modeldb[$modelID]["useGMA950brightfix"];
    
    if ($needfix == "yes") {
        echo "  Applying GMA950 Brightness fix \n";
        //Syncing kextpack to local storage
    		if(!is_dir("$workpath/kpsvn/Display"));
    			system_call("mkdir $workpath/kpsvn/Display");
    			
    		kextpackLoader("Display/gma950-brightness-fix");
    		
        system_call("cp -R $workpath/kpsvn/Display/gma950-brightness-fix/AppleIntelIntegratedFramebuffer.kext $ee");
    }
}	
	

/**
 * replace system_call() .. works with LWS also
 */
function system_call($data) {
    passthru("$data");
    echo str_repeat(' ', 254);
    flush();
}
	
function downloadAndRun($url, $filetype, $filename, $execpath) {
    echo "Making downloads folder in /Downloads and initiating download of $url\n\n";
    system_call("mkdir /downloads; cd /downloads; curl -O $url");
    echo "Mounting $filename... \n\n";
    
    if ($filetype == "dmg") {
        system_call("hdiutil attach /downloads/$filename >/dev/null");
    }
    
    echo "Executing the package installer... \n\n";
    system_call("open $execpath");
}	
	


// VGA and HDMI patch for Intel HD3000 GPU
function patchAppleIntelSNBGraphicsFB() {

    global $slepath;
    
    $patchedInfoFile = "$slepath/AppleIntelSNBGraphicsFB.kext/Contents/KextPatched.plist";
    if (!file_exists($patchedInfoFile)) {
    system_call('sudo perl -pi -e \'s|\x01\x02\x04\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x02\x05\x00\x00\x00\x04\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00\x04\x06\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00|\x01\x02\x03\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x06\x02\x00\x00\x00\x01\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x08\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00|g\' $slepath/AppleIntelSNBGraphicsFB.kext/Contents/MacOS/AppleIntelSNBGraphicsFB');
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\(OSXLatitude\)\"\" $slepath/AppleIntelSNBGraphicsFB.kext/Contents/KextPatched.plist");  
    }
}

function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    
    $patchedInfoFile = "$slepath/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist";
    if (!file_exists($patchedInfoFile)) {
    //system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' $slepath/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"Leon\ and\ EMlyDinEsH\(OSXLatitude\)\"\" $slepath/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist");  
    }
}

// WiFI and Bluetooth Kext Patching
//<-----------------------------------------------------------------------------------------------------------------------------------

/*
 * Patch AirPortAtheros40.kext for the card AR5B95/AR5B195 from Lion onwards
 */
function patchWiFiAR9285AndAR9287() {
	global $slepath;
	echo "  Applying AR9285/AR9287 WiFi Fix for AR5B195/AR5B95 and AR5B197\n";

	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist"); 
    }
    else { echo "  AirPortAtheros40.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}


/*
 * Patch AirPortBrcm4360.kext and BroadcomBluetoothHostControllerUSBTransport.kext for the card BCM94352HMB from Mountain Lion 10.8.5 onwards
 */
function patchWiFiBTBCM4352() {
	global $ee, $slepath;
	echo "  Applying BCM4352 WiFi Fix for BCM94352HMB card\n";
	
	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist");
    }
    else { echo "  AirPortBrcm4360.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
    }
   
    echo "  Applying BCM20702A1 Bluetooth Fix for BCM94352HMB card\n";
    
    $patchedInfoFile = "$slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
    $file = "$slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist";   if (file_exists($file)) {
    // Patch kext
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404 dict\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:CFBundleIdentifier string \"com.apple.iokit.BroadcomBluetoothHostControllerUSBTransport\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:IOClass string \"BroadcomBluetoothHostControllerUSBTransport\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:IOProviderClass string \"IOUSBDevice\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:LMPLoggingEnabled bool \"NO\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:idProduct integer \"13316\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:idVendor integer \"5075\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/KextPatched.plist");
    system_call("sudo touch /System/Library/Extensions/");
    }
    else { echo "  BroadcomBluetoothHostControllerUSBTransport.kext not found for patching in System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/\n"; }
  }
    
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card Dell DW1395, DW1397 from Lion onwards
 */
function patchDW13957WiFiBCM43224() {
	global $slepath;
	echo "  Applying BCM43224 WiFi Fix for Dell DW1395, DW1397 \n";
	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}

/*
 * Patch AppleAirPortBrcm4311.kext for the card Dell DW1395, DW1397 in Snow Leopard
 */
function patchDW13957WiFiBCM4311() {
	global $slepath;
	echo "  Applying BCM4311 WiFi Fix for Dell DW1395, DW1397 \n";
	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm4311.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM43224() {
	global $slepath;
	echo "  Applying BCM43224 WiFi Fix for BCM943224 HMS and BCM943225 HMB \n";
	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4353\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
   }
}

/*
 * Patch AirPortBrcm4331.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM4331() {
	global $slepath;
	echo "  Applying BCM4331 WiFi Fix for BCM943225 HMB \n";
	$patchedInfoFile = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist");
    }
     else { echo "  AirPortBrcm4331.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
   }
}

//<-----------------------------------------------------------------------------------------------------------------------------------

	
function getVersion() {
    global $rootpath, $os_string;

    $path = "".$rootpath."System/Library/CoreServices/SystemVersion";
    $v = exec("defaults read $path ProductVersion");
    $r = '';

    if ($v == "10.6")   { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }		
    if ($v == "10.6.0") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.1") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.2") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.3") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }						
    if ($v == "10.6.4") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.5") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.6") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.7") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.8") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.6.9") { $r="sl"; $os_string = "MacOS X Snow Leopard $v"; }
    if ($v == "10.7")   { $r="lion"; $os_string = "MacOS X Lion $v"; }			
    if ($v == "10.7.0") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.1") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.2") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.3") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.4") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.5") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.6") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.7.7") { $r="lion"; $os_string = "MacOS X Lion $v"; }
    if ($v == "10.8")   { $r="ml"; $os_string = "OSX Mountain Lion $v"; }
    if ($v == "10.8.0") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
    if ($v == "10.8.1") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
    if ($v == "10.8.2") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
    if ($v == "10.8.3") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }
    if ($v == "10.8.4") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }	
    if ($v == "10.8.5") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }
    if ($v == "10.8.6") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }    	
    if ($v == "10.9") 	{ $r="mav"; $os_string = "OSX Maverick $v"; }	
    if ($v == "10.9.0") { $r="mav"; $os_string = "OSX Maverick $v"; }	
    if ($v == "10.9.1") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.2") { $r="mav"; $os_string = "OSX Maverick $v"; }
    if ($v == "10.9.3") { $r="mav"; $os_string = "OSX Maverick $v"; }				
    return $r;
}
	
function getMacOSXVersion() {
		$path = "/System/Library/CoreServices/SystemVersion";
    	$ver = exec("defaults read $path ProductVersion");
    	return $ver;
}

function AppleACPIfixCheck() {
    global $ee, $workpath, $slepath, $modeldb, $modelID;

    //Check if ACPIfix is selected
    if ($modeldb[$modelID]["useACPIfix"] == "yes") {
        echo "  Applying ACPI fix (Coolbook fix)\n";
        
        if(!is_dir("$workpath/kpsvn/ACPI"));
    			system_call("mkdir $workpath/kpsvn/ACPI");
    			
    		kextpackLoader("ACPI/coolbook-fix");
    		
        system_call("cp -R $workpath/kpsvn/ACPI/coolbook-fix/AppleACPIPlatform.kext $ee");

        if (is_dir("$slepath/AppleACPIPlatform.kext")) {
            //Create backup folder
            date_default_timezone_set('UTC');
            $date = date("d-m-Y");
            $backupfolder = "/backup/$date-AppleACPIPlatform.kext-$kver2";
            system_call("mkdir /backup");
            system_call("mkdir $backupfolder");
            system_call("mv $slepath/AppleACPIPlatform.kext $backupfolder");
        }
    }
}

function kernelcachefix() {
    global $workpath, $rootpath;
    
    $chkdir = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup";
    $kerncachefile = $rootpath . "/System/Library/Caches/com.apple.kext.caches/Startup/kernelcache";

    if (!is_dir("$chkdir") && ($workpath == "/Extra/EDP" || $workpath == "/Extra")) {
        system_call("mkdir $chkdir");
        if (file_exists($kerncachefile)) {
            echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
            system_call("kextcache -system-prelinked-kernel");
        }
    }
}
function copyEssentials() {
    global $workpath, $incpath, $modeldb, $modelID, $os; global $edp;

    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "Cleaning up by system...<br>");
    edpCleaner();

	$extrapath = "/Extra";
	
    $edp->writeToLog("$workpath/build.log", "  Copying System plists and dsdt.aml from $workpath/model-data/$modelName to $extrapath<br>");
    
    $file1 = "$workpath/model-data/$modelName/common/SMBios.plist"; $file2 = "$workpath/model-data/$modelName/$os/SMBios.plist"; 
    if((file_exists($file1)) || (file_exists($file2))) {
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/SMBios.plist")) { system_call("rm $extrapath/SMBios.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    }
    
    $file1 = "$workpath/model-data/$modelName/common/org.chameleon.Boot.plist"; $file2 = "$workpath/model-data/$modelName/$os/org.chameleon.Boot.plist"; 
    if((file_exists($file1)) || (file_exists($file2))) {
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/org.chameleon.Boot.plist")) { system_call("rm $extrapath/org.chameleon.Boot.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    }
    
    $file1 = "$workpath/model-data/$modelName/common/dsdt.aml"; $file2 = "$workpath/model-data/$modelName/$os/dsdt.aml"; 
    if((file_exists($file1)) || (file_exists($file2))) {
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/dsdt.aml")) { system_call("rm $extrapath/dsdt.aml"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    }
	
    // If its mavericks then copy the files from ml folder for now
    if($os == "mav" && !is_dir("$workpath/model-data/$modelName/$os")) {
    $edp->writeToLog("$workpath/build.log", "  mavericks directory is not found, Copying dsdt and plist files from ml folder..<br>");
    $file = "$workpath/model-data/$modelName/ml/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    $file = "$workpath/model-data/$modelName/ml/SMBios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/ml/org.chameleon.Boot.plist";  if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    }						

	// set UseKernelCache to Yes from org.chameleon.Boot.plist
	system("sudo /usr/libexec/PlistBuddy -c \"set UseKernelCache Yes\" $extrapath/org.chameleon.Boot.plist");
	
    $edp->writeToLog("$workpath/build.log", "  Checking if your model includes SSDT dump files - will copy if any exists..<br>");
    $file = "$workpath/model-data/$modelName/common/SSDT.aml";   if (file_exists($file)) 
    { 
    	system_call("cp -f $file $extrapath");
    	// set DropSSDT to Yes from org.chameleon.Boot.plist
		system("sudo /usr/libexec/PlistBuddy -c \"set DropSSDT Yes\" $extrapath/org.chameleon.Boot.plist"); 
    }

    
    $file = "$workpath/model-data/$modelName/common/SSDT-1.aml"; if (file_exists($file)) { 
    if (file_exists("$extrapath/SSDT-1.aml")) { system_call("rm $extrapath/SSDT-1.aml"); }
    system_call("cp -f $file $extrapath"); 
    }
    $file = "$workpath/model-data/$modelName/common/SSDT-2.aml"; if (file_exists($file)) { 
    if (file_exists("$extrapath/SSDT-2.aml")) { system_call("rm $extrapath/SSDT-2.aml"); }
    system_call("cp -f $file $extrapath"); 
    }
    $file = "$workpath/model-data/$modelName/common/SSDT-3.aml"; if (file_exists($file)) { 
    if (file_exists("$extrapath/SSDT-3.aml")) { system_call("rm $extrapath/SSDT-3.aml"); }
    system_call("cp -f $file $extrapath"); 
    }
    $file = "$workpath/model-data/$modelName/common/SSDT-4.aml"; if (file_exists($file)) {
    if (file_exists("$extrapath/SSDT-4.aml")) { system_call("rm $extrapath/SSDT-4.aml"); } 
    system_call("cp -f $file $extrapath"); 
    }
    $file = "$workpath/model-data/$modelName/common/SSDT-5.aml"; if (file_exists($file)) {
    if (file_exists("$extrapath/SSDT-5.aml")) { system_call("rm $extrapath/SSDT-5.aml"); } 
    system_call("cp -f $file $extrapath"); 
    }			


    //Copy essentials from /Extra/include if user has
    $edp->writeToLog("$workpath/build.log", "  Checking if we have any essential files in $incpath that will be used instead...<br>");
    if (is_file("$incpath/smbios.plist")) 				{ system_call("cp -f $incpath/smbios.plist /Extra"); }
    if (is_file("$incpath/org.chameleon.Boot.plist")) 	{ system_call("cp -f $incpath/org.chameleon.Boot.plist /Extra"); }
    if (is_file("$incpath/dsdt.aml")) 					{ system_call("cp -f $incpath/dsdt.aml /Extra"); }
    if (is_file("$incpath/SSDT.aml")) 					{ system_call("cp -f $incpath/SSDT.aml /Extra"); }
    if (is_file("$incpath/SSDT-1.aml")) 				{ system_call("cp -f $incpath/SSDT-1.aml /Extra"); }
    if (is_file("$incpath/SSDT-2.aml")) 				{ system_call("cp -f $incpath/SSDT-2.aml /Extra"); }
    if (is_file("$incpath/SSDT-3.aml")) 				{ system_call("cp -f $incpath/SSDT-3.aml /Extra"); }    
    if (is_file("$incpath/SSDT-4.aml")) 				{ system_call("cp -f $incpath/SSDT-4.aml /Extra"); }
    if (is_file("$incpath/SSDT-5.aml")) 				{ system_call("cp -f $incpath/SSDT-5.aml /Extra"); }   
    
    // Copy Themes folder from EDP workpath to Extra
    if (!is_dir("/Extra/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying Standard themes folder to /Extra<br>");
    	system_call("mkdir /Extra/Themes");
		system_call("cp -R $workpath/Themes/. /Extra/Themes");
     } 
}



//Copying kexts 
function copyKexts() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modelID, $os, $ee, $batterydb, $landb, $fakesmcdb, $edp;
    
    //Get our class(s)
    global $builder;
	
	//kextpack svn path
	$kpsvn = "$workpath/kpsvn";
	
	if(!is_dir("$workpath/kpsvn"));
    			system_call("mkdir $workpath/kpsvn");
    			
    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "  Start by cleaning up in $ee..<br>");
    system_call("rm -Rf $ee/*.kext");

    /*********************** Begin Kexts related to Hardware *****************************/
    
    //copying PS2 kexts from kextpacks
    $ps2id = $modeldb[$modelID]['ps2pack'];
    
    // remove voodooPS2 related files if installed before
    if($ps2id != "2" && $ps2id != "5" && $ps2id != "6")
    {
        	if(is_dir("/Library/PreferencePanes/VoodooPS2.prefpane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2.prefpane");}
        	if(file_exists("/usr/bin/VoodooPS2Daemon")) {system_call("rm -rf /usr/bin/VoodooPS2Daemon");}
        	if(file_exists("/Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist")) {system_call("rm -rf /Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist");}
        	if(is_dir("/Library/PreferencePanes/VoodooPS2synapticsPane.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2synapticsPane.prefPane");}
    }
    
    if ($ps2id != "" && $ps2id != "no") {
    	$name = $ps2db[$ps2id]["foldername"];
        if ($name != "") {  
        	 
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/PS2Touchpad"));
    			system_call("mkdir $kpsvn/PS2Touchpad");
    			
    		kextpackLoader("$name");
    		
    		$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kexts prefpanes<br>");
    		// Copy VodooPS2dameon and preference files
        	if($ps2id == "5")//VoodoPS2 Standard
        	 	system_call("cp -R $kpsvn/$name/VoodooPS2.prefpane /Library/PreferencePanes");
        	 	
        	 else if($ps2id == "6" || $ps2id == "2")//for new VoodooPS2 by RehabMan and ALPS modified by bpedman
        	 {
        	 	system_call("cp $kpsvn/$name/VoodooPS2Daemon /usr/bin");
        	 	system_call("cp $kpsvn/$name/org.rehabman.voodoo.driver.Daemon.plist /Library/LaunchDaemons");
        	 	system_call("cp -R $kpsvn/$name/VoodooPS2synapticsPane.prefPane /Library/PreferencePanes");
        	 }
        	 
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kexts ($kpsvn/$name) to $ee<br>");
    		if($ps2id == "6" || $ps2id == "2")
    		{
    			system_call("mkdir $ee/VoodooPS2Controller.kext");
    			system_call("cp -R $kpsvn/$name/VoodooPS2Controller.kext $ee");
    		}
    		else
    			system_call("cp -R $kpsvn/$name/. $ee");
    	}
	} 
	//Resetting $name
	$name = "";
	

    //copying Wifi/BT kexts from kextpacks/ patch Wifi/BT Kexts
    if ($modeldb[$modelID]['wifikext'] != "" && $modeldb[$modelID]['wifikext'] != "no") {
        $wifid = $modeldb[$modelID]['wifikext'];
        $name = $wifidb[$wifid]['kextname'];
        if ($name != "") {
        	
    		$edp->writeToLog("$workpath/build.log", "  Patching WiFi kext $name<br>");
    		
    		if($wifid == "0" || $wifid == "1")
    			patchWiFiAR9285AndAR9287();
    		else if($wifid == "2")
    			patchWiFiBTBCM4352();
    		else if($wifid == "3")
    			patchDW13957WiFiBCM43224();
    		else if($wifid == "4")
    			patchDW13957WiFiBCM4311();
    		else if($wifid == "5")
    			patchWiFiBCM43224();
    		else if($wifid == "6")
    			patchWiFiBCM4331();
    		
    		//Syncing kextpack to local storage
    		if($wifid < 3)
    		{
    			system_call("mkdir $kpsvn/Wireless");
    			kextpackLoader("Wireless/BluetoothFWUploader"); 
    			//Copying the kextpack to /Extra/Extentions
    			$edp->writeToLog("$workpath/build.log", "  Copying the Bluetooth kext ($workpath/kpsvn/Wireless/BluetoothFWUploader) to $ee<br>");
    			system_call("cp -R $workpath/kpsvn/Wireless/BluetoothFWUploader/. $ee");
    		}
    	}
	}
	//Resetting $name
	$name = "";    
    

    //copying fakesmc kexts from kextpacks
    $fakesmcid = $modeldb[$modelID]['fakesmc'];
    $name = $fakesmcdb[$fakesmcid]["foldername"]; 
    if ($modeldb[$modelID]['fakesmc'] != "" && $modeldb[$modelID]['fakesmc'] != "no" && $name != "") {   
    	
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/FakeSMC"));
    		system_call("mkdir $kpsvn/FakeSMC");
    		
    	kextpackLoader("$name");   
    		
    	//Copying the kextpack to /Extra/Extentions
    	$edp->writeToLog("$workpath/build.log", "  Copying the fakesmc kext ($kpsvn/$name) to $ee<br>");
    	system_call("cp -R $kpsvn/$name/. $ee");
    }
	//Resetting $name
	$name = ""; 
	
    	
    //copying audio kexts
    $audioid = $modeldb[$modelID]['audiopack'];
    $audiodir = $audiodb[$audioid]["foldername"]; $name = $audiodir;
    
    // remove voodooHDA related files if installed before
    if($audioid == "no" || $audioid == "builtin") {
        	 	if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
    }
    
    if ($modeldb[$modelID]['audiopack'] != "" && $modeldb[$modelID]['audiopack'] != "no") {
        $edp->writeToLog("$workpath/build.log", "  Copying the Audio kexts to $ee<br>");
        
        //Clean up
        if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
        
        if ($audioid == "builtin") {
        	if (is_dir("$workpath/model-data/$modelName/$os/applehda")) { system_call("cp -R $workpath/model-data/$modelName/$os/applehda/. $ee/"); }
        	else { 
        		if (is_dir("$workpath/model-data/$modelName/common/applehda")) { system_call("cp -R $workpath/model-data/$modelName/common/applehda/. $ee/"); }
        	}
        	 	
        } else { 
        	//Syncing kextpack to local storage
        	if(!is_dir("$kpsvn/Audio"));
    			system_call("mkdir $kpsvn/Audio");
    		
        	kextpackLoader("$name");
        	
        	//Copy Prefpane and Settings loader
        	kextpackLoader("Audio/Settings");
        	system_call("cp -R $kpsvn/Audio/Settings/VoodooHdaSettingsLoader.app /Applications");
        	system_call("cp $kpsvn/Audio/Settings/com.restore.voodooHDASettings.plist /Library/LaunchAgents");
        	system_call("cp -R $kpsvn/Audio/Settings/VoodooHDA.prefPane /Library/PreferencePanes");
        	 	
        	$edp->writeToLog("$workpath/build.log", "  Copying the $name kextpack ($kpsvn/$name) to $ee<br>");
        	system_call("cp -R $kpsvn/$name/. $ee");
        }   
    }
    //Resetting $name
	$name = ""; 
	

	//copying ethernet kexts from kextpacks
    if ($modeldb[$modelID]['ethernet'] != "" && $modeldb[$modelID]['ethernet'] != "no") {
        $lanid = $modeldb[$modelID]['ethernet'];
        $lankext = $landb[$lanid]['name'];
        $name = $landb[$lanid]['foldername'];
        if ($name != "") {
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/Ethernet"));
    			system_call("mkdir $kpsvn/Ethernet");
    		
    		//if there is no category folder then create it
    		if(!is_dir("$kpsvn/Ethernet/$name"));
    			system_call("mkdir $kpsvn/Ethernet/$name");
    			
    		//should change to Ethernet folder to create the kext
    		system_call("cd $kpsvn/Ethernet/$name");
    		
    		kextpackLoader("Ethernet/$name/$lankext");   
    		
    		if ($lankext != "") {
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the Ethernet kexts ($kpsvn/Ethernet/$name/$lankext) to $ee<br>");
            system_call("cp -R $kpsvn/Ethernet/$name/$lankext $ee/");
       	 }	
       }
	}

	//copying battery kexts from kextpacks
    if ($modeldb[$modelID]['batteryKext'] != "" && $modeldb[$modelID]['batteryKext'] != "no") {
        $battid = $modeldb[$modelID]['batteryKext'];
        $name = $batterydb[$battid]['foldername'];
        if ($name != "") {
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/Battery"));
    			system_call("mkdir $kpsvn/Battery");
    			
    		kextpackLoader("$name");   
    		
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the Battery kexts ($/kpsvn/$name) to $ee<br>");
    		system_call("cp -R $kpsvn/$name/. $ee");
    	}
	}
	
	//Resetting $name
	$name = ""; 
	
	
	 //Copy optional kexts
    $data = $modeldb[$modelID]['optionalpacks'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting foldername from ID
	    $categ = $builder->getCategoryNameFromID("optionalpacks", "$id");
        $name = $builder->getKextpackNameFromID("optionalpacks", "$id");
        
        if($id == "2") {
        $edp->writeToLog("$workpath/build.log", "  Patching AppleIntelSNBGraphicsFB.kext for VGA and HDMI in Intel HD3000<br>");
        patchAppleIntelSNBGraphicsFB();
        }
        
        else if ($name != "") { 
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/$categ"));
    			system_call("mkdir $kpsvn/$categ");
    		
    		if($id == "5") {
			//Choose new version 
    		if(getMacOSXVersion() >= "10.8.5")
    			kextpackLoader("$categ/GenericXHCIUSB3_New");
    		//chooose old version
    		else if(getMacOSXVersion() < "10.8.5")
    			kextpackLoader("$categ/$name");
    		}
    		else	
    		kextpackLoader("$categ/$name");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying optional kextpack: $kpsvn/$categ/$name to $ee<br>");
    		system_call("cp -R $kpsvn/$categ/$name/. $ee");
    	}
	}


 /*********************** End Kexts related to Hardware *****************************/
 
/*********************** Begin Fixes and Patches *****************************/
     $edp->writeToLog("$workpath/build.log", "  Applying fixes and patches... <br>");

	//Checking if we need to patch AppleIntelCPUPowerManagement.kext
    $pathCPU = $modeldb[$modelID]["patchCPU"];
    if ($pathCPU == "yes") {
        patchAppleIntelCPUPowerManagement();
    }
    
    //Checking if we need nullcpu
    if ($modeldb[$modelID]['nullcpu'] == "yes" || $modeldb[$modelID]['nullcpu'] == "y") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/PowerMgmt"));
    			system_call("mkdir $kpsvn/PowerMgmt");
    			
    		kextpackLoader("PowerMgmt/NullCPUPowerManagement.kext"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying NullCPUPowerManagement.kext for disabling Apples native power management.. <br>");
        system_call("cp -R $workpath/kpsvn/PowerMgmt/NullCPUPowerManagement.kext $ee");
    }

    //Checking if we need to patch AHCI
    if ($modeldb[$modelID]['patchAHCIml'] == "yes" || $modeldb[$modelID]['patchAHCIml'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Patching IOAHCIFamily.kext for OS: $os... <br>");
        if ($os == "ml") {
            patchAHCI();
        }
    } 
    

    //Checking if we need Sleepenabler
    if ($modeldb[$modelID]['sleepEnabler'] == "yes" || $modeldb[$modelID]['sleepEnabler'] == "y") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/PowerMgmt"));
    			system_call("mkdir $kpsvn/PowerMgmt");
    			
    		kextpackLoader("PowerMgmt/SleepEnabler.kext"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying SleepEnabler.kext for enabling sleep...<br>");
        system_call("cp -R $workpath/kpsvn/PowerMgmt/SleepEnabler.kext $ee");
    }

    if ($modeldb[$modelID]['loadIOATAFamily'] == "yes") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/Others"));
    			system_call("mkdir $kpsvn/Others");
    			
    		kextpackLoader("Others/IOATAFamily.kext"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying IOATAFamily.kext to $ee.. <br>");
        system_call("cp -R $workpath/kpsvn/Others/IOATAFamily.kext $ee");
    }

    if ($modeldb[$modelID]['loadNatit'] == "yes") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/Others"));
    			system_call("mkdir $kpsvn/Others");
    			
    		kextpackLoader("Others/Natit.kext"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying Natit.kext to $ee.. <br>");
        system_call("cp -R $workpath/kpsvn/Others/Natit.kext $ee");
    }

    if ($modeldb[$modelID]['tscsync'] == "yes" || $modeldb[$modelID]['tscsync'] == "y") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/PowerMgmt"));
    			system_call("mkdir $kpsvn/PowerMgmt");
    			
    		kextpackLoader("PowerMgmt/VoodooTSCSync.kext"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Check if we need VoodooTSCSync.kext for syncing CPU cores...<br>");
        system_call("cp -R $workpath/kpsvn/PowerMgmt/VoodooTSCSync.kext $ee");
    }

    if ($modeldb[$modelID]['emulatedST'] == "yes" || $modeldb[$modelID]['emulatedST'] == "y") {
    	//Syncing kextpack to local storage
    	if(!is_dir("$kpsvn/PowerMgmt"));
    			system_call("mkdir $kpsvn/PowerMgmt");
    			
    		kextpackLoader("PowerMgmt/VoodooPState"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Check if we are using emulated speedstep via voodoopstate and voodoopstatemenu <br>");
        system_call("cp -R $workpath/kpsvn/PowerMgmt/VoodooPState/VoodooPState.kext $ee");
        system_call("cp $workpath/kpsvn/PowerMgmt/VoodooPState/PStateMenu.plist /Library/LaunchAgents");
    } else {
        if(file_exists("/Library/LaunchAgents/PStateMenu.plist")) { system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist"); }
    }

	
    //Check if we need a custom version of chameleon
    if ($modeldb[$modelID]['customCham'] == "yes" || $modeldb[$modelID]['customCham'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom chameleon to $rootpath.. <br>");
        system_call("rm -f $rootpath/boot");
        system_call("cp $workpath/model-data/$modelName/$os/boot $rootpath");
    }

    //Check if we need a custom made kernel
    if ($modeldb[$modelID]['customKernel'] == "yes" || $modeldb[$modelID]['customKernel'] == "y") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom made kernel to $rootpath.. <br>");
        system_call("rm -f $rootpath/custom_kernel");
        system_call("cp $workpath/model-data/$modelName/$os/custom_kernel $rootpath");
    }
    
    //Check for ACPIPlatfrmxxx and Brightness Fix of GMA950
    AppleACPIfixCheck();
    GMA950brightnessfixCheck();
    
    /*********************** End Fixes and Patches *****************************/


	/*********************** Begin Common and Custom kexts *****************************/

    $edp->writeToLog("$workpath/build.log", "  Copying standard common kexts to $ee.. <br>");
    //Syncing kextpack to local storage
    if(!is_dir("$kpsvn/Standard"));
    			system_call("mkdir $kpsvn/Standard");
    
    kextpackLoader("Standard/common");
    
    if ($os != "sl")//skip on Snow leopard for now as we don't have any kexts needed for tha
    	system_call("cp -R $workpath/kpsvn/Standard/common/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    //Syncing kextpack to local storage
    kextpackLoader("Standard/$os");
    
    system_call("cp -R $workpath/kpsvn/Standard/$os/* $ee");
    
	// From Model data
    $edp->writeToLog("$workpath/build.log", "  Copying common kexts to $ee..<br>");
    $tf = "$workpath/model-data/$modelName/common/Extensions";
    system_call("cp -Rf $tf/* $ee");
	// From Model data
    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    $tf = "$workpath/model-data/$modelName/$os/Extensions";
    system_call("cp -Rf $tf/* $ee");


    //Copying custom kexts from include folder
   /* $edp->writeToLog("$workpath/build.log", "  Copying custom kexts from $workpath/include/Extensions to $ee/<br>");
    system_call("cp -R $workpath/include/Extensions/* $ee");*/
    

    $edp->writeToLog("$workpath/build.log", "  Copying custom kexts and Themes from $incpath to /Extra<br>");
    //Copying kexts
    system_call("cp -R $incpath/Extensions/* $ee");
    
    // Copy Custom Themes folder from $incpatch to /Extra
    if (is_dir("$incpath/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying Custom themes folder to /Extra<br>");
        system_call("rm -rf /Extra/Themes");
        system_call("mkdir /Extra/Themes");
		system_call("cp -R $incpath/Themes/. /Extra/Themes");
     }

	/*********************** End Common and Custom kexts *****************************/
	
	
    $edp->writeToLog("$workpath/build.log", "  Removing version control of kexts in $ee");
    system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`");
}

function isEmptyDir($dir) {
    if (($files = @scandir("$dir")) && (count($files) > 2)) {
        return "yes";
    } else {
        return "no";
    }
}
	
?>