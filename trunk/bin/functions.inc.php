<?php

//------------------> EDPweb functions -----------------------------------------------------------------------------------------------

function checkbox($title, $formname, $status) {
	if ($status == "yes") { $c = "checked"; }
	echo "<li class='checkbox'><span class='name'>$title</span><input name='$formname' type='checkbox' $c/> </li>\n";
}

//<-----------------------------------------------------------------------------------------------------------------------------------



class builder {

	public function copyOptinalKextPack($id) {
		if ($id != "") {
			global $edp_db; global $workpath; global $ee;
			$stmt = $edp_db->query("SELECT * FROM optionalpacks where id = '$id'");
			$stmt->execute();
			$bigrow = $stmt->fetchAll(); $row = $bigrow[0];
			if ($row[foldername] != "") {
				$folder = "$workpath/storage/kextpacks/$row[foldername]";
				system_call("cp -R $folder/. $ee");
				return;
			}
		}
	}
	
		
}


$builder = new builder();






function copyChamModules($chamModConfig) {
	global $workpath;
	$modpathFROM 	= "$workpath/storage/modules";
	$modpathTO 		= "$workpath/modules/";
	
	//Cleaning existing modules folder
	system_call("rm -Rf /Extra/modules/*");
	
	//Copying modules
	if ($chamModConfig['ACPICodec'] == "on") 		{ system_call("cp -Rf $modpathFROM/ACPICodec.dylib $modpathTO"); }
	if ($chamModConfig['FileNVRAM'] == "on") 		{ system_call("cp -Rf $modpathFROM/FileNVRAM.dylib $modpathTO"); }
	if ($chamModConfig['KernelPatcher'] == "on") 	{ system_call("cp -Rf $modpathFROM/KernelPatcher.dylib $modpathTO"); }
	if ($chamModConfig['Keylayout'] == "on") 		{ system_call("cp -Rf $modpathFROM/Keylayout.dylib $modpathTO"); }		
	if ($chamModConfig['klibc'] == "on") 			{ system_call("cp -Rf $modpathFROM/klibc.dylib $modpathTO"); }
	if ($chamModConfig['Resolution'] == "on") 		{ system_call("cp -Rf $modpathFROM/Resolution.dylib $modpathTO"); }	
	if ($chamModConfig['Sata'] == "on") 			{ system_call("cp -Rf $modpathFROM/Sata.dylib $modpathTO"); }
	if ($chamModConfig['uClibcxx'] == "on") 		{ system_call("cp -Rf $modpathFROM/uClibcxx.dylib $modpathTO"); }	
}

function chamModGetConfig() {
	global $workpath;
	$modpath = "$workpath/modules";

	$array = array(
		"ACPICodec" => (is_file("$modpath/ACPICodec.dylib") === TRUE ? "yes" : "no"),
		"FileNVRAM" => (is_file("$modpath/FileNVRAM.dylib") === TRUE ? "yes" : "no"),
		"KernelPatcher" => (is_file("$modpath/KernelPatcher.dylib") === TRUE ? "yes" : "no"),
		"Keylayout" => (is_file("$modpath/Keylayout.dylib") === TRUE ? "yes" : "no"),
		"klibc" => (is_file("$modpath/klibc.dylib") === TRUE ? "yes" : "no"),
		"Resolution" => (is_file("$modpath/Resolution.dylib") === TRUE ? "yes" : "no"),
		"Sata" => (is_file("$modpath/Sata.dylib") === TRUE ? "yes" : "no"),
		"uClibcxx" => (is_file("$modpath/uClibcxx.dylib") === TRUE ? "yes" : "no"),
					
 	);
 	return $array;	
}


		
function updateEDP() {
    include_once "config.inc.php";
    global $workpath;
    
    echo "<pre>";;
    echo "Cleaning up $workpath using SVN <br>\n";
    system_call("svn cleanup $workpath");
    
    echo "<br>Downloading latest sources from EDP's svn server<br>\n";
    system_call("svn --non-interactive --username edp --password edp --force update $workpath");

    echo "Updating database... <br>\n";
    system_call("rm -Rf /Extra/bin/edp.sqlite3; curl -o /Extra/bin/edp.sqlite3 http://www.osxlatitude.com/dbupdate.php");

    system_call("chmod -R 755 /Extra");
    echo "<br> .. Your EDP have been updated...<br><br>.. Press COMMAND+R to reload EDP...<br>\n";

    exit;
}

function writeToLog($logfile, $data) {
    file_put_contents($logfile, $data, FILE_APPEND | LOCK_EX);
}

function lastMinFixes() {
	global $workpath;
	writeToLog("$workpath/build.log", "Clearing boot-args in NVRAM...<br>");
	system_call("nvram -d boot-args");
}


			
function updateCham() {
    // Note: Overtime we will add a function to make sure that the user have the latest version 
    // of cham distrobuted with EDP - until then, we will force the update on each build    
    echo "  Updating Chameleon to latest versions from EDP \n";
    system_call("cp -f /Extra/storage/boot /");
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
    system_call("perl /Extra/bin/fixes/patch-ahci-mlion.pl >>$workpath/build.log");
}

/**
 * Function to check if the model is allready checked out
 * if the model is not checked out it will check it out
 */
function svnModeldata($model) {
    global $workpath;

    $modelfolder = "$workpath/model-data/$model";
    
    if (is_dir("$modelfolder")) {
        writeToLog("$workpath/build.log", "Locale cache found for $model, updating cache....<br>");
        system_call("svn --non-interactive --username edp --password edp --force update $modelfolder >>$workpath/build.log");
    } else {
        echo "  Locale cache NOT found for $model, downloading....\n";
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

function getKextVersion($kext) {
    global $workpath;
    
    if (!is_dir($kext)) {
        return "0.00";
    }
    
    include_once "$workpath/bin/PlistParser.inc";
    $parser = new plistParser();
    $plist = $parser->parseFile("$kext/Contents/Info.plist");
    reset($plist);
    
    while (list($key, $value) = each($plist)) {
        if ($key == "CFBundleShortVersionString") {
            return "$value";
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
        if ($os == "lion") {
            system_call("cp -R $workpath/storage/fixes/gma950-brightness-fix/AppleIntelIntegratedFramebuffer.kext $ee");
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
 * Used for listing to user input in console mode
 */
function getChoice() {
    $stdin = fopen('php://stdin', 'r');
    $choice = trim(fgets($stdin, 100));
    fclose($stdin);
    
    return $choice;
}

function patchAppleIntelCPUPowerManagement() {
    global $ee, $slepath;
    system_call("cp -R $slepath/AppleIntelCPUPowerManagement.kext $ee/");
    system_call('perl -pi -e \'s|\xE2\x00\x00\x00\x0F\x30|\xE2\x00\x00\x00\x90\x90|g\' /Extra/Extensions/AppleIntelCPUPowerManagement.kext/Contents/MacOS/AppleIntelCPUPowerManagement');
}
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
    system_call("diskutil repairPermissions $rootpath");
    system_call("clear");
    echo "Building new cache for kexts in $slepath \n";
    system_call("touch \"$slepath\"");
    echo "Fix applied.. return to menu in 2 secs..";
    system_call("sleep 2");
    loadFixsystem_call();
}
	
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
    if ($v == "10.8.7") { $r="ml"; $os_string = "OSX Mountain Lion $v"; }				
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

    if (!is_dir("$chkdir") && $workpath == "/Extra") {
        system_call("mkdir $chkdir");
        if (file_exists($kerncachefile)) {
            echo "\n\nWARNING: Falling back to EDP kernelcache generation - myfix was not successfull.. \n\n";
            system_call("kextcache -system-prelinked-kernel");
        }
    }
}
function copyEssentials() {
    global $workpath, $modeldb, $modelID, $os;

    $modelName = $modeldb[$modelID]["name"];

    writeToLog("$workpath/build.log", "Cleaning up by system...<br>");
    edpCleaner();

    $file = "$workpath/smbios.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$workpath/org.chameleon.Boot.plist";
    if (file_exists($file)) { system_call("rm $file"); }

    $file = "$workpath/dsdt.aml";
    if (file_exists($file)) { system_call("rm $file"); }
        

    //Remove old SSDT table files
    $file = "$workpath/SSDT.aml";   if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-1.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-2.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-3.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-4.aml"; if (file_exists($file)) { system_call("rm $file"); }
    $file = "$workpath/SSDT-5.aml"; if (file_exists($file)) { system_call("rm $file"); }

    writeToLog("$workpath/build.log", "  Copying COMMON system plists and dsdt.aml from $workpath/model-data/$modelName/common to $workpath<br>");
    $file = "$workpath/model-data/$modelName/common/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $workpath"); }

    writeToLog("$workpath/build.log", "  Copying OS Specific system plists and dsdt.aml from $workpath/model-data/$modelName/$os to $workpath<br>");		
    $file = "$workpath/model-data/$modelName/$os/smbios.plist";             if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/$os/org.chameleon.Boot.plist"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/$os/dsdt.aml";                 if (file_exists($file)) { system_call("cp -f $file $workpath"); }					

    writeToLog("$workpath/build.log", "  Checking if your model includes SSDT dump files - will copy if any exists..<br>");
    $file = "$workpath/model-data/$modelName/common/SSDT.aml";   if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-1.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-2.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-3.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-4.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }
    $file = "$workpath/model-data/$modelName/common/SSDT-5.aml"; if (file_exists($file)) { system_call("cp -f $file $workpath"); }			


    //Copy essentials from /Extra/include
    writeToLog("$workpath/build.log", "  Checking if we have any essential files in /Extra/include that will be used instead...<br>");
    if (is_file("$workpath/include/smbios.plist")) 				{ system_call("cp -f $workpath/include/smbios.plist /Extra"); }
    if (is_file("$workpath/include/org.chameleon.Boot.plist")) 	{ system_call("cp -f $workpath/include/org.chameleon.Boot.plist /Extra"); }
    if (is_file("$workpath/include/dsdt.aml")) 					{ system_call("cp -f $workpath/include/dsdt.aml /Extra"); }
    if (is_file("$workpath/include/SSDT.aml")) 					{ system_call("cp -f $workpath/include/SSDT.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-1.aml")) 				{ system_call("cp -f $workpath/include/SSDT-1.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-2.aml")) 				{ system_call("cp -f $workpath/include/SSDT-2.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-3.aml")) 				{ system_call("cp -f $workpath/include/SSDT-3.aml /Extra"); }    
    if (is_file("$workpath/include/SSDT-4.aml")) 				{ system_call("cp -f $workpath/include/SSDT-4.aml /Extra"); }
    if (is_file("$workpath/include/SSDT-5.aml")) 				{ system_call("cp -f $workpath/include/SSDT-5.aml /Extra"); }    
}

function copyKexts() {
    //Get vars from config.inc.php
    global $workpath, $rootpath, $slepath, $ps2db, $audiodb, $incpath, $wifidb, $modeldb, $modelID, $os, $ee, $batterydb, $landb; global $fakesmcdb;
    
    //Get our class(s)
    global $builder;

    $modelName = $modeldb[$modelID]["name"];

    writeToLog("$workpath/build.log", "  Start by cleaning up in $ee..<br>");
    system_call("rm -Rf $ee/*");

    //Checking if we need to patch AppleIntelCPUPowerManagement.kext
    $pathCPU = $modeldb[$modelID]["patchCPU"];
    if ($pathCPU == "yes") {
        patchAppleIntelCPUPowerManagement();
    }

    writeToLog("$workpath/build.log", "  Copying the PS2 controller kexts (mouse+keyboard driver) to $ee<br>");
    $ps2id = $modeldb[$modelID]['ps2pack'];
    if ($ps2id != "" && $ps2id != "no") {
        $ps2dir = $ps2db[$ps2id]["foldername"];
        system_call("cp -R $workpath/storage/kextpacks/$ps2dir/. $ee/");
    }

    writeToLog("$workpath/build.log", "  Copying custom kexts from $workpath/include/Extensions to $ee/<br>");
    system_call("cp -R $workpath/include/Extensions/* $ee");

    $audioid = $modeldb[$modelID]['audiopack'];
    $audiodir = $audiodb[$audioid]["foldername"];
    if ($modeldb[$modelID]['audiopack'] != "" && $modeldb[$modelID]['audiopack'] != "no") {
        writeToLog("$workpath/build.log", "  Copying the Audio kexts to $ee<br>");
        //Clean up
        if (is_dir("$slepath/HDAEnabler.kext")) {
            system_call("rm -Rf $slepath/HDAEnabler.kext");
        }
        system_call("cp -R $workpath/storage/kextpacks/$audiodir/. $ee/");
    }

    //Copying FakeSMC kextpack
    $fakesmcid = $modeldb[$modelID]['fakesmc'];
    $fakesmcdir = $fakesmcdb[$fakesmcid]["foldername"];
    if ($modeldb[$modelID]['fakesmc'] != "" && $modeldb[$modelID]['fakesmc'] != "no") {
        writeToLog("$workpath/build.log", "  Copying the FakeSMC kexts to $ee<br>");
        system_call("cp -R $workpath/storage/kextpacks/$fakesmcdir/. $ee/");
    }

    //Copying ethernet kexts
    if ($modeldb[$modelID]['ethernet'] != "" && $modeldb[$modelID]['ethernet'] != "no") {
        $lanid = $modeldb[$modelID]['ethernet'];
        $lankext = $landb[$lanid]['kextname'];
        writeToLog("$workpath/build.log", "  Copying the Lan kext to $ee ($lankext - ID: $lanid)<br>");
        if ($lankext != "") {
            system_call("cp -R $workpath/storage/kexts/networking/$lankext $ee/");
        }
    }

    //Copying wifi kexts
    if ($modeldb[$modelID]['wifikext'] != "" && $modeldb[$modelID]['wifikext'] != "no") {
        $wifid = $modeldb[$modelID]['wifikext'];
        $wififolder = $wifidb[$wifid]['foldername'];
        $wifikextname = $wifidb[$wifid]['kextname'];
        if ($wififolder != "") {
            writeToLog("$workpath/build.log", "  Copying the wifi kext to $ee ($wifikextname)<br>");
            system_call("cp -R $workpath/storage/kextpacks/$wififolder/. $ee/");
        }
    }

    //Checking if we need nullcpu
    if ($modeldb[$modelID]['nullcpu'] == "yes" || $modeldb[$modelID]['nullcpu'] == "y") {
        writeToLog("$workpath/build.log", "  Copying NullCPUPowerManagement.kext for disabling Apples native power management.. <br>");
        system_call("cp -R $workpath/storage/kexts/NullCPUPowerManagement.kext $ee");
    }

    //Checking if we need to patch AHCI
    if ($modeldb[$modelID]['patchAHCIml'] == "yes" || $modeldb[$modelID]['patchAHCIml'] == "y") {
        writeToLog("$workpath/build.log", "  Patching IOAHCIFamily.kext for OS: $os... <br>");
        if ($os == "ml") {
            patchAHCI();
        }
    } 
    
    
    //Checking if we need USB Roolback fix
    if ($modeldb[$modelID]['usbRollBack'] == "yes" || $modeldb[$modelID]['usbRollBack'] == "y") {
    	writeToLog("$workpath/build.log", "  Copying USB Roolback kexts... <br>");
    	system_call("cp -R $workpath/storage/kextpacks/usb_rollback/* $ee");
    } 
    
    //Copy optinal kexts
    $data = $modeldb[$modelID]['optionalpacks'];
    $array 	= explode(',', $data);
    writeToLog("$workpath/build.log", "  Copying optinal kextpacks...<br>");
    foreach($array as $id) {
    	echo "ID: $id <br>";
	   	if ($id != "") {
	   		$builder->copyOptinalKextPack($id);
	   	}
	}

    //Checking if we need Sleepenabler
    if ($modeldb[$modelID]['sleepEnabler'] == "yes" || $modeldb[$modelID]['sleepEnabler'] == "y") {
        writeToLog("$workpath/build.log", "  Copying SleepEnabler.kext for enabling sleep...<br>");
        system_call("cp -R $workpath/storage/kexts/$os/SleepEnabler.kext $ee");
    }

    if ($modeldb[$modelID]['loadIOATAFamily'] == "yes") {
        writeToLog("$workpath/build.log", "  Copying IOATAFamily.kext to $ee.. <br>");
        system_call("cp -R $workpath/storage/kexts/IOATAFamily.kext $ee");
    }

    if ($modeldb[$modelID]['loadNatit'] == "yes") {
        writeToLog("$workpath/build.log", "  Copying Natit.kext to $ee.. <br>");
        system_call("cp -R $workpath/storage/kexts/natit.kext $ee");
    }

    if ($modeldb[$modelID]['tscsync'] == "yes" || $modeldb[$modelID]['tscsync'] == "y") {
        writeToLog("$workpath/build.log", "  Check if we need VoodooTSCSync.kext for syncing CPU cores...<br>");
        system_call("cp -R $workpath/storage/kexts/VoodooTSCSync.kext $ee");
    }

    if ($modeldb[$modelID]['emulatedST'] == "yes" || $modeldb[$modelID]['emulatedST'] == "y") {
        writeToLog("$workpath/build.log", "  Check if we are using emulated speedstep via voodoopstate and voodoopstatemenu <br>");
        system_call("cp -R $workpath/storage/kexts/VoodooPState.kext $ee");
        system_call("cp $workpath/storage/LaunchAgents/PStateMenu.plist /Library/LaunchAgents");
    } else {
        system_call("rm -rf /Library/LaunchAgents/PStateMenu.plist");
    }

    //Copy battery kexts
    if ($modeldb[$modelID]['batteryKext'] != "" && $modeldb[$modelID]['batteryKext'] != "no") {
        $battid = $modeldb[$modelID]['batteryKext'];
        $battkext = $batterydb[$battid]["kextname"];
        writeToLog("$workpath/build.log", "  Copying Battery kext ($battkext) to $ee <br>");
        system_call("cp -R $workpath/storage/kexts/battery/$os/$battkext $ee/");
    }

    //Check if we need a custom version of chameleon
    if ($modeldb[$modelID]['customCham'] == "yes" || $modeldb[$modelID]['customCham'] == "y") {
        writeToLog("$workpath/build.log", "  Copying custom chameleon to $rootpath.. <br>");
        system_call("rm -f $rootpath/boot");
        system_call("cp $workpath/model-data/$modelName/$os/boot $rootpath");
    }

    //Check if we need a custom made kernel
    if ($modeldb[$modelID]['customKernel'] == "yes" || $modeldb[$modelID]['customKernel'] == "y") {
        writeToLog("$workpath/build.log", "  Copying custom made kernel to $rootpath.. <br>");
        system_call("rm -f $rootpath/custom_kernel");
        system_call("cp $workpath/model-data/$modelName/$os/custom_kernel $rootpath");
    }
    
    writeToLog("$workpath/build.log", "  Copying standard kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/common/Extensions/* $ee");

    writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    system_call("cp -R $workpath/storage/standard/$os/Extensions/* $ee");

    writeToLog("$workpath/build.log", "  Copying common kexts to $ee..<br>");
    $tf = "$workpath/model-data/$modelName/common/Extensions";
    system_call("cp -Rf $tf/* $ee");

    writeToLog("$workpath/build.log", "  Copying $os kexts to $ee.. <br>");
    $tf = "$workpath/model-data/$modelName/$os/Extensions";
    system_call("cp -Rf $tf/* $ee");

    writeToLog("$workpath/build.log", "  Applying fixes... <br>");
    AppleACPIfixCheck();
    GMA950brightnessfixCheck();

    writeToLog("$workpath/build.log", "  Applying custom plists, kexts etc.");
    //Copying kexts
    system_call("cp -R $incpath/Extensions/* $ee");
    //Copying any .AML files to /Extra
    system_call("cp -R $incpath/*.aml $workpath");
    //Copying any plists files to /Extra
    system_call("cp -R $incpath/*.plist $workpath");

    writeToLog("$workpath/build.log", "  Removing version control of kexts in $ee");
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