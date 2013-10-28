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
    //system_call("cp -f /Extra/EDP/storage/boot /");
     system_call("cp -f $workpath/storage/boot /");
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
	global $workpath;
    system_call("cp -R /System/Library/Extensions/IOAHCIFamily.kext /Extra/Extensions");
    //system_call("perl /Extra/EDP/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
    system_call("perl $workpath/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
}


/**
 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 */
function kextpackLoader($name) {
	global $edp_db, $workpath, $edp;
	if ($name != "") {
		$workfolder = "$workpath/storage/kpsvn/$name";
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
        system_call("cp -R $workpath/storage/fixes/gma950-brightness-fix/AppleIntelIntegratedFramebuffer.kext $ee");
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
	


function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    
    $patchedInfoFile = "/System/Library/Extensions/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist";
    if (!file_exists($patchedInfoFile)) {
    //system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /System/Library/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist");  
    }
}

// WiFI and Bluetooth Kext Patching
//<-----------------------------------------------------------------------------------------------------------------------------------

/*
 * Patch AirPortAtheros40.kext for the card AR5B95/AR5B195 from Lion onwards
 */
function patchWiFiAR9285AndAR9287() {
	echo "  Applying AR9285/AR9287 WiFi Fix for AR5B195/AR5B95 and AR5B197\n";

	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist"); 
    }
    else { echo "  AirPortAtheros40.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}


/*
 * Patch AirPortBrcm4360.kext and BroadcomBluetoothHostControllerUSBTransport.kext for the card BCM94352HMB from Mountain Lion 10.8.5 onwards
 */
function patchWiFiBTBCM4352() {
	echo "  Applying BCM4352 WiFi Fix for BCM94352HMB card\n";
	
	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist");
    }
    else { echo "  AirPortBrcm4360.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
    }
   
    echo "  Applying BCM20702A1 Bluetooth Fix for BCM94352HMB card\n";
    $patchedInfoFile = "/System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
    $file = "/System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404 dict\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:CFBundleIdentifier string \"com.apple.iokit.BroadcomBluetoothHCIControllerUSBTransport\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:IOClass string \"BroadcomBluetoothHCIControllerUSBTransport\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:IOProviderClass string \"IOUSBDevice\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:LMPLoggingEnabled bool \"NO\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:idProduct integer \"13316\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom2046FamilyUSBBluetoothHCIController_3404:idVendor integer \"5075\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/BroadcomBluetoothHostControllerUSBTransport.kext/Contents/KextPatched.plist");
    }
    else { echo "  BroadcomBluetoothHostControllerUSBTransport.kext not found for patching in System/Library/Extensions/IOBluetoothFamily.kext/Contents/PlugIns/\n"; }
  }
    
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card Dell DW1395, DW1397 from Lion onwards
 */
function patchDW13957WiFiBCM43224() {
	echo "  Applying BCM43224 WiFi Fix for Dell DW1395, DW1397 \n";
	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}

/*
 * Patch AppleAirPortBrcm4311.kext for the card Dell DW1395, DW1397 in Snow Leopard
 */
function patchDW13957WiFiBCM4311() {
	echo "  Applying BCM4311 WiFi Fix for Dell DW1395, DW1397 \n";
	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm4311.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
  }
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM43224() {
	echo "  Applying BCM43224 WiFi Fix for BCM943224 HMS and BCM943225 HMB \n";
	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4353\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
   }
}

/*
 * Patch AirPortBrcm4331.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM4331() {
	echo "  Applying BCM4331 WiFi Fix for BCM943225 HMB \n";
	$patchedInfoFile = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist";
	if (!file_exists($patchedInfoFile)) {
	$file = "/System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist";   if (file_exists($file)) {
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"EMlyDinEsH\ And\ Mario\(OSXLatitude\)\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist");
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
	
function AppleACPIfixCheck() {
    global $ee, $workpath, $slepath, $modeldb, $modelID;

    //Check if ACPIfix is selected
    if ($modeldb[$modelID]["useACPIfix"] == "yes") {
        echo "  Applying ACPI fix (Coolbook fix)\n";
        system_call("cp -R $workpath/storage/fixes/coolbook-fix/AppleACPIPlatform.kext $ee");

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
    global $workpath, $modeldb, $modelID, $os; global $edp;

    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "Cleaning up by system...<br>");
    edpCleaner();

	$extrapath = "/Extra";
	
    $file = "$extrapath/smbios.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$extrapath/org.chameleon.Boot.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$extrapath/dsdt.aml";
    if (file_exists($file)) { system_call("rm $file"); }
        

    //Remove old SSDT table files
    $file = "$extrapath/SSDT.aml";   if (file_exists($file)) { system_call("rm $file"); }
    $file = "$extrapath/SSDT-1.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$extrapath/SSDT-2.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$extrapath/SSDT-3.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$extrapath/SSDT-4.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$extrapath/SSDT-5.aml"; if (file_exists($file)) { system_call("rm $file"); }

    $edp->writeToLog("$workpath/build.log", "  Copying COMMON system plists and dsdt.aml from $workpath/model-data/$modelName/common to $extrapath<br>");
    $file = "$workpath/model-data/$modelName/common/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }

    $edp->writeToLog("$workpath/build.log", "  Copying OS Specific system plists and dsdt.aml from $workpath/model-data/$modelName/$os to $extrapath<br>");		
    $file = "$workpath/model-data/$modelName/$os/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/$os/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/$os/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    
    // If its mavericks then copy the file from ml folder for now
    if($os == "mav" && !is_dir("$workpath/model-data/$modelName/$os")) {
    $edp->writeToLog("$workpath/build.log", "  mavericks directory is not found, Copying dsdt and plist files from ml folder..<br>");
    $file = "$workpath/model-data/$modelName/ml/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    $file = "$workpath/model-data/$modelName/ml/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
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
    $file = "$workpath/model-data/$modelName/common/SSDT-1.aml"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-2.aml"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-3.aml"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-4.aml"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-5.aml"; if (file_exists($file)) { system_call("cp -f $file $extrapath"); }			

	// Copy Themes folder from EDP workpath to Extra
    if (!is_dir("/Extra/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying themes folder to /Extra<br>");
    	system_call("mkdir /Extra/Themes");
		//system_call("cp -R /Extra/EDP/Themes/. /Extra/Themes");
		system_call("cp -R $workpath/Themes/. /Extra/Themes");
     }

    //Copy essentials from /Extra/include
    $edp->writeToLog("$workpath/build.log", "  Checking if we have any essential files in /Extra/include that will be used instead...<br>");
    if (is_file("/Extra/include/smbios.plist")) 				{ system_call("cp -f /Extra/include/smbios.plist /Extra"); }
    if (is_file("/Extra/include/org.chameleon.Boot.plist")) 	{ system_call("cp -f /Extra/include/org.chameleon.Boot.plist /Extra"); }
    if (is_file("/Extra/include/dsdt.aml")) 					{ system_call("cp -f /Extra/include/dsdt.aml /Extra"); }
    if (is_file("/Extra/include/SSDT.aml")) 					{ system_call("cp -f /Extra/include/SSDT.aml /Extra"); }
    if (is_file("/Extra/include/SSDT-1.aml")) 				{ system_call("cp -f /Extra/include/SSDT-1.aml /Extra"); }
    if (is_file("/Extra/include/SSDT-2.aml")) 				{ system_call("cp -f /Extra/include/SSDT-2.aml /Extra"); }
    if (is_file("/Extra/include/SSDT-3.aml")) 				{ system_call("cp -f /Extra/include/SSDT-3.aml /Extra"); }    
    if (is_file("/Extra/include/SSDT-4.aml")) 				{ system_call("cp -f /Extra/include/SSDT-4.aml /Extra"); }
    if (is_file("/Extra/include/SSDT-5.aml")) 				{ system_call("cp -f /Extra/include/SSDT-5.aml /Extra"); }    
}



//Copying kexts 
function copyKexts() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepathfull, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modelID, $os, $ee, $batterydb, $landb, $fakesmcdb, $edp;
    
    //Get our class(s)
    global $builder;

	$slepathfull = "/System/Library/Extensions";
	
    $modelName = $modeldb[$modelID]["name"];

    $edp->writeToLog("$workpath/build.log", "  Start by cleaning up in $ee..<br>");
    system_call("rm -Rf $ee/*.kext");

    /*********************** Begin Kexts related to Hardware *****************************/
    
    //copying PS2 kexts from kextpacks
    $ps2id = $modeldb[$modelID]['ps2pack'];
    if ($ps2id != "" && $ps2id != "no") {
    	$name = $ps2db[$ps2id]["foldername"];
        if ($name != "") {  
    		//Syncing kextpack to local storage
    		kextpackLoader("$name");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kexts ($workpath/storage/kpsvn/$name) to $ee<br>");
    		system_call("cp -R $workpath/storage/kpsvn/$name/. $ee");
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
    			kextpackLoader("BluetoothFWUploader"); 
    			//Copying the kextpack to /Extra/Extentions
    			$edp->writeToLog("$workpath/build.log", "  Copying the Bluetooth kext ($workpath/storage/kpsvn/BluetoothFWUploader) to $ee<br>");
    			system_call("cp -R $workpath/storage/kpsvn/BluetoothFWUploader/. $ee");
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
    	kextpackLoader("$name");   
    		
    	//Copying the kextpack to /Extra/Extentions
    	$edp->writeToLog("$workpath/build.log", "  Copying the fakesmc kext ($workpath/storage/kpsvn/$name) to $ee<br>");
    	system_call("cp -R $workpath/storage/kpsvn/$name/. $ee");
    }
	//Resetting $name
	$name = ""; 
	
    	
    //copying audio kexts
    $audioid = $modeldb[$modelID]['audiopack'];
    $audiodir = $audiodb[$audioid]["foldername"]; $name = $audiodir;
    if ($modeldb[$modelID]['audiopack'] != "" && $modeldb[$modelID]['audiopack'] != "no") {
        $edp->writeToLog("$workpath/build.log", "  Copying the Audio kexts to $ee<br>");
        //Clean up
        if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
        if ($audioid == "buildin") {
        	if (is_dir("$workpath/model-data/$modelName/$os/applehda")) { system_call("cp -R $workpath/model-data/$modelName/$os/applehda/. $ee/"); }
        	else { 
        		if (is_dir("$workpath/model-data/$modelName/common/applehda")) { system_call("cp -R $workpath/model-data/$modelName/common/applehda/. $ee/"); }
        	}
	        
        } else { 
        	//Syncing kextpack to local storage
        	kextpackLoader("$name");
        	$edp->writeToLog("$workpath/build.log", "  Copying the $name kextpack ($workpath/storage/kpsvn/$name) to $ee<br>");
        	system_call("cp -R $workpath/storage/kpsvn/$name/. $ee");
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
    		kextpackLoader("$name");   
    		
    		if ($lankext != "") {
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the Ethernet kexts ($workpath/storage/kpsvn/$name) to $ee<br>");
            system_call("cp -R $workpath/storage/kpsvn/$name/$lankext $ee/");
       	 }	
       }
	}

	//copying battery kexts from kextpacks
    if ($modeldb[$modelID]['batteryKext'] != "" && $modeldb[$modelID]['batteryKext'] != "no") {
        $battid = $modeldb[$modelID]['batteryKext'];
        $name = $batterydb[$battid]['foldername'];
        if ($name != "") {
    		//Syncing kextpack to local storage
    		kextpackLoader("$name");   
    		
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the Battery kexts ($workpath/storage/kpsvn/$name) to $ee<br>");
    		system_call("cp -R $workpath/storage/kpsvn/$name/. $ee");
    	}
	}
	
	//Resetting $name
	$name = ""; 
	
	
	 //Copy optional kexts
    $data = $modeldb[$modelID]['optionalpacks'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting foldername from ID
        $name = $builder->getKextpackNameFromID("optionalpacks", "$id");
        if ($name != "") { 
    		//Syncing kextpack to local storage
    		kextpackLoader("$name");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying optional kextpack: $workpath/storage/kpsvn/$name to $ee<br>");
    		system_call("cp -R $workpath/storage/kpsvn/$name/. $ee");
    	}
	}


 /*********************** End Kexts related to Hardware *****************************/
 
/*********************** Begin Fixes and Patches *****************************/
  
	//Checking if we need to patch AppleIntelCPUPowerManagement.kext
    $pathCPU = $modeldb[$modelID]["patchCPU"];
    if ($pathCPU == "yes") {
        patchAppleIntelCPUPowerManagement();
    }

    //Checking if we need nullcpu
    if ($modeldb[$modelID]['nullcpu'] == "yes" || $modeldb[$modelID]['nullcpu'] == "y") {
    	//Syncing kextpack to local storage
    		kextpackLoader("PowerMgmt"); 
        $edp->writeToLog("$workpath/build.log", "  Copying NullCPUPowerManagement.kext for disabling Apples native power management.. <br>");
        //system_call("cp -R $workpath/storage/kexts/NullCPUPowerManagement.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/PowerMgmt/NullCPUPowerManagement.kext $ee");
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
    		kextpackLoader("PowerMgmt"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying SleepEnabler.kext for enabling sleep...<br>");
        //system_call("cp -R $workpath/storage/kexts/$os/SleepEnabler.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/PowerMgmt/SleepEnabler.kext $ee");
    }

    if ($modeldb[$modelID]['loadIOATAFamily'] == "yes") {
    	//Syncing kextpack to local storage
    		kextpackLoader("Others"); 
    		
        $edp->writeToLog("$workpath/build.log", "  Copying IOATAFamily.kext to $ee.. <br>");
        //system_call("cp -R $workpath/storage/kexts/IOATAFamily.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/Others/IOATAFamily.kext $ee");
    }

    if ($modeldb[$modelID]['loadNatit'] == "yes") {
    	//Syncing kextpack to local storage
    		kextpackLoader("Others"); 
        $edp->writeToLog("$workpath/build.log", "  Copying Natit.kext to $ee.. <br>");
        //system_call("cp -R $workpath/storage/kexts/natit.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/Others/Natit.kext $ee");
    }

    if ($modeldb[$modelID]['tscsync'] == "yes" || $modeldb[$modelID]['tscsync'] == "y") {
    	//Syncing kextpack to local storage
    		kextpackLoader("PowerMgmt"); 
        $edp->writeToLog("$workpath/build.log", "  Check if we need VoodooTSCSync.kext for syncing CPU cores...<br>");
        //system_call("cp -R $workpath/storage/kexts/VoodooTSCSync.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/PowerMgmt/VoodooTSCSync.kext $ee");
    }

    if ($modeldb[$modelID]['emulatedST'] == "yes" || $modeldb[$modelID]['emulatedST'] == "y") {
    	//Syncing kextpack to local storage
    		kextpackLoader("PowerMgmt"); 
        $edp->writeToLog("$workpath/build.log", "  Check if we are using emulated speedstep via voodoopstate and voodoopstatemenu <br>");
        //system_call("cp -R $workpath/storage/kexts/VoodooPState.kext $ee");
        system_call("cp -R $workpath/storage/kpsvn/PowerMgmt/VoodooPState.kext $ee");
        system_call("cp $workpath/storage/LaunchAgents/PStateMenu.plist /Library/LaunchAgents");
    } else {
        system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist");
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
    
    $edp->writeToLog("$workpath/build.log", "  Applying fixes... <br>");
    AppleACPIfixCheck();
    GMA950brightnessfixCheck();
    
    /*********************** End Fixes and Patches *****************************/


	/*********************** Begin Common and Custom kexts *****************************/

    $edp->writeToLog("$workpath/build.log", "  Copying standard common kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/common/Extensions/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/$os/Extensions/* $ee");
    
    $edp->writeToLog("$workpath/build.log", "  Copying standard kexts to $ee.. <br>");
    if ($os != "sl") {//skip on Snow leopard
            system_call("cp -R $workpath/storage/standard/Extensions/* $ee");
        }

    $edp->writeToLog("$workpath/build.log", "  Copying common kexts to $ee..<br>");
    $tf = "$workpath/model-data/$modelName/common/Extensions";
    system_call("cp -Rf $tf/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    $tf = "$workpath/model-data/$modelName/$os/Extensions";
    system_call("cp -Rf $tf/* $ee");


    //Copying custom kexts from inside include folder
    $edp->writeToLog("$workpath/build.log", "  Copying custom kexts from $workpath/include/Extensions to $ee/<br>");
    system_call("cp -R $workpath/include/Extensions/* $ee");
    

    $edp->writeToLog("$workpath/build.log", "  Applying custom plists, kexts etc.");
    //Copying kexts
    system_call("cp -R $incpath/Extensions/* $ee");
    //Copying any .AML files to /Extra
    system_call("cp -R $incpath/*.aml $workpath");
    //Copying any plists files to /Extra
    system_call("cp -R $incpath/*.plist $workpath");

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