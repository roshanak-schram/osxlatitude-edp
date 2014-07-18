 
<?php

class kexts {
 
    //------> Function to get version from kext
    public function getKextVersion($kext) {
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
	
	/*
 	 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 	 */
	function kextpackLoader($categ, $fname, $name) {
		global $workpath, $edp, $ee;
    	    	
    	if(!is_dir("$workpath/kpsvn/dload/statFiles"))
			$createStatFile = "mkdir $workpath/kpsvn/dload/statFiles; cd $workpath/kpsvn/dload/statFiles; touch $fname.txt";
    	else		
			$createStatFile = "cd $workpath/kpsvn/dload/statFiles; touch $fname.txt";	
		
		$endStatFile = "cd $workpath/kpsvn/dload/statFiles; rm -rf $fname.txt";
		
		//
		// Download custom Kexts, kernel and AppleHDA from model data
		//
    	if ($categ == "Extensions"  || $categ == "Kernel")
		{
			$categdir = "$workpath/model-data/$name";
			$packdir = "$categdir/$fname";
			$svnpath = "model-data/$name/$fname";
			$copyKextCmd = "cp -a $workpath/model-data/$name/$fname/*.kext $ee/; echo \"Copy : $fname file(s) installed<br>\" >> $workpath/build.log";
			$name = $fname;
		}
		//
		// Download kexts and booloader from kextpacks
		//
		else {
			$categdir = "$workpath/kpsvn/$categ";
			$packdir = "$categdir/$fname";
			$svnpath = "kextpacks/$categ/$fname";
			$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";


			// Copy VoodooHDA prefpanes
			if ($name == "AudioSettings")
			{
        		$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp -R $workpath/kpsvn/$categ/$fname/VoodooHdaSettingsLoader.app /Applications/; cp $workpath/kpsvn/$categ/$fname/com.restore.voodooHDASettings.plist /Library/LaunchAgents/; cp -R $workpath/kpsvn/$categ/$fname/VoodooHDA.prefPane /Library/PreferencePanes/; echo \"Copy : VoodooHDA file(s) installed<br>\" >> $workpath/build.log";
			}
			
			switch ($fname) {
				// Copy VoodooPState launch agent plist
				case "VoodooPState":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp $workpath/kpsvn/PowerMgmt/VoodooPState/PStateMenu.plist /Library/LaunchAgents/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";
				break;
				
				// Copy VoodooPS2 prefpanes
				case "StandardVooDooPS2":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp -R $workpath/kpsvn/$categ/$fname/VoodooPS2.prefpane /Library/PreferencePanes; echo \"Copy : $fname installed to /Library/PreferencePanes<br>\" >> $workpath/build.log";
				break;
				
				case "LatestVoodooPS2":				
				case "VoooDooALPS2":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/*.kext $ee/; cp $workpath/kpsvn/$categ/$fname/VoodooPS2Daemon /usr/bin; cp $workpath/kpsvn/$categ/$fname/org.rehabman.voodoo.driver.Daemon.plist /Library/LaunchDaemons; cp -R $workpath/kpsvn/$categ/$fname/VoodooPS2synapticsPane.prefPane /Library/PreferencePanes; echo \"Copy : $fname file(s) installed<br>\" >> $workpath/build.log";
				break;
				
				default:
				break;
			}
			
			// change to correct bootloader, ethernet and power mgmt kexts folder path
			switch ($categ) {
		
				case "Ethernet":
				$categdir = "$workpath/kpsvn/$categ/$fname"; // Ethernet/RealTek/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$fname/$name";	
				// Copy kext inside the name folder for new RTL kext 
				if ($name == "NewRTL81xx" || $name == "NewRTL81xx_Lion") {
					$copyKextCmd = "cp -a $workpath/kpsvn/$categ/$fname/$name/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";
				}			
				break;
				
				case "PowerMgmt":
				$copyKextCmd = "cp -a $workpath/kpsvn/$categ/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $workpath/build.log";
				$categdir = "$workpath/kpsvn/$categ"; // PowerMgmt/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$name";
				break;
				
				case "Bootloader":
				$copyKextCmd = "cp -f $workpath/kpsvn/$categ/$fname/$name /; echo \"Copy : $fname bootloader installed/updated<br>\" >> $workpath/build.log";
				$categdir = "$workpath/kpsvn/$categ";
				$packdir = "$categdir/$fname";
				$svnpath = "kextpacks/$categ/$fname";
				break;
			}
		}	
			
		if (is_dir("$packdir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $packdir; then echo \"Update : $name file(s) finished<br>\" >> $workpath/build.log; $copyKextCmd; fi";

			$edp->writeToLog("$workpath/kpsvn/dload/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			system_call("sh $workpath/kpsvn/dload/$fname.sh >> $workpath/build.log &");
			
			// system_call("svn --non-interactive --username edp --password edp --quiet --force update $packdir");
		}
		else {
			$checkoutCmd = "mkdir $categdir; cd $categdir; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"Download : $name file(s) finished<br>\" >> $workpath/build.log; $copyKextCmd; fi";

			$edp->writeToLog("$workpath/kpsvn/dload/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile; ");
			system_call("sh $workpath/kpsvn/dload/$fname.sh >> $workpath/build.log &");
			
			// system_call("mkdir $packdir; cd $packdir; svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$pname/ .");
		}
	} 
}

$kexts = new kexts();

?> 
