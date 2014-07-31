<?php
   
// Include these class for some functions
  include_once "classes/chamModules.php";
  include_once "classes/svn.php";
  include_once "classes/database.php"; 
    
//------------------> EDPweb functions -----------------------------------------------------------------------------------------------

function checkbox($title, $formname, $status) {
	if ($status == "yes") { $c = "checked"; }
	echo "<li class='checkbox'><span class='name'>$title</span><input name='$formname' type='checkbox' $c/> </li>\n";
}

//Writes out the html for the pageitemtop
function echoPageItemTOP($icon, $text) {
	echo "<div class='pageitem_top'><img src='$icon'><span><b>$text</span></div></b>\n";
}


//<------------------> EDP Functions ----------------------------------------------------------------------------------------------------
    
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
		if ($v == "10.9.4") { $r="mav"; $os_string = "OSX Maverick $v"; }
		if ($v == "10.9.5") { $r="mav"; $os_string = "OSX Maverick $v"; }
		if ($v == "10.9.6") { $r="mav"; $os_string = "OSX Maverick $v"; }
		if ($v == "10.10") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
		if ($v == "10.10.0") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
		if ($v == "10.10.1") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
		if ($v == "10.10.2") { $r="yos"; $os_string = "OSX Yosemite $v"; }    
		if ($v == "10.10.3") { $r="yos"; $os_string = "OSX Yosemite $v"; }
		if ($v == "10.10.4") { $r="yos"; $os_string = "OSX Yosemite $v"; }
		if ($v == "10.10.5") { $r="yos"; $os_string = "OSX Yosemite $v"; } 
		if ($v == "10.10.6") { $r="yos"; $os_string = "OSX Yosemite $v"; }                            			
		return $r;
	}
	
	function getMacOSXVersion() {
		$path = "/System/Library/CoreServices/SystemVersion";
		$ver = exec("defaults read $path ProductVersion");
		return $ver;
	}

	/*
	 * Writes a $data to $logfile
	 */
	function writeToLog($logfile, $data) {
		file_put_contents($logfile, $data, FILE_APPEND | LOCK_EX);
	}

	/*
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

   //------> Function to get version from kext
    function getKextVersion($kext) {
    	global $workpath;
    
    	if (!is_dir($kext)) { return "0.00"; }		// If $kext dosent exist we will just return 0.00
    
    	include_once "$workpath/bin/html/libs/PlistParser.inc";
    	$parser = new plistParser();
    	$plist = $parser->parseFile("$kext/Contents/Info.plist");
    	reset($plist);
    
    	while (list($key, $value) = each($plist)) {
        	if ($key == "CFBundleShortVersionString") {
            	return "$value";
            }
        }
    }

    //-----> Copys $kext to /System/Library/Extensions/
    function copyKextToSLE($kext, $frompath) {
    	global $slepath, $workpath;

    	//Create backup folder
    	date_default_timezone_set('UTC');
    	$date = date("d-m-Y");
    	$backupfolder = "/backup/$date";
    	system_call("mkdir /backup");
    	system_call("mkdir $backupfolder");
    	system_call("rm -Rf $backupfolder/*");
    
    	//Do backup
    	echo "Copying old $slepath/$kext to $backupfolder \n";
    	system_call("cp -R $slepath/$kext $backupfolder");

    	//Remove the present kext
    	system_call("rm -R $slepath/$kext");

    	echo "Copying $workpath/$frompath/$kext to $slepath/ \n";
    	system_call("cp -R $workpath/$frompath/$kext $slepath/");

    	system_call("chown -R root:wheel $slepath/$kext");
    	system_call("chmod -R 755 \"$slepath/$kext\"");
    }
    
	//
	// Get Value from Key in SMbios.plist
	//
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
	
//<------------------> Patches  ----------------------------------------------------------------------------------------------------	
	/*
	 * Patch AHCI
	 * @see http://www.insanelymac.com/forum/topic/280062-waiting-for-root-device-when-kernel-cache-used-only-with-some-disks-fix/page__st__60#entry1851722
	 */
	function patchAHCI() {
		global $workpath,$slepath, $ee;
		system_call("cp -R $slepath/IOAHCIFamily.kext $ee");
		system_call("perl $workpath/bin/fixes/patch-ahci-mlion.pl >> $workpath/logs/build/build.log");
	}

	/*
	 * Patch VGA and HDMI for Intel HD3000 GPU
	 */
	function patchAppleIntelSNBGraphicsFB($log, $pathToPatch, $genCache) {

		global $ee, $slepath, $workpath;
	
		writeToLog("$workpath/logs/build/build.log", " Patching AppleIntelSNBGraphicsFB.kext for VGA and HDMI in Intel HD3000...<br>");

		if(!is_dir("/System/Library/Extensions/AppleIntelSNBGraphicsFB.kext")) {
				writeToLog("$log", "  AppleIntelSNBGraphicsFB.kext not found for patching<br>");
				system_call("cd $workpath/logs/fixes; touch patchFail.txt;");
				return;
	  	}
	  	
		switch ($pathToPatch)
		{
			case "SLE":			
			system_call('sudo perl -pi -e \'s|\x01\x02\x04\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x02\x05\x00\x00\x00\x04\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00\x04\x06\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00|\x01\x02\x03\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x06\x02\x00\x00\x00\x01\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x08\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00|g\' /System/Library/Extensions/AppleIntelSNBGraphicsFB.kext/Contents/MacOS/AppleIntelSNBGraphicsFB');
			
			if ($genCache == "yes") {
				system_call("sudo touch /System/Library/Extensions/ >> $log &");
			}
			break;
			
			case "EE":		
			system_call("cp -R $slepath/AppleIntelSNBGraphicsFB.kext $ee/");
			system_call('sudo perl -pi -e \'s|\x01\x02\x04\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x02\x05\x00\x00\x00\x04\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00\x04\x06\x00\x00\x00\x04\x00\x00\x09\x00\x00\x00|\x01\x02\x03\x00\x10\x07\x00\x00\x10\x07\x00\x00\x05\x03\x00\x00\x02\x00\x00\x00\x30\x00\x00\x00\x06\x02\x00\x00\x00\x01\x00\x00\x07\x00\x00\x00\x03\x04\x00\x00\x00\x08\x00\x00\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00|g\' /Extra/Extensions/AppleIntelSNBGraphicsFB.kext/Contents/MacOS/AppleIntelSNBGraphicsFB');
			
			// touch for kernel cache
			if ($genCache == "yes") {
				myHackCheck();
				system_call("sudo myfix -q -t / >> $log &");
			}
			break;
		}
		
		writeToLog("$log", "  AppleIntelSNBGraphicsFB.kext patched successfullly for Intel HD3000 VGA and HDMI<br>");
		system_call("touch $workpath/logs/fixes/patchSuccess.txt;");
	}

	/*
	 * Patch AppleIntelCPUPowerxxx for Native Speedstep and Power managment
	 */
	function patchAppleIntelCPUPowerManagement($log, $pathToPatch, $genCache) {
		global $workpath;
			
        writeToLog("$workpath/logs/build/build.log", " Patching AppleIntelCPUPowerManagement.kext...<br>");
		
		if(!is_dir("/System/Library/Extensions/AppleIntelCPUPowerManagement.kext")) {
				writeToLog("$log", "  AppleIntelCPUPowerManagement.kext not found for patching<br>");
				system_call("cd $workpath/logs/fixes; touch patchFail.txt;");
				return;
	  	}
	  	
		/*
		 * Note: Using the variables for the path in hex patch doesn't work, so we have to provide the full path
		 */
		switch ($pathToPatch)
		{
			case "SLE":			
			system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /System/Library/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
			system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x48\x89\xF2\x0F\x30|\xE2\x00\x00\x00\x48\x89\xF2\x90\x90|g\' /System/Library/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
			// touch for kernel cache
			if ($genCache == "yes") {
				system_call("sudo touch /System/Library/Extensions/ >> $log &");
			}
			break;
			
			case "EE":		
			system_call("cp -R /System/Library/Extensions/AppleIntelCPUPowerManagement.kext /Extra/Extensions/");
			system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
			system_call('sudo perl -pi -e \'s|\xE2\x00\x00\x00\x48\x89\xF2\x0F\x30|\xE2\x00\x00\x00\x48\x89\xF2\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
			// touch for kernel cache
			if ($genCache == "yes") {
				myHackCheck();
				system_call("sudo myfix -q -t / >> $log &");
			}
			break;
		}
		
		writeToLog("$log", "  AppleIntelCPUPowerManagement.kext patched successfullly<br>");
		system_call("touch $workpath/logs/fixes/patchSuccess.txt;");
	}


	/*
	 * WiFI and Bluetooth kext Patches
	 */
	//<-----------------------------------------------------------------------------------------------------------------------------------

	/*
	 * Patch AirPortAtheros40.kext for the card AR5B95/AR5B195 from Lion onwards
	 */
	function patchWiFiAR9285AndAR9287($log, $pathToPatch, $genCache) {
		global $ee, $slepath, $workpath;
		
		writeToLog("$log", " Applying AR9285/AR9287 WiFi kext patch for AR5B195/AR5B95 and AR5B197...<br>");

		if (!file_exists("$slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist")) {
			writeToLog("$log", "  IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext kext not found<br>");
			system_call("cd $workpath/logs/fixes; touch patchFail.txt;");
			return;
		}
		
		switch ($pathToPatch)
		{
			case "SLE":			
			// Kext patch
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" $slepath/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
			
			// touch for kernel cache
			if ($genCache == "yes") {
				system_call("sudo touch /System/Library/Extensions/ >> $log &");
			}
			break;
			
			case "EE":		
			system_call("cp -R $slepath/IO80211Family.kext $ee/");
			
			// Kext patch
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2b\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Atheros\ Wireless\ LAN\ PCI:IONameMatch:0 string \"pci168c,2e\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortAtheros40.kext/Contents/Info.plist");
			
			// touch for kernel cache
			if ($genCache == "yes") {
				myHackCheck();
				system_call("sudo myfix -q -t / >> $log &");
			}
			break;
		} 
		
		writeToLog("$log", "  WiFi kext patched successfullly for AR9285/AR9287 card <br>");
		system_call("touch $workpath/logs/fixes/patchSuccess.txt;");
	}

	/*
	 * Patch AirPortBrcm4360.kext for the card BCM94352HMB from Mountain Lion 10.8.5 onwards
	 */
	function patchWiFiBTBCM4352($log, $pathToPatch, $genCache) {
		global $ee, $slepath, $workpath;
		
		writeToLog("$log", " Applying WiFi patches for BCM4352 card...<br>");

		if (!file_exists("$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist")) {
			writeToLog("$log", "  IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext kext not found<br>");
			system_call("cd $workpath/logs/fixes; touch patchFail.txt;");
			return;
		}
		switch ($pathToPatch)
		{
			case "SLE":			
			// Binary patches
			system_call('sudo perl -pi -e \'s|\x01\x58\x54|\x01\x55\x53|g\' /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // region code change to US
			system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x75|\x6B\x10\x00\x00\x74|g\' /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
			system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x0F\x85|\x6B\x10\x00\x00\x0F\x84|g\' /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
			
			// Kext patch
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" /System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");   
		
			// touch for kernel cache
			if ($genCache == "yes") {
				system_call("sudo touch /System/Library/Extensions/ >> $log &");
			}
			break;
			
			case "EE":		
			system_call("cp -R $slepath/IO80211Family.kext $ee/");
			
			// Binary patches
			system_call('sudo perl -pi -e \'s|\x01\x58\x54|\x01\x55\x53|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // region code change to US
			system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x75|\x6B\x10\x00\x00\x74|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
			system_call('sudo perl -pi -e \'s|\x6B\x10\x00\x00\x0F\x85|\x6B\x10\x00\x00\x0F\x84|g\' /Extra/Extensions/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/MacOS/AirPortBrcm4360'); // skipping binary checks of apple device id to work Appple card
			
			// Kext patch
			system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,43b1\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4360.kext/Contents/Info.plist");   
			
			// touch for kernel cache
			if ($genCache == "yes") {
				myHackCheck();
				system_call("sudo myfix -q -t / >> $log &");
			}
			break;
		} 
		
		writeToLog("$log", "  WiFi kext/binary patched successfullly for BCM4352 card<br>");
		system_call("touch $workpath/logs/fixes/patchSuccess.txt;");
	}

	/*
	 * Patch AppleAirPortBrcm43224.kext for the card Dell DW1395, DW1397 from Lion onwards
	 */
	function patchDW13957WiFiBCM43224() {
		global $ee, $slepath;
		echo " Applying BCM43224 WiFi Fix for Dell DW1395, DW1397 \n";

		$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
		system_call("cp -R $slepath/IO80211Family.kext $ee/");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
		}
		 else { echo " AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
	}

	/*
	 * Patch AppleAirPortBrcm4311.kext for the card Dell DW1395, DW1397 in Snow Leopard
	 */
	function patchDW13957WiFiBCM4311() {
		global $ee, $slepath;
		echo " Applying BCM4311 WiFi Fix for Dell DW1395, DW1397 \n";

		$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist";   if (file_exists($file)) {
		system_call("cp -R $slepath/IO80211Family.kext $ee/");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4315\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/Info.plist");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm4311.kext/Contents/KextPatched.plist");
		}
		 else { echo " AppleAirPortBrcm4311.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
	}

	/*
	 * Patch AppleAirPortBrcm43224.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
	 */
	function patchWiFiBCM43224() {
		global $ee, $slepath;
		echo " Applying BCM43224 WiFi Fix for BCM943224 HMS and BCM943225 HMB \n";

		$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist";   if (file_exists($file)) {
		system_call("cp -R $slepath/IO80211Family.kext $ee/");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4353\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/Info.plist");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AppleAirPortBrcm43224.kext/Contents/KextPatched.plist");
		}
		 else { echo " AppleAirPortBrcm43224.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
	}

	/*
	 * Patch AirPortBrcm4331.kext for the card BCM943224 HMS and BCM943225 HMB from Lion onwards
	 */
	function patchWiFiBCM4331() {
		global $ee, $slepath;
		echo " Applying BCM4331 WiFi Fix for BCM943225 HMB \n";

		$file = "$slepath/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist";   if (file_exists($file)) {
		system_call("cp -R $slepath/IO80211Family.kext $ee/");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add IOKitPersonalities:Broadcom\ 802.11\ PCI:IONameMatch:0 string \"pci14e4,4357\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/Info.plist");
		system_call("sudo /usr/libexec/PlistBuddy -c \"add PatchedBy string \"OSXLatitude\"\" $ee/IO80211Family.kext/Contents/PlugIns/AirPortBrcm4331.kext/Contents/KextPatched.plist");
		}
		 else { echo " AirPortBrcm4331.kext not found for patching in System/Library/Extensions/IO80211Family.kext/Contents/PlugIns/\n"; }
	}

//<----------------------------> EDP Build functions ---------------------------------------------------------------------------------------------------

/*
 * Essential file copy like dsdt, ssdt and plists
 */
function copyEssentials($modelNamePath, $dsdt, $ssdt, $theme, $smbios, $chame) {
   
    $os = getVersion();

	$workpath = "/Extra/EDP";
	$extrapath = "/Extra";
	$modelDirPath = "$workpath/model-data/$modelNamePath";
	
    writeToLog("$workpath/logs/build/build.log", " Checking for DSDT, SSDT and System Plist files... will be overwritten if exists<br>");
    
    if (shell_exec("cd $modelDirPath/common; ls | wc -l") > 0 && 
         (!is_dir("$modelDirPath/cpu") || shell_exec("cd $modelDirPath/cpu; ls | wc -l") > 0)) 
    {
		writeToLog("$workpath/logs/build/build.log", " Found Essential files downloaded from SVN, Copying...</b><br>");
	}
	else {
		writeToLog("$workpath/logs/build/build.log", " Essential files not downloaded from SVN (either not found or no internet)</b><br>");
		return;
	}
		
    // use EDP SMBIos?
    if($smbios == "yes")
    {
    	$file1 = "$modelDirPath/common/SMBios.plist"; 
    	$file2 = "$modelDirPath/$os/SMBios.plist"; 

    	// Remove existing file from /Extra
    	// if (file_exists("$extrapath/SMBios.plist")) { system_call("rm $extrapath/SMBios.plist"); }
    	
    	// Copy file from common folder if exists
    	if(file_exists($file1)) {
    		system_call("cp -f $file1 $extrapath"); 
   			writeToLog("$workpath/logs/build/build.log", "  Common SMBios.plist found, Copying to $extrapath<br>");
    	}
    	
    	// Copy file from os folder if exists
    	if(file_exists($file2)) {
    		system_call("cp -f $file2 $extrapath");
   			writeToLog("$workpath/logs/build/build.log", "  OSX specific SMBios.plist found, Copying to $extrapath <br>");
    	}
    	
    } else {
    	writeToLog("$workpath/logs/build/build.log", "  Skipping SMBios.plist file from EDP on user request<br>");
    }
    
    // use EDP org.chameleon.Boot.plist?
    if($chame == "yes")
    {
    	$file1 = "$modelDirPath/common/org.chameleon.Boot.plist"; 
    	$file2 = "$modelDirPath/$os/org.chameleon.Boot.plist"; 

    	// Remove existing file from /Extra
    	// if (file_exists("$extrapath/org.chameleon.Boot.plist")) { system_call("rm $extrapath/org.chameleon.Boot.plist"); }
    	
    	// Copy file from common folder if exists
    	if(file_exists($file1)) {
    		system_call("cp -f $file1 $extrapath"); 
   	  		writeToLog("$workpath/logs/build/build.log", "  Common org.chameleon.Boot.plist found, Copying to $extrapath<br>");
    	}
    	
    	// Copy file from os folder if exists
    	if(file_exists($file2)) {
    		system_call("cp -f $file2 $extrapath");
   	    	writeToLog("$workpath/logs/build/build.log", "  OS specific org.chameleon.Boot.plist found, Copying to $extrapath<br>");
    	}
   	 	
    } else {
    	writeToLog("$workpath/logs/build/build.log", "  Skipping org.chameleon.Boot.plist file from EDP on user request<br>");
    }
    
    // use EDP DSDT?
    if($dsdt == "yes")
    {
    	$file1 = "$modelDirPath/common/dsdt.aml"; 
    	$file2 = "$modelDirPath/$os/dsdt.aml"; 

    	// Remove existing file from /Extra
    	// if (file_exists("$extrapath/dsdt.aml")) { system_call("rm $extrapath/dsdt.aml"); }
    	
    	//Copy file from common folder if exists
    	if(file_exists($file1)) {
    		system_call("cp -f $file1 $extrapath");
    		writeToLog("$workpath/logs/build/build.log", "  Common dsdt found, Copying to $extrapath<br>");
    	}
    	
    	//Copy file from os folder if exists
    	if(file_exists($file2)) {
    		system_call("cp -f $file2 $extrapath");
    		writeToLog("$workpath/logs/build/build.log", "  OS specific dsdt found, Copying to $extrapath<br>");
    	}
    	
    } else {
    	writeToLog("$workpath/logs/build/build.log", "  Skipping DSDT file from EDP on user request<br>");
    }
	
    // If its mavericks then copy the files from ml folder temporarilyfor now
    if($os == "mav" && !is_dir("$modelDirPath/$os") && is_dir("$modelDirPath/ml")) {
    	
    	writeToLog("$workpath/logs/build/build.log", "  mavericks directory is not found, Copying dsdt and plist files from ml folder<br>");
		
		if($modeldb[$modelRowID]["useEDPDSDT"] == "on") {
			$file = "$modelDirPath/ml/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
			}
		if($modeldb[$modelRowID]["useEDPSMBIOS"] == "on") {
			$file = "$modelDirPath/ml/SMBios.plist";             if (file_exists($file)) { system_call("cp -f $file $extrapath"); }
			}
		if($modeldb[$modelRowID]["useEDPCHAM"] == "on") {
			$file = "$modelDirPath/ml/org.chameleon.Boot.plist";  if (file_exists($file)) { system_call("cp -f $file $extrapath"); }	
			}
    }						

	// use EDP org.chameleon.Boot.plist?
	if($chame == "yes") {
		// set UseKernelCache to Yes from org.chameleon.Boot.plist
		system("sudo /usr/libexec/PlistBuddy -c \"set UseKernelCache Yes\" $extrapath/org.chameleon.Boot.plist");
	}
	
	// use EDP SSDT?
    if($ssdt == "yes")
    {
    
      if (is_dir("$modelDirPath/cpu") && shell_exec("cd $modelDirPath/cpu; ls | wc -l") > 0) {
      	$ssdtFilesPath = "$modelDirPath/cpu";
      }
      else {
      	$ssdtFilesPath = "$modelDirPath/common";
      }
      
      if (file_exists("$extrapath/SSDT.aml")) { system_call("rm $extrapath/SSDT.aml"); }
      $file = "$ssdtFilesPath/SSDT.aml";
  	  if (file_exists($file)) 
       { 
    		writeToLog("$workpath/logs/build/build.log", "  SSDT files found, Copying to $extrapath<br>");
    		system_call("cp -f $file $extrapath");
    		
    		// set DropSSDT to Yes from org.chameleon.Boot.plist
			system("sudo /usr/libexec/PlistBuddy -c \"set DropSSDT Yes\" $extrapath/org.chameleon.Boot.plist"); 
   		}
   		
    	if (file_exists("$extrapath/SSDT-1.aml")) { system_call("rm $extrapath/SSDT-1.aml"); }
   		$file = "$ssdtFilesPath/SSDT-1.aml"; if (file_exists($file)) { 
   			system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-2.aml")) { system_call("rm $extrapath/SSDT-2.aml"); }
    	$file = "$ssdtFilesPath/SSDT-2.aml"; if (file_exists($file)) { 
    		system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-3.aml")) { system_call("rm $extrapath/SSDT-3.aml"); }
    	$file = "$ssdtFilesPath/SSDT-3.aml"; if (file_exists($file)) { 
    		system_call("cp -f $file $extrapath"); 
    	}

    	if (file_exists("$extrapath/SSDT-4.aml")) { system_call("rm $extrapath/SSDT-4.aml"); } 
    	$file = "$ssdtFilesPath/SSDT-4.aml"; if (file_exists($file)) {
    		system_call("cp -f $file $extrapath"); 
    	}
    	
    	if (file_exists("$extrapath/SSDT-5.aml")) { system_call("rm $extrapath/SSDT-5.aml"); } 
    	$file = "$ssdtFilesPath/SSDT-5.aml"; if (file_exists($file)) {
    		system_call("cp -f $file $extrapath"); 
    	}	
    }  
    else {
    	writeToLog("$workpath/logs/build/build.log", "  Skipping SSDT files from EDP on user request<br>");
    }
    
    //
    // Copy Themes folder to Extra
    //
    if($theme == "yes")
    {
    	writeToLog("$workpath/logs/build/build.log", "  Copying Themes folder to /Extra...<br>");
		if (!is_dir("/Extra/Themes")) {
			system_call("mkdir /Extra/Themes");
		 }
		 else {
		 	system_call("rm -rf /Extra/Themes/*");
		 }
	 
		if(is_dir("$modelDirPath/common/Themes")) {
			system_call("cp -a $modelDirPath/common/Themes/. /Extra/Themes");
		}
		// Standard theme folder
		else {
			system_call("cp -a $workpath/bin/Themes/. /Extra/Themes");
		}
    }
    else {
    	writeToLog("$workpath/logs/build/build.log", "  Skipping Themes from EDP on user request<br>");
    }
    
}

 /*
  * Function to check if myhack.kext exists in ale, 
  * and if it dosen't for some weird reason... copy it there...
  */
 	function myHackCheck() {
  	  global $workpath, $slepath;

   	 if (!is_dir("$slepath/myHack.kext")) {
    	// copy kext to workpath
        system_call("cp -R \"$workpath/bin/myHack/myHack.kext\" $workpath");
        // Remove svn versioning
        system_call("rm -Rf `find -f path \"$workpath/myHack.kext\" -type d -name .svn`");
        // copy kext to sle
        system_call("cp -R \"$workpath/myHack.kext\" $slepath");
      }
      
   	 if (!is_file("/usr/sbin/")) {
        system_call("cp \"$workpath/bin/myHack/myfix\" /usr/sbin/myfix; chmod 777 /usr/sbin/myfix");
   	 }
   	 
	}
	 
/*
 * Copy EDP Kexts copy for build
 */
 function PrepareEDPKextpacks()
 {
 	//Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $modelNamePath, $ee;
    global $ps2db, $audiodb, $wifidb, $cpufixdb, $batterydb, $landb, $fakesmcdb;
    global $modeldb, $modelRowID;
    global $os;
    
    //Get our class(s)
    global $builder;
	global $svnLoad;

	// model path
	$modelDirPath = "$workpath/model-data/$modelNamePath";
	
	// kextpack svn path
	$kextpacks = "$workpath/kextpacks";    
    
    // Use EDP Kexts?
    if($modeldb[$modelRowID]['useEDPExtensions'] == "on")
    {
    	//
    	// copying PS2 kexts from kextpacks
    	//
    	$ps2id = $modeldb[$modelRowID]['ps2pack'];
    
    	if ($ps2id != "" && $ps2id != "no")
        {
    		$fname = $ps2db[$ps2id]["foldername"];
    		$name = $ps2db[$ps2id]['kextname'];
    		
    		// remove installed kexts before
			if (is_dir("/Extra/Extensions/VoodooPS2Controller.kext"))  {
				system_call("rm -rf /Extra/Extensions/VoodooPS2Controller.kext");
			}
			if (is_dir("/Extra/Extensions/ApplePS2Controller.kext"))  {
				system_call("rm -rf /Extra/Extensions/ApplePS2Controller.kext");
			}
			if (is_dir("/Extra/Extensions/AppleACPIPS2Nub.kext"))  {
				system_call("rm -rf /Extra/Extensions/AppleACPIPS2Nub.kext");
			}
			if (is_dir("/Extra/Extensions/ApplePS2ElanTouchpad.kext"))  {
				system_call("rm -rf /Extra/Extensions/ApplePS2ElanTouchpad.kext");
			}
						
    		// remove voodooPS2 files if its installed before for non-voodoo install
    		if($ps2id != "2" && $ps2id != "5" && $ps2id != "6")
    		{
        		if(is_dir("/Library/PreferencePanes/VoodooPS2.prefpane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2.prefpane");}
        		if(file_exists("/usr/bin/VoodooPS2Daemon")) {system_call("rm -rf /usr/bin/VoodooPS2Daemon");}
        		if(file_exists("/Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist")) {system_call("rm -rf /Library/LaunchDaemons/org.rehabman.voodoo.driver.Daemon.plist");}
        		if(is_dir("/Library/PreferencePanes/VoodooPS2synapticsPane.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooPS2synapticsPane.prefPane");}
    		}
    	
        	if ($fname != "") { 
        	    writeToLog("$workpath/logs/build/build.log", " Preparing to download Touchpad kext $fname...<br>");
        	    
        	    if(!is_dir("$kextpacks/PS2Touchpad"))
    				system_call("mkdir $kextpacks/PS2Touchpad");
    				
    			$svnLoad->PrepareKextpackDownload("PS2Touchpad", "$fname", "$name");
    		}
		 } 
		// Reset vars
		$name = "";
		$fname = "";
		
		
		//
    	// copying Wifi/BT kexts from kextpacks / patch WiFi/BT Kexts
    	//
    	if ($modeldb[$modelRowID]['wifipack'] != "" && $modeldb[$modelRowID]['wifipack'] != "no") 
    	{
        	$wifid = $modeldb[$modelRowID]['wifipack'];
        	$name = $wifidb[$wifid]['kextname'];
        	$fname = $wifidb[$wifid]['foldername'];
        	
        	if ($name != "") {
        	    		
    		switch($wifid) {
    			case 0:    			
    			case 1:
    				writeToLog("$workpath/logs/build/build.log", " Patching WiFi kext $name...<br>");
    				patchWiFiAR9285AndAR9287("$workpath/logs/build/build.log","EE", "no");
    			break;
    			
    			case 2:
    			    writeToLog("$workpath/logs/build/build.log", " Patching WiFi kext $name...<br>");

    				if(getMacOSXVersion() >= "10.8.5")
    					patchWiFiBTBCM4352("$workpath/logs/build/build.log","EE", "no");
    				else
    					writeToLog("$workpath/logs/build/build.log", "  OSX version is not supported for WiFi, need OSX 10.8.5 or later<br>");
    			break;
    			
    			case 4:
    				writeToLog("$workpath/logs/build/build.log", " Preparing to download WiFi kext $fname...<br>");
    					
    				if(!is_dir("$kextpacks/Wireless"))
    					system_call("mkdir $kextpacks/Wireless");
    				
    				$svnLoad->PrepareKextpackDownload("Wireless", "$fname", "$name");
    			break;
    		}
    		
    		// Load Bluetooth kext for AR3011 and BCM4352
    		if($wifid < "3")
    			{
    				writeToLog("$workpath/logs/build/build.log", " Preparing to download Bluetooth kext $fname...<br>");
        	    
        	    	 if(!is_dir("$kextpacks/Wireless"))
    					system_call("mkdir $kextpacks/Wireless");
    					
    				 $svnLoad->PrepareKextpackDownload("Wireless", "BluetoothFWUploader", "BluetoothFWUploader.kext");
    			}
    		}
		}
		// Reset vars
		$name = "";
		$fname = "";    

		 //
   		 // copying fakesmc kexts from kextpacks
   		 //
    	
    	if ($modeldb[$modelRowID]['fakesmc'] != "" && $modeldb[$modelRowID]['fakesmc'] != "no")
    	 {   
    		$fakesmcid = $modeldb[$modelRowID]['fakesmc'];
    		$fname = $fakesmcdb[$fakesmcid]['foldername'];
    		$name = $fakesmcdb[$fakesmcid]['name']; 
    		
    		if ($fname != "") {
    			writeToLog("$workpath/logs/build/build.log", " Preparing to download FakeSMC kext $fname...<br>");
        	    
        	    if(!is_dir("$kextpacks/FakeSMC"))
    				system_call("mkdir $kextpacks/FakeSMC");
    					
    			$svnLoad->PrepareKextpackDownload("FakeSMC", "$fname", "$name");
    		}
     	}
		// Reset vars
		$name = "";
		$fname = "";	
    	
    	
    	//
    	// copying audio kexts
    	//
    	if ($modeldb[$modelRowID]['audiopack'] != "" && $modeldb[$modelRowID]['audiopack'] != "no")
        {
        
        	$audioid = $modeldb[$modelRowID]['audiopack'];
    		$fname = $audiodb[$audioid]['foldername']; 
    		$name = $audiodb[$audioid]['name']; 
    
    
    		//
    		// remove voodooHDA related files if installed before
    		//
    		if($audioid == "no" || $audioid == "builtin") {
        	 	if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
   			 }
        	if (is_dir("$slepath/HDAEnabler.kext")) { system_call("rm -Rf $slepath/HDAEnabler.kext"); }
        	
   			
   			//
			// Check for AppleHDA		
			//
			$usingAppleHDA = "";
			if ($audioid == "builtin") {
				global $modelID, $edp_db;
				global $os;
				$applehda = $edp_db->query("SELECT * FROM applehda WHERE model_id = '$modelID'");
				switch ($os) {
					case "sl":    				
					case "lion":    				
					case "ml":    				
					case "mav":    				
					case "yos":
						foreach($applehda as $row) {
							if ($row[$os] != "no")
							$aID = explode(',', $row[$os]);
						
							if (getVersion() >= $aID[1]) {
								writeToLog("$workpath/logs/build/build.log", " Preparing to download Audio kext patched AppleHDA...<br>");

								if(!is_dir("$modelDirPath/applehda"))
									system_call("mkdir $modelDirPath/applehda");
					
								$svnLoad->PrepareKextpackDownload("Extensions", "audiocommon", "$modelNamePath/applehda");
								$svnLoad->PrepareKextpackDownload("Extensions", "audio$os", "$modelNamePath/applehda");
								$usingAppleHDA = "yes";
							}
							else 
							{
								writeToLog("$workpath/logs/build/build.log", " Chosen Patched AppleHDA is not supported in this OSX version, using latest VoodooHDA instead<br>");
							}
						}
					break;
				}
			}
    		
    		//
    		// Check for VoodooHDA
    		//
        	if ($fname != "" && $usingAppleHDA == "") {
    			        
    		    writeToLog("$workpath/logs/build/build.log", " Preparing to download Audio kext $fname...<br>");

    			if(!is_dir("$kextpacks/Audio"))
    				system_call("mkdir $kextpacks/Audio");
    					    
        		$svnLoad->PrepareKextpackDownload("Audio", "$fname", "$name");
        		
        		// Copy Prefpane and Settings loader
        		$svnLoad->PrepareKextpackDownload("Audio", "Settings", "AudioSettings");
        	} 
        	
   	 	}
   	 	// Reset vars
		$name = "";
		$fname = "";
	
		//
		// copying ethernet kexts from kextpacks
		//
    	if ($modeldb[$modelRowID]['ethernet'] != "" && $modeldb[$modelRowID]['ethernet'] != "no")
    	 {
        	$lanid = $modeldb[$modelRowID]['ethernet'];
        	$name = $landb[$lanid]['name'];
        	$fname = $landb[$lanid]['foldername'];
        	
        	if ($fname != "") {
        		
        		writeToLog("$workpath/logs/build/build.log", " Preparing to download Ethernet kext $name...<br>");
        	    
    			if(!is_dir("$kextpacks/Ethernet"))
    				system_call("mkdir $kextpacks/Ethernet");
    			
    			// Category folder
    			if(!is_dir("$kextpacks/Ethernet/$fname"))
    				system_call("mkdir $kextpacks/Ethernet/$fname");
    		
    			// New Realtek kext
    			if($lanid == "11") {
				
					// Choose 10.8+ version 
					if(getMacOSXVersion() >= "10.8")
						$svnLoad->PrepareKextpackDownload("Ethernet", "$fname", "RealtekRTL8111");
					
					// Choose Lion version
					else if(getMacOSXVersion() == "10.7")
						$svnLoad->PrepareKextpackDownload("Ethernet", "$fname", "RealtekRTL8111_Lion");
    			}
    			else
    				$svnLoad->PrepareKextpackDownload("Ethernet", "$fname", "$name");   
     	  	}	
		}
		// Reset vars
		$name = "";
		$fname = "";
		
	 //	
	 // copying battery kexts from kextpacks
	 //
   	 if ($modeldb[$modelRowID]['batterypack'] != "" && $modeldb[$modelRowID]['batterypack'] != "no") 
   	 {
        $battid = $modeldb[$modelRowID]['batterypack'];
        $fname = $batterydb[$battid]['foldername'];
        $name = $batterydb[$battid]['name'];
        
        if ($fname != "") {
        		writeToLog("$workpath/logs/build/build.log", " Preparing to download Battery kext $name...<br>");
        	    
        	    if(!is_dir("$kextpacks/Battery"))
    				system_call("mkdir $kextpacks/Battery");
    				
    			$svnLoad->PrepareKextpackDownload("Battery", "$fname", "$name");  
    		}
	   }
		// Reset vars
		$name = "";
		$fname = "";
    } 
    else {
    	writeToLog("$workpath/logs/build/build.log", " Skipping Standard Kexts from EDP on user request<br>");
    }
    
    //
    // Copy selected optional kexts
    //
    $data = $modeldb[$modelRowID]['optionalpacks'];
    $array 	= explode(',', $data);
    global $edpDBase;
    
    foreach($array as $id) {
    	$opdata = $edpDBase->getKextpackDataFromID("optionalpacks", $id);
        $categ = $opdata[category];
        $fname = $opdata[foldername];
        $name = $opdata[name];
        
         if ($fname != "") { 
         
         	writeToLog("$workpath/logs/build/build.log", " Preparing to download Optional pack $name...<br>");

    		if(!is_dir("$kextpacks/$categ"))
    			system_call("mkdir $kextpacks/$categ");
    		
    		// Generic XHCI USB3.0
    		if($id == "5") {
				// Choose new version 
				if(getMacOSXVersion() >= "10.8.5")
					$svnLoad->PrepareKextpackDownload("$categ", "GenericXHCIUSB3_New", "$name");
				
				// Choose old version
				else if(getMacOSXVersion() < "10.8.5")
					$svnLoad->PrepareKextpackDownload("$categ", "$fname", "$name");
    		}
    		else	
    			$svnLoad->PrepareKextpackDownload("$categ", "$fname", "$name");
    	 }
      }
    	// Reset vars
		$name = "";
		$fname = "";
		
	writeToLog("$workpath/logs/build/build.log", " Preparing to download Standard kexts... <br>");

	//
    // Standard kexts
    //
    if(!is_dir("$workpath/kextpacks/Standard"));
    	system_call("mkdir $workpath/kextpacks/Standard");
    	
    $svnLoad->PrepareKextpackDownload("Standard", "common", "Standard common");

    $svnLoad->PrepareKextpackDownload("Standard", "$os", "Standard $os");
    
    writeToLog("$workpath/logs/build/build.log", " Preparing to download Model specific kexts... <br>");

    //
	// From Model data (Extensions folder)
	//
	$svnLoad->PrepareKextpackDownload("Extensions", "kextscommon", "$modelNamePath/Extensions");
	$svnLoad->PrepareKextpackDownload("Extensions", "kexts$os", "$modelNamePath/Extensions");
	
    // From Model data (Common and $os folder used before, have to remove this when all the models updated to new Extensions folder)
    if(is_dir("$modelDirPath/common/Extensions"))
    {
    	writeToLog("$workpath/logs/build/build.log", "  Copying kexts from model common folder to $ee<br>");
    	$tf = "$modelDirPath/common/Extensions";
    	system_call("cp -a $tf/. $ee/");
    }
    if(is_dir("$modelDirPath/$os/Extensions"))
    {
    	writeToLog("$workpath/logs/build/build.log", "  Copying kexts from model $os folder to $ee<br>");
    	$tf = "$modelDirPath/$os/Extensions";
    	system_call("cp -a $tf/. $ee/");
    }
    
    //
    // Download custom kernel from EDP
    //
    	  	
    $svnLoad->PrepareKextpackDownload("Kernel", "kernel$os", "$modelNamePath/Kernel");
    
	
    //
    // Create a script file if we need to copy kexts from Extra/include/Extensions
    //
    if($modeldb[$modelRowID]["useIncExtensions"] == "on")
    {
    	writeToLog("$workpath/logs/build/dLoadScripts/CopyCustomKexts.sh", "");
    } 
    
 }
 
/*
 * Fixes
 */
function applyFixes() {
	//Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $modelNamePath, $os, $ee;
    global $sysType, $modeldb, $modelRowID, $modelID;
	global $edpDBase;
	global $cpufixdb;

	// model path
	$modelDirPath = "$workpath/model-data/$modelNamePath";
	
	//Get our class(s)
	global $svnLoad;
	
	//kextpack svn path
	$kextpacks = "$workpath/kextpacks";
	
    writeToLog("$workpath/logs/build/build.log", " Applying fixes and patches...... <br>");
	
	//
	// Apply power management related fixes 
	//
    $mdata = $edpDBase->getModelDataFromID($sysType, $modelID);
    $array 	= explode(',', $mdata['pmfixes']);
    
    $i = 0; // iterating through all the id's
	while ($cpufixdb[$i] != "") {
	    // Get foldername from ID
        $cpufixdata = $edpDBase->getKextpackDataFromID("pmfixes", "$i");
        $foldername = $cpufixdata[foldername];
        $name = $cpufixdata[edpid];
        
        // Checking if we need to patch AppleIntelCPUPowerManagement.kext
        if(($modeldb[$modelRowID]['applecpupwr'] == "on") && $i == "1") {
        	patchAppleIntelCPUPowerManagement("$workpath/logs/build/build.log","EE", "no");
        }
        else if(($modeldb[$modelRowID]['emupstates'] == "on") && $i == "3") {
        	
        	$svnLoad->PrepareKextpackDownload("PowerMgmt", "VoodooPState", "$foldername"); 
        }
        else if ($foldername != "" && $modeldb[$modelRowID][$cpufixdata[edpid]] == "on") { 

    		if(!is_dir("$kextpacks/PowerMgmt"))
    			system_call("mkdir $kextpacks/PowerMgmt");
    		
    		$svnLoad->PrepareKextpackDownload("PowerMgmt", "$foldername", "$foldername");
    		
    		 //remove PStateMenu if installed before
    		 if (file_exists("/Library/LaunchAgents/PStateMenu.plist")) { system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist"); }
    	}
    	$i++;
	}

	// Reset vars
	$name = "";
	$fname = "";
		
	//
 	// Apply Generic fixes
 	//
    $data = $modeldb[$modelRowID]['fixes'];
    $array 	= explode(',', $data);
    
    foreach($array as $id) {
	    //Getting names from ID
	    $fixdata = $edpDBase->getKextpackDataFromID("sysfixes", "$id");
        $categ = $fixdata[category];
        $fname = $fixdata[foldername];
        $name = $fixdata[name];
        
       if($id == "2") {
       		writeToLog("$workpath/logs/build/build.log", " Patching AHCI.kext to waiting for root device problem in ML<br>");
       		patchAHCI();
       }
       else if($id == "8") {
        	patchAppleIntelSNBGraphicsFB("$workpath/logs/build/build.log","EE", "no");
        }
       else if ($fname != "") { 

			if($id == "1") {
       			writeToLog("$workpath/logs/build/build.log", " Applying ACPI fix for Battery read and Coolbook...<br>");
       		}
       		else if($id == "5") {
       			writeToLog("$workpath/logs/build/build.log", " Preparing to download patched IOATAFamily fix for IDE disks...<br>");
       		}
       		
    		if(!is_dir("$kextpacks/$categ"))
    			system_call("mkdir $kextpacks/$categ");
    		
    		$svnLoad->PrepareKextpackDownload("$categ", "$fname", "$name");
    	}
	}
    	
    // Reset vars
	$name = "";
	$fname = "";
} 
 
/*
 * Copying custom kexts 
 */
function copyCustomFiles() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $incpath, $os, $ee, $modelNamePath;
	global $modeldb, $modelRowID;
	
	// model path
	$modelDirPath = "$workpath/model-data/$modelNamePath";
	
	$extrapath = "/Extra";

	writeToLog("$workpath/logs/build/build.log", " Checking for Custom files from EDP model database... <br>");

	//
    // Check if we need a custom version of chameleon from essential common and $os folders
    //
    if ($modeldb[$modelRowID]['customCham'] == "on") {
        
        $cboot = "$modelDirPath/common/boot";
        $osboot = "$modelDirPath/$os/boot";
        
        if(is_file("$cboot") || is_file("$osboot"))
        {
        	writeToLog("$workpath/logs/build/build.log", "  Custom chameleon found, copied to $rootpath <br>");

        	system_call("rm -f $rootpath/boot");
        	system_call("cp $modelDirPath/common/boot $rootpath");
        	system_call("cp $modelDirPath/$os/boot $rootpath");
        }
    }
    
	//
    // Check if we need a custom made kernel from EDP model kernel folder
    //
    
    if(is_dir("$modelDirPath/kernel/kernel$os")) {
        	
        $ckernel = "$modelDirPath/kernel/kernel$os/custom_kernel";
        if(is_file("$ckernel"))
        {
        	writeToLog("$workpath/logs/build/build.log", "  Custom kernel found, copied to $rootpath <br>");
        	system_call("rm -f $rootpath/custom_kernel");
       		system_call("cp $modelDirPath/kernel/kernel$os/custom_kernel $rootpath");
        }
        $kernelos = "$modelDirPath/kernel/kernel$os/mach_kernel";
        if(is_file("$kernelos"))
        {
        	writeToLog("$workpath/logs/build/build.log", "  Custom mach_kernel found, copied to $rootpath <br>");
        	system_call("rm -f $rootpath/mach_kernel");
       		system_call("cp $modelDirPath/kernel/kernel$os/mach_kernel $rootpath");
        }
    }
    
	writeToLog("$workpath/logs/build/build.log", " Checking for user provided custom files from $incpath... <br>");

	//
    // Copy essentials from /Extra/include if user has
    //

    if (is_file("$incpath/smbios.plist") && $modeldb[$modelRowID]["useIncSMBIOS"] == "on") 				{ 
    	writeToLog("$workpath/logs/build/build.log", "  Custom smbios.plist found, Copied from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/smbios.plist /Extra"); 
    }
    if (is_file("$incpath/org.chameleon.Boot.plist") && $modeldb[$modelRowID]["useIncCHAM"] == "on") 	{ 
    	writeToLog("$workpath/logs/build/build.log", "  Custom org.chameleon.Boot.plist found, Copied from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/org.chameleon.Boot.plist /Extra"); 
    }
    if (is_file("$incpath/dsdt.aml") && $modeldb[$modelRowID]["useIncDSDT"] == "on") 					{ 
    	writeToLog("$workpath/logs/build/build.log", "  Custom dsdt file found, Copied from $incpath to $extrapath<br>");
    	system_call("cp -f $incpath/dsdt.aml /Extra"); 
    }
    if($modeldb[$modelRowID]["useIncSSDT"] == "on")
    {
    	if (is_file("$incpath/SSDT.aml")) 					{ 
    		writeToLog("$workpath/logs/build/build.log", "  Custom SSDT files found, Copied from $incpath to $extrapath<br>");
    		system_call("cp -f $incpath/SSDT.aml /Extra"); 
    	}
    	if (is_file("$incpath/SSDT-1.aml")) 				{ system_call("cp -f $incpath/SSDT-1.aml /Extra"); }
    	if (is_file("$incpath/SSDT-2.aml")) 				{ system_call("cp -f $incpath/SSDT-2.aml /Extra"); }
    	if (is_file("$incpath/SSDT-3.aml")) 				{ system_call("cp -f $incpath/SSDT-3.aml /Extra"); }    
    	if (is_file("$incpath/SSDT-4.aml")) 				{ system_call("cp -f $incpath/SSDT-4.aml /Extra"); }
    	if (is_file("$incpath/SSDT-5.aml")) 				{ system_call("cp -f $incpath/SSDT-5.aml /Extra"); }  
    }
    
    //
    // Copy Custom Themes folder from $incpatch to /Extra
    //
    if(is_file("$workpath/logs/build/dLoadScripts/CopyCustomTheme.sh") && shell_exec("cd $incpath/Themes; ls | wc -l") > 0)
    {
    	if (is_dir("$incpath/Themes")) {
			writeToLog("$workpath/logs/build/build.log", "  Custom themes folder found, copied to /Extra<br>");
			system_call("rm -rf /Extra/Themes");
			system_call("mkdir /Extra/Themes");
			system_call("cp -a $incpath/Themes/. /Extra/Themes/");
     	}
    }
     
	//
    // Copying Custom kexts from include if CopyCustomKexts file exists
    //
    if(is_file("$workpath/logs/build/dLoadScripts/CopyCustomKexts.sh") && shell_exec("cd $incpath/Extensions; ls | wc -l") > 0)
    {
    	writeToLog("$workpath/logs/build/build.log", "  Custom kexts found, copied to /Extra<br>");
    	system_call("cp -a $incpath/Extensions/. $ee/");
    	
    	//If AppleHDA is found in Extra/include then remove VoodooHDA from ee
    	if(file_exists("$incpath/Extensions/AppleHDA.kext")) {
    			if(is_dir("/Applications/VoodooHdaSettingsLoader.app")) {system_call("rm -rf /Applications/VoodooHdaSettingsLoader.app");}
        	 	if(file_exists("/Library/LaunchAgents/com.restore.voodooHDASettings.plist")) {system_call("rm -rf /Library/LaunchAgents/com.restore.voodooHDASettings.plist");}
        	 	if(is_dir("/Library/PreferencePanes/VoodooHDA.prefPane")) {system_call("rm -rf /Library/PreferencePanes/VoodooHDA.prefPane");}
    			system_call("rm -rf $ee/VoodooHDA.kext");
    			system_call("rm -rf $ee/AppleHDADisabler.kext");
    			writeToLog("$workpath/logs/build/build.log", "  Found AppleHDA from $incpath, VoodooHDA removed<br>");
   		 }
    } 

}	
	
?>