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
//Returns model data from id
	function getModelDataFromID($id) {
		if ($id != "") {
			global $edp_db;
			$stmt = $edp_db->query("SELECT * FROM modelsdata where id = '$id'");
			$stmt->execute(); $result = $stmt->fetchAll(); 
			$mdata = $result[0]; //get first row
			return $mdata;
		}		
	}

//Returns Kextpack data from id
	function getKextpackDataFromID($table, $id) {
		if ($table != "" && $id != "") {
			global $edp_db;
			$stmt = $edp_db->query("SELECT * FROM $table where id = '$id'");
			$stmt->execute(); $result = $stmt->fetchAll(); $kprow = $result[0];
			return $kprow;
		}		
	}

//--- Get EDP builder Model / Vendor / series values for the user to select from
function builderGetVendorValuebyID($modelid) {
    global $edp_db;

	$stmt = $edp_db->query("SELECT vendor FROM modelsdata where id = '$modelid'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[vendor];
}
function builderGetGenValuebyID($modelid) {
    global $edp_db;

	$stmt = $edp_db->query("SELECT generation FROM modelsdata where id = '$modelid'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[generation];
}
function builderGetSeriesValuebyID($modelid) {
    global $edp_db;

	$stmt = $edp_db->query("SELECT series FROM modelsdata where id = '$modelid'");
	$stmt->execute();
	$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
    return $mdrow[series];
}
function builderGetVendorValues() {
    global $edp_db;

    $result = $edp_db->query('SELECT DISTINCT vendor FROM modelsdata ORDER BY vendor');
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['vendor'] . '">&nbsp;&nbsp;' . $row['vendor'] . '</option>';
    }

    return $return;
}
function builderGetSerieValues($vendor) {
    global $edp_db;

    $result = $edp_db->query('SELECT DISTINCT series, vendor FROM modelsdata WHERE vendor = "' . $vendor . '" ORDER BY series');
    $return = '';

    foreach($result as $row) {
        $return .= '<option value="' . $row['series'] . '">&nbsp;&nbsp;' . $row['vendor'] . ' ' . $row['series'] . ' </option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select series...</option>' . $return;
}
function builderGetModelValues($vendor, $series, $generation) {
    global $edp_db;

    $result = $edp_db->query('SELECT * FROM modelsdata WHERE vendor = "' . $vendor . '" AND series = "' . $series . '" ORDER BY type');
    $return = '';

    foreach($result as $row) {
    if($row['generation'] != "")
        	$return .= '<option value="' . $row['id'] . '">&nbsp;&nbsp;' . $row[desc] . ' (' . $row['type'] .')  ' . $row['generation'] .' </option>';
        else
        	$return .= '<option value="' . $row['id'] . '">&nbsp;&nbsp;' . $row[desc] . ' (' . $row['type']  .') </option>';
    }

    return '<option value="" >&nbsp;&nbsp;Select model...</option>' . $return;
}
//---

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
 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 */
function kextpackLoader($name) {
	global $edp_db, $workpath, $edp;
	if ($name != "") {
		$workfolder = "$workpath/kpsvn/$name";
		if (is_dir("$workfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force update $workfolder >> $workpath/kpload.log 2>&1 &");
		}
		else {
			system_call("mkdir $workfolder; cd $workfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$name . >> $workpath/kpload.log 2>&1 &");
		}
	}
} 

 
/**
 * Function to check if the model is allready checked out
 * if the model is not checked out it will check it out
 */
function loadModeldata() {
    global $workpath, $modelNamePath;
	
    $modelfolder = "$workpath/model-data/$modelNamePath";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force update $modelfolder >> $workpath/checkout.log 2>&1 &");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath . >> $workpath/checkout.log 2>&1 &");
    }
}
	
function svnModeldata($model) {
    global $workpath;
		
    $modelfolder = "$workpath/model-data/$model";
    if (is_dir("$modelfolder")) {
        system_call("svn --non-interactive --username edp --password edp --force update $modelfolder >> $workpath/checkout.log 2>&1 &");
    } else {
        system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force co http://osxlatitude-edp.googlecode.com/svn/model-data/$model . >> $workpath/checkout.log 2>&1 &");
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
 * replace system_call() .. works with LWS also
 */
function system_call($data) {
    passthru("$data");
    echo str_repeat(' ', 254);
    flush();
}

function isEmptyDir($dir) {
    if (($files = @scandir("$dir")) && (count($files) > 2)) {
        return "yes";
    } else {
        return "no";
    }
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
	
/**
 * AppleACPIPlatformxxx kext fix for Battery and Coolbook
 */
function AppleACPIfixCheck() {
    global $ee, $workpath, $slepath, $modeldb, $modeldbID;

    //Check if ACPIfix is selected
    if ($modeldb[$modeldbID]["useACPIfix"] == "yes") {
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

    if (!is_dir("$chkdir") && ($workpath == "/Extra/EDP")) {
        system_call("mkdir $chkdir");
        if (file_exists($kerncachefile)) {
            echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
            system_call("kextcache -system-prelinked-kernel");
        }
    }
}

function lastMinFixes() {
		global $workpath; global $edp; global $nvram;
		// Final Chown to SLE (this is due to some issuses with myFix in Mavericks)
		system_call("sudo chown -R root:wheel /System/Library/Extensions/");
		$stat = $nvram->clear();
		$edp->writeToLog("$workpath/build.log", "Clearing boot-args in NVRAM...$stat<br>");
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
 * Patch VGA and HDMI for Intel HD3000 GPU
 */
function patchAppleIntelSNBGraphicsFB() {

    global $ee, $slepath;
    
    system_call("cp -R $slepath/AppleIntelSNBGraphicsFB.kext $ee/");
    system_call('sudo perl -pi -e \'s|\x01\x02\x04\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x02\x05\x00\x00\x00\x04\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00\x04\x06\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00|\x01\x02\x03\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x06\x02\x00\x00\x00\x01\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x08\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00|g\' /Extra/Extensions/AppleIntelSNBGraphicsFB.kext/Contents/MacOS/AppleIntelSNBGraphicsFB');
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/AppleIntelSNBGraphicsFB.kext/Contents/KextPatched.plist");  
}
/**
 * Patch AppleIntelCPUPowerxxx for Native Speedstep and Power managment
 */
function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    
    if(is_dir("$slepath/AppleIntelCPUPowerManagement.kext")) {
    	system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    	system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
		system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x48\x89\xF2\x0F\x30|\xE2\x00\x00\x00\x48\x89\xF2\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
        system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/AppleIntelCPUPowerManagement.kext/Contents/KextPatched.plist");  
  }
}
/**
 * Patch WiFI and Bluetooth Kexts
 */
//<-----------------------------------------------------------------------------------------------------------------------------------

/*
 * Patch AirPortAtheros40.kext for the card AR5B95/AR5B195 from Lion onwards
 */
function patchWiFiAR9285AndAR9287() {
	global $ee, $slepath;
	echo "  Applying AR9285/AR9287 WiFi Fix for AR5B195/AR5B95 and AR5B197\n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/KextPatched.plist"); 
    }
    else { echo "  AirPortAtheros40.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}


/*
 * Patch AirPortBrcm4360.kext for the card BCM94352HMB from Mountain Lion 10.8.5 onwards
 */
function patchWiFiBTBCM4352() {
	global $ee, $slepath;
	echo "  Applying BCM4352 WiFi Fix for BCM94352HMB card\n";
	
	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/KextPatched.plist");
   
    // Binary patches
    system_call("sudo perl -pi -e 's|\x01\x58\x54|\x01\x55\x53|g' $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360"); // region code change to US
    system_call("sudo perl -pi -e 's|\x6B\x10\x00\x00\x75|\x6B\x10\x00\x00\x74|g' $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360"); // skipping binary checks of apple device id to work Appple card
    system_call("sudo perl -pi -e 's|\x6B\x10\x00\x00\x0F\x85|\x6B\x10\x00\x00\x0F\x84|g' $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360"); // skipping binary checks of apple device id to work Appple card
    }
    else { echo "  AirPortBrcm4360.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }    
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card Dell DW1395, DW1397 from Lion onwards
 */
function patchDW13957WiFiBCM43224() {
	global $ee, $slepath;
	echo "  Applying BCM43224 WiFi Fix for Dell DW1395, DW1397 \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AppleAirPortBrcm4311.kext for the card Dell DW1395, DW1397 in Snow Leopard
 */
function patchDW13957WiFiBCM4311() {
	global $ee, $slepath;
	echo "  Applying BCM4311 WiFi Fix for Dell DW1395, DW1397 \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm4311.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AppleAirPortBrcm43224.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM43224() {
	global $ee, $slepath;
	echo "  Applying BCM43224 WiFi Fix for BCM943224 HMS and BCM943225 HMB \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4353\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
    }
     else { echo "  AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
}

/*
 * Patch AirPortBrcm4331.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
 */
function patchWiFiBCM4331() {
	global $ee, $slepath;
	echo "  Applying BCM4331 WiFi Fix for BCM943225 HMB \n";

	$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist";   if (file_exists($file)) {
	system_call("cp -R $slepath/IO80211Family.kext $ee/");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist");
    system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist");
    }
     else { echo "  AirPortBrcm4331.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
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

/**
 * Essentials like dsdt, ssdt and plists loading for build
 */
function copyEssentials() {
    global $workpath, $incpath, $os; global $edp;
	global $modeldb, $modeldbID;
     global $modelNamePath;
	
    $edp->writeToLog("$workpath/build.log", "Cleaning up by System...<br>");
    edpCleaner();

	$edp->writeToLog("$workpath/build.log", " Model EDP Path: $workpath/model-data/$modelNamePath<br>");
	$edp->writeToLog("$workpath/build.log", "  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *<br>");

	$extrapath = "/Extra";
    $edp->writeToLog("$workpath/build.log", " Checking for DSDT, SSDT and System Plist files from EAPD......<br>");
    
    // use EDP SMBIos?
    if($modeldb[$modeldbID]["useEDPSMBIOS"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/SMBios.plist"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/SMBios.plist"; 
    	if((file_exists($file1)) || (file_exists($file2))) {
   		 $edp->writeToLog("$workpath/build.log", " SMBios.plist found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/SMBios.plist")) { system_call("rm $extrapath/SMBios.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    	}
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping SMBios.plist file from EDP on user request<br>");
    }
    
    // use EDP org.chameleon.Boot.plist?
    if($modeldb[$modeldbID]["useEDPCHAM"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/org.chameleon.Boot.plist"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/org.chameleon.Boot.plist"; 
    	if((file_exists($file1)) || (file_exists($file2))) {
   	    $edp->writeToLog("$workpath/build.log", " org.chameleon.Boot.plist found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/org.chameleon.Boot.plist")) { system_call("rm $extrapath/org.chameleon.Boot.plist"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
   	 	}
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping org.chameleon.Boot.plist file from EDP on user request<br>");
    }
    
    // use EDP DSDT?
    if($modeldb[$modeldbID]["useEDPDSDT"] == "on")
    {
    	$file1 = "$workpath/model-data/$modelNamePath/common/dsdt.aml"; 
    	$file2 = "$workpath/model-data/$modelNamePath/$os/dsdt.aml"; 
    	if((file_exists($file1)) || (file_exists($file2))) {
    	$edp->writeToLog("$workpath/build.log", " dsdt found, Copying to $extrapath<br>");
    	//Remove existing file from /Extra
    	if (file_exists("$extrapath/dsdt.aml")) { system_call("rm $extrapath/dsdt.aml"); }
    	//Copy file from common folder if exists
    	if(file_exists($file1))
    	system_call("cp -f $file1 $extrapath"); 
    	//Copy file from os folder if exists
    	if(file_exists($file2))
    	system_call("cp -f $file2 $extrapath");
    	}
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping DSDT file from EDP on user request<br>");
    }
    
	
    // If its mavericks then copy the files from ml folder temporarilyfor now
    if($os == "mav" && !is_dir("$workpath/model-data/$modelNamePath/$os") && is_dir("$workpath/model-data/$modelNamePath/ml")) {
    $edp->writeToLog("$workpath/build.log", "  mavericks directory is not found, Copying dsdt and plist files from ml folder<br>");
    if($modeldb[$modeldbID]["useEDPDSDT"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    	}
    if($modeldb[$modeldbID]["useEDPSMBIOS"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/SMBios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
    	}
    if($modeldb[$modeldbID]["useEDPCHAM"] == "on") {
    	$file = "$workpath/model-data/$modelNamePath/ml/org.chameleon.Boot.plist";  if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
    	}
    }						

	// use EDP org.chameleon.Boot.plist?
	if($modeldb[$modeldbID]["useEDPCHAM"] == "on") {
		// set UseKernelCache to Yes from org.chameleon.Boot.plist
		system("sudo /usr/libexec/PlistBuddy -c \"set UseKernelCache Yes\" $extrapath/org.chameleon.Boot.plist");
	}
	
	// use EDP SSDT?
    if($modeldb[$modeldbID]["useEDPSSDT"] == "on")
    {
      $file = "$workpath/model-data/$modelNamePath/common/SSDT.aml";   if (file_exists($file)) 
       { 
    	$edp->writeToLog("$workpath/build.log", " SSDT files found, Copying to $extrapath<br>");
    	system_call("cp -f $file $extrapath");
    	// set DropSSDT to Yes from org.chameleon.Boot.plist
		system("sudo /usr/libexec/PlistBuddy -c \"set DropSSDT Yes\" $extrapath/org.chameleon.Boot.plist"); 
   		}
   		$file = "$workpath/model-data/$modelNamePath/common/SSDT-1.aml"; if (file_exists($file)) { 
    	if (file_exists("$extrapath/SSDT-1.aml")) { system_call("rm $extrapath/SSDT-1.aml"); }
   		system_call("cp -f $file $extrapath"); 
    	}
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-2.aml"; if (file_exists($file)) { 
    	if (file_exists("$extrapath/SSDT-2.aml")) { system_call("rm $extrapath/SSDT-2.aml"); }
    	system_call("cp -f $file $extrapath"); 
    	}
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-3.aml"; if (file_exists($file)) { 
    	if (file_exists("$extrapath/SSDT-3.aml")) { system_call("rm $extrapath/SSDT-3.aml"); }
    	system_call("cp -f $file $extrapath"); 
    	}
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-4.aml"; if (file_exists($file)) {
    	if (file_exists("$extrapath/SSDT-4.aml")) { system_call("rm $extrapath/SSDT-4.aml"); } 
    	system_call("cp -f $file $extrapath"); 
    	}
    	$file = "$workpath/model-data/$modelNamePath/common/SSDT-5.aml"; if (file_exists($file)) {
    	if (file_exists("$extrapath/SSDT-5.aml")) { system_call("rm $extrapath/SSDT-5.aml"); } 
    	system_call("cp -f $file $extrapath"); 
    	}	
    }  else {
    	$edp->writeToLog("$workpath/build.log", " Skipping SSDT files from EDP on user request<br>");
    }
    		

	$edp->writeToLog("$workpath/build.log", "  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *<br>");
    //Copy essentials from /Extra/include if user has
    $edp->writeToLog("$workpath/build.log", "  Checking if we have any essential files in $incpath to use......<br>");
    if (is_file("$incpath/smbios.plist") && $modeldb[$modeldbID]["useIncSMBIOS"] == "on") 				{ 
    $edp->writeToLog("$workpath/build.log", " Custom smbios.plist found, Copying from $incpath to $extrapath<br>");
    system_call("cp -f $incpath/smbios.plist /Extra"); 
    }
    if (is_file("$incpath/org.chameleon.Boot.plist") && $modeldb[$modeldbID]["useIncCHAM"] == "on") 	{ 
    $edp->writeToLog("$workpath/build.log", " Custom org.chameleon.Boot.plist found, Copying from $incpath to $extrapath<br>");
    system_call("cp -f $incpath/org.chameleon.Boot.plist /Extra"); 
    }
    if (is_file("$incpath/dsdt.aml") && $modeldb[$modeldbID]["useIncDSDT"] == "on") 					{ 
    $edp->writeToLog("$workpath/build.log", " Custom dsdt file found, Copying from $incpath to $extrapath<br>");
    system_call("cp -f $incpath/dsdt.aml /Extra"); 
    }
    if($modeldb[$modeldbID]["useIncSSDT"] == "on")
    {
    	if (is_file("$incpath/SSDT.aml")) 					{ 
    	$edp->writeToLog("$workpath/build.log", " Custom SSDT files found, Copying from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/SSDT.aml /Extra"); 
    	}
    	if (is_file("$incpath/SSDT-1.aml")) 				{ system_call("cp -f $incpath/SSDT-1.aml /Extra"); }
    	if (is_file("$incpath/SSDT-2.aml")) 				{ system_call("cp -f $incpath/SSDT-2.aml /Extra"); }
    	if (is_file("$incpath/SSDT-3.aml")) 				{ system_call("cp -f $incpath/SSDT-3.aml /Extra"); }    
    	if (is_file("$incpath/SSDT-4.aml")) 				{ system_call("cp -f $incpath/SSDT-4.aml /Extra"); }
    	if (is_file("$incpath/SSDT-5.aml")) 				{ system_call("cp -f $incpath/SSDT-5.aml /Extra"); }  
    }
     
    
    // Copy Themes folder from EDP workpath to Extra
    if (!is_dir("/Extra/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying Standard themes folder to /Extra<br>");
    	system_call("mkdir /Extra/Themes");
		system_call("cp -R $workpath/Themes/. /Extra/Themes");
     }
}


/**
 * Kexts loading for build
 */
//Copying kexts 
function copyKexts() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modeldbID, $os, $ee, $batterydb, $landb, $fakesmcdb, $edp;
    global $cpufixdb;
    
    //Get our class(s)
    global $builder;
    global $modelNamePath;
	
	//kextpack svn path
	$kpsvn = "$workpath/kpsvn";
	
	if(!is_dir("$workpath/kpsvn"));
    			system_call("mkdir $workpath/kpsvn");

    $edp->writeToLog("$workpath/build.log", "  Cleaning up kexts in $ee..<br>");
    system_call("rm -Rf $ee/*.kext");

    /*********************** Begin Kexts related to Hardware *****************************/
    
    // Use EDP Kexts?
    if($modeldb[$modeldbID]['useEDPExtentions'] == "on")
    {
    	//copying PS2 kexts from kextpacks
    	$ps2id = $modeldb[$modeldbID]['ps2pack'];
    
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
    		
    		$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kext prefpanes if exists...<br>");
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
    		$edp->writeToLog("$workpath/build.log", "  Copying the PS2 controller kext ($kpsvn/$name) to $ee<br>");
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
    	if ($modeldb[$modeldbID]['wifipack'] != "" && $modeldb[$modeldbID]['wifipack'] != "no") {
        $wifid = $modeldb[$modeldbID]['wifipack'];
        $name = $wifidb[$wifid]['kextname'];
        $fname = $wifidb[$wifid]['foldername'];
        if ($name != "") {
        	
    		$edp->writeToLog("$workpath/build.log", "  Patching WiFi kext $name<br>");
    		
    		if($wifid == "0" || $wifid == "1")
    			patchWiFiAR9285AndAR9287();
    		else if($wifid == "2") {
    			if(getMacOSXVersion() >= "10.8.5")
    				patchWiFiBTBCM4352();
    			else
    				$edp->writeToLog("$workpath/build.log", "  OSX version is not supported for WiFi, need OSX 10.8.5 or later<br>");
    		}
    			
    		else if($wifid == "3")
    			patchDW13957WiFiBCM43224();
    		else if($wifid == "4")
    			patchDW13957WiFiBCM4311();
    		else if($wifid == "5")
    			patchWiFiBCM43224();
    		else if($wifid == "6")
    			patchWiFiBCM4331();
    		else if($wifid == "7")
    			{
    				if(!is_dir("$kpsvn/Wireless"))
    					system_call("mkdir $kpsvn/Wireless");
    					
    				kextpackLoader("Wireless/$fname"); 
    				//Copying the kextpack to /Extra/Extentions
    				$edp->writeToLog("$workpath/build.log", "  Copying the Wifi kext ($workpath/kpsvn/Wireless/$fname/$name) to $ee<br>");
    				system_call("cp -R $workpath/kpsvn/Wireless/$fname/. $ee");
    			}
    		
    		//Syncing kextpack to local storage
    		/*if($wifid < 3)
    			{
    				system_call("mkdir $kpsvn/Wireless");
    				kextpackLoader("Wireless/BluetoothFWUploader"); 
    				//Copying the kextpack to /Extra/Extentions
    				$edp->writeToLog("$workpath/build.log", "  Copying the Bluetooth kext ($workpath/kpsvn/Wireless/BluetoothFWUploader) to $ee<br>");
    				system_call("cp -R $workpath/kpsvn/Wireless/BluetoothFWUploader/. $ee");
    			}*/
    		}
		}
		//Resetting $name
		$name = "";    
    

   		 //copying fakesmc kexts from kextpacks
    	$fakesmcid = $modeldb[$modeldbID]['fakesmc'];
    	$name = $fakesmcdb[$fakesmcid]["foldername"]; 
    	if ($modeldb[$modeldbID]['fakesmc'] != "" && $modeldb[$modeldbID]['fakesmc'] != "no" && $name != "") {   
    	
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
    	$audioid = $modeldb[$modeldbID]['audiopack'];
    	$audiodir = $audiodb[$audioid]["foldername"]; $name = $audiodir;
    
    	// remove voodooHDA related files if installed before
    	if($audioid == "no" || $audioid == "builtin") {
        	 	if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
   		 }
    
    	if ($modeldb[$modeldbID]['audiopack'] != "" && $modeldb[$modeldbID]['audiopack'] != "no") {
        $edp->writeToLog("$workpath/build.log", "  Copying the Audio kexts to $ee<br>");
        
        //Clean up
        if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
        
        if ($audioid == "builtin") {
        	
        	if(is_dir("$workpath/model-data/$modelNamePath/applehda/$os")) { 
        		$edp->writeToLog("$workpath/build.log", "  Copying $os AppleHDA kext to $ee<br>");
        		system_call("cp -R $workpath/model-data/$modelNamePath/applehda/$os/. $ee/"); 
        	}
        	else if(is_dir("$workpath/model-data/$modelNamePath/applehda/common")) { 
        		$edp->writeToLog("$workpath/build.log", "  Copying common AppleHDA kext to $ee<br>");
        		system_call("cp -R $workpath/model-data/$modelNamePath/applehda/common/. $ee/");
        	}
        		 
        	else if(is_dir("$workpath/model-data/$modelNamePath/$os/applehda")) { system_call("cp -R $workpath/model-data/$modelNamePath/$os/applehda/. $ee/"); }
        	else {  
        		if (is_dir("$workpath/model-data/$modelNamePath/common/applehda")) { system_call("cp -R $workpath/model-data/$modelNamePath/common/applehda/. $ee/"); }
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
    	if ($modeldb[$modeldbID]['ethernet'] != "" && $modeldb[$modeldbID]['ethernet'] != "no") {
        $lanid = $modeldb[$modeldbID]['ethernet'];
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
    		$edp->writeToLog("$workpath/build.log", "  Copying the Ethernet kext ($kpsvn/Ethernet/$name/$lankext) to $ee<br>");
            system_call("cp -R $kpsvn/Ethernet/$name/$lankext $ee/");
       		 }	
     	  }
		}

		//copying battery kexts from kextpacks
   	 if ($modeldb[$modeldbID]['batterypack'] != "" && $modeldb[$modeldbID]['batterypack'] != "no") {
        $battid = $modeldb[$modeldbID]['batterypack'];
        $name = $batterydb[$battid]['foldername'];
        if ($name != "") {
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/Battery"));
    			system_call("mkdir $kpsvn/Battery");
    			
    		kextpackLoader("$name");   
    		
    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying the Battery kext ($kpsvn/Battery/$name) to $ee<br>");
    		system_call("cp -R $kpsvn/$name/. $ee");
    		}
		}
	
		//Resetting $name
		$name = ""; 
    } else {
    	$edp->writeToLog("$workpath/build.log", " Skipping Standard Kexts from EDP on user request<br>");
    }
    
	
	//Copy optional kexts
    $data = $modeldb[$modeldbID]['optionalpacks'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
    	$opdata = getKextpackDataFromID("optionalpacks", $id);
        $categ = $opdata[category];
        $fname = $opdata[foldername];
        
         if ($fname != "") { 
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/$categ"));
    			system_call("mkdir $kpsvn/$categ");
    		
    		if($id == "5") {
			//Choose new version 
    		if(getMacOSXVersion() >= "10.8.5")
    			kextpackLoader("$categ/GenericXHCIUSB3_New");
    		//chooose old version
    		else if(getMacOSXVersion() < "10.8.5")
    			kextpackLoader("$categ/$fname");
    		}
    		else	
    		kextpackLoader("$categ/$fname");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying optional kextpack: $kpsvn/$categ/$fname to $ee<br>");
    		system_call("cp -R $kpsvn/$categ/$fname/. $ee");
    	}
	}
	//Resetting $name
	$fname = ""; 

 /*********************** End Kexts related to Hardware *****************************/
 
/*********************** Begin Fixes and Patches *****************************/
	$edp->writeToLog("$workpath/build.log", "  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *<br>");

    $edp->writeToLog("$workpath/build.log", "  Applying fixes and patches...... <br>");
	
	//Apply CPU and power management related fixes 
    $mdata = getModelDataFromID($modelID);
    $array 	= explode(',', $mdata['cpufixes']);
    
    $i=0; // iterating through all the id's
	while ($cpufixdb[$i] != "") {
	    //Getting kextname from ID
        $cpufixdata = getKextpackDataFromID("cpufixesdata", "$i");
        $kxtname = $cpufixdata[kextname];
        
        //Checking if we need to patch AppleIntelCPUPowerManagement.kext
        if(($modeldb[$modeldbID]['applecpupwr'] == "on") && $i == "1") {
        $edp->writeToLog("$workpath/build.log", "  Patching AppleIntelCPUPowerManagement.kext<br>");
        patchAppleIntelCPUPowerManagement();
        }
        else if(($modeldb[$modeldbID]['emupstates'] == "on") && $i == "3") {
        	
        	kextpackLoader("PowerMgmt/VoodooPState"); 
    		
        	$edp->writeToLog("$workpath/build.log", "  Copying $kxtname for emulated speedsteps <br>");
        	system_call("cp -R $workpath/kpsvn/PowerMgmt/VoodooPState/VoodooPState.kext $ee");
        	system_call("cp $workpath/kpsvn/PowerMgmt/VoodooPState/PStateMenu.plist /Library/LaunchAgents");
        }
        else if ($kxtname != "" && $modeldb[$modeldbID][$cpufixdata[edpid]] == "on") { 
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/PowerMgmt"));
    			system_call("mkdir $kpsvn/PowerMgmt");
    		
    		kextpackLoader("PowerMgmt/$kxtname");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying $kxtname to $ee<br>");
    		system_call("cp -R $kpsvn/PowerMgmt/$kxtname $ee");
    		
    		 //remove PStateMenu if installed before
    		 if(file_exists("/Library/LaunchAgents/PStateMenu.plist")) { system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist"); }
    	}
    	$i++;
	}

	
 	//Apply fixes
    $data = $modeldb[$modeldbID]['fixes'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting names from ID
	    $fixdata = getKextpackDataFromID("fixesdata", "$id");
        $categ = $fixdata[category];
        $fname = $fixdata[foldername];
        
       if($id == "2") {
       $edp->writeToLog("$workpath/build.log", "  Patching AHCI.kext to waiting for root device problem in ML<br>");
       	patchAHCI();
       }
       else if($id == "8") {
        $edp->writeToLog("$workpath/build.log", "  Patching AppleIntelSNBGraphicsFB.kext for VGA and HDMI in Intel HD3000<br>");
        patchAppleIntelSNBGraphicsFB();
        }
       else if ($fname != "") { 
    		//Syncing kextpack to local storage
    		if(!is_dir("$kpsvn/$categ"));
    			system_call("mkdir $kpsvn/$categ");
    		
    		
    		kextpackLoader("$categ/$fname");

    		//Copying the kextpack to /Extra/Extentions
    		$edp->writeToLog("$workpath/build.log", "  Copying $fname to $ee<br>");
    		if($id == "5") 
    			system_call("cp -R $kpsvn/$categ/$fname $ee");
    		else 
    			system_call("cp -R $kpsvn/$categ/$fname/. $ee"); 
    	}
	}
    	
     //Resetting $name
	$fname = "";
	
    //Check if we need a custom version of chameleon
    if ($modeldb[$modeldbID]['customCham'] == "on") {
        $edp->writeToLog("$workpath/build.log", "  Copying custom chameleon to $rootpath.. <br>");
        system_call("rm -f $rootpath/boot");
        system_call("cp $workpath/model-data/$modelNamePath/common/boot $rootpath");
        system_call("cp $workpath/model-data/$modelNamePath/$os/boot $rootpath");
    }

    //Check if we need a custom made kernel
    if(file_exists("$workpath/model-data/$modelNamePath/$os/custom_kernel")) {
        $edp->writeToLog("$workpath/build.log", "  Copying custom made kernel to $rootpath.. <br>");
        system_call("rm -f $rootpath/custom_kernel");
        system_call("cp $workpath/model-data/$modelNamePath/$os/custom_kernel $rootpath");
    }
    
    
    /*********************** End Fixes and Patches *****************************/


	/*********************** Begin Standard and Custom kexts copy *****************************/
	$edp->writeToLog("$workpath/build.log", "  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *<br>");

	$edp->writeToLog("$workpath/build.log", "  Checking for Standard and Custom kexts, will be used if exists...... <br>");
    $edp->writeToLog("$workpath/build.log", "  Copying Standard common files to $ee <br>");
    //Syncing kextpack to local storage
    if(!is_dir("$kpsvn/Standard"))
    		system_call("mkdir $kpsvn/Standard");
    
    kextpackLoader("Standard/common");
    
    if ($os != "sl")// skip on Snow leopard as we don't have any common kexts needed in EDP
    	system_call("cp -R $workpath/kpsvn/Standard/common/* $ee");

    $edp->writeToLog("$workpath/build.log", "  Copying Standard $os files to $ee <br>");
    //Syncing kextpack to local storage
    kextpackLoader("Standard/$os");
    
    system_call("cp -R $workpath/kpsvn/Standard/$os/* $ee");
    
	// From Model data (Extensions folder)
    if(is_dir("$workpath/model-data/$modelNamePath/Extensions"))
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying common kexts from Extensions folder to $ee<br>");
    	$tf = "$workpath/model-data/$modelNamePath/Extensions/common";
    	system_call("cp -Rf $tf/* $ee");
    	$edp->writeToLog("$workpath/build.log", "  Copying $os kexts from Extensions folder to $ee <br>");
    	$tf = "$workpath/model-data/$modelNamePath/Extensions/$os";
    	system_call("cp -Rf $tf/* $ee");
    }
	
    // From Model data (Common folder used before, have to remove this when all the models updated to new Extensions folder)
    if(is_dir("$workpath/model-data/$modelNamePath/common/Extensions"))
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying common kexts from common folder to $ee<br>");
    	$tf = "$workpath/model-data/$modelNamePath/common/Extensions";
    	system_call("cp -Rf $tf/* $ee");
    	$edp->writeToLog("$workpath/build.log", "  Copying $os kexts from common folder to $ee<br>");
    	$tf = "$workpath/model-data/$modelNamePath/$os/Extensions";
    	system_call("cp -Rf $tf/* $ee");
    }

    // Copying Custom kexts
    if($modeldb[$modeldbID]["useIncExtentions"] == "on")
    {
    	$edp->writeToLog("$workpath/build.log", "  Copying custom kexts from $incpath to /Extra<br>");
    	system_call("cp -R $incpath/Extensions/* $ee");
    	//If AppleHDA is found in Extra/include then remove VoodooHDA from ee
    	if(file_exists("$incpath/Extensions/AppleHDA.kext")) {
    			if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
    			system_call("rm -rf $ee/VoodooHDA.kext");
    			system_call("rm -rf $ee/AppleHDADisabler.kext");
    			$edp->writeToLog("$workpath/build.log", "  found AppleHDA from $incpath, VoodooHDA removed<br>");
   		 }
    } 
    
    // Copy Custom Themes folder from $incpatch to /Extra
    if (is_dir("$incpath/Themes")) {
        $edp->writeToLog("$workpath/build.log", "  Copying Custom themes folder to /Extra...<br>");
        system_call("rm -rf /Extra/Themes");
        system_call("mkdir /Extra/Themes");
		system_call("cp -R $incpath/Themes/. /Extra/Themes");
     }

	/*********************** End Common and Custom kexts *****************************/
	
	
    $edp->writeToLog("$workpath/build.log", "  Removing version control of kexts in $ee");
    system_call("rm -Rf `find -f path \"$ee\" -type d -name .svn`");
}

	
?>