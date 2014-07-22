<?php

class svnDownload {

	/*
	 * public functions to download essential and custom files from model folder
	 */
	public function loadModelEssentialFiles() {
		global $workpath, $modelNamePath, $os;
	
		//
		// download essential files from common folder
		//
		$modelfolder = "$workpath/model-data/$modelNamePath/common";
		if (is_dir("$modelfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
		} else {
			system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/common .");
		}
	
		//
		// download essential files from $os folder
		//
		$modelfolder = "$workpath/model-data/$modelNamePath/$os";
		if (is_dir("$modelfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
		} else {
			system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/$os .");
		}
	}
	
	public function svnModeldata($model) {
		global $workpath, $os;
		
		//
		// download essential files from common folder
		//
		$modelfolder = "$workpath/model-data/$model/common";
		if (is_dir("$modelfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
		} else {
			system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$model/common .");
		}
	
		//
		// download essential files from common $os
		//
		$modelfolder = "$workpath/model-data/$model/$os";
		if (is_dir("$modelfolder")) {
			system_call("svn --non-interactive --username edp --password edp --force --quiet update $modelfolder");
		} else {
			system_call("mkdir $modelfolder; cd $modelfolder; svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$model/$os .");
		}
	}

	public function checkSVNrevs() {
		global $localrev, $workpath;

		$remoterev = exec("cd $workpath; svn info -r HEAD --username edp --password edp --non-interactive | grep -i \"Last Changed Rev\"");
		$remoterev = str_replace("Last Changed Rev: ", "", $remoterev);

		if ($localrev < $remoterev) {
			echo "\n   ---------------------------------------------------------------------------------------\n";
			echo "        !!! There is an update of EDP, please run option 2 to download the update !!!\n";
			echo "   ---------------------------------------------------------------------------------------\n\n";
		}
	}
	
	/*
 	 * This function will download a kextpack from SVN if requested (or update it if allready exists) 
 	 */
	public function kextpackLoader($categ, $fname, $name) {
		global $workpath, $edp, $ee;
		
    	$buildLogPath = "$workpath/logs/build";
    	
    	if(!is_dir("$buildLogPath/dLoadScripts/"))
    		system_call("mkdir $buildLogPath/dLoadScripts");
    		
    	if(!is_dir("$buildLogPath/dLoadStatus/"))
			$createStatFile = "mkdir $buildLogPath/dLoadStatus; cd $buildLogPath/dLoadStatus; touch $fname.txt";
    	else
    		$createStatFile = "cd $buildLogPath/dLoadStatus; touch $fname.txt";	

		$endStatFile = "cd $buildLogPath/dLoadStatus; rm -rf $fname.txt";
		
		//
		// Download custom Kexts, kernel and AppleHDA from model data
		//
    	if ($categ == "Extensions"  || $categ == "Kernel")
		{
			$categdir = "$workpath/model-data/$name";
			$packdir = "$categdir/$fname";
			$svnpath = "model-data/$name/$fname";
			$copyKextCmd = "cp -a $workpath/model-data/$name/$fname/*.kext $ee/; echo \"Copy : $fname file(s) installed<br>\" >> $buildLogPath/build.log";
			$name = $fname;
		}
		//
		// Download kexts and booloader from kextpacks
		//
		else {
			$categdir = "$workpath/kextPacks/$categ";
			$packdir = "$categdir/$fname";
			$svnpath = "kextpacks/$categ/$fname";
			$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $buildLogPath/build.log";


			// Copy VoodooHDA prefpanes
			if ($name == "AudioSettings")
			{
        		$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/*.kext $ee/; cp -R $workpath/kextPacks/$categ/$fname/VoodooHdaSettingsLoader.app /Applications/; cp $workpath/kextPacks/$categ/$fname/com.restore.voodooHDASettings.plist /Library/LaunchAgents/; cp -R $workpath/kextPacks/$categ/$fname/VoodooHDA.prefPane /Library/PreferencePanes/; echo \"Copy : VoodooHDA file(s) installed<br>\" >> $buildLogPath/build.log";
			}
			
			switch ($fname) {
				// Copy VoodooPState launch agent plist
				case "VoodooPState":
				$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/*.kext $ee/; cp $workpath/kextPacks/PowerMgmt/VoodooPState/PStateMenu.plist /Library/LaunchAgents/; echo \"Copy : $name file(s) installed<br>\" >> $buildLogPath/build.log";
				break;
				
				// Copy VoodooPS2 prefpanes
				case "StandardVooDooPS2":
				$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/*.kext $ee/; cp -R $workpath/kextPacks/$categ/$fname/VoodooPS2.prefpane /Library/PreferencePanes; echo \"Copy : $fname installed to /Library/PreferencePanes<br>\" >> $buildLogPath/build.log";
				break;
				
				case "LatestVoodooPS2":				
				case "VoooDooALPS2":
				$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/*.kext $ee/; cp $workpath/kextPacks/$categ/$fname/VoodooPS2Daemon /usr/bin; cp $workpath/kextPacks/$categ/$fname/org.rehabman.voodoo.driver.Daemon.plist /Library/LaunchDaemons; cp -R $workpath/kextPacks/$categ/$fname/VoodooPS2synapticsPane.prefPane /Library/PreferencePanes; echo \"Copy : $fname file(s) installed<br>\" >> $buildLogPath/build.log";
				break;
				
				default:
				break;
			}
			
			// change to correct bootloader, ethernet and power mgmt kexts folder path
			switch ($categ) {
		
				case "Ethernet":
				$categdir = "$workpath/kextPacks/$categ/$fname"; // Ethernet/RealTek/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$fname/$name";	
				// Copy kext inside the name folder for new RTL kext 
				if ($name == "NewRTL81xx" || $name == "NewRTL81xx_Lion") {
					$copyKextCmd = "cp -a $workpath/kextPacks/$categ/$fname/$name/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $buildLogPath/build.log";
				}			
				break;
				
				case "PowerMgmt":
				$copyKextCmd = "cp -a $workpath/kextPacks/$categ/*.kext $ee/; echo \"Copy : $name file(s) installed<br>\" >> $buildLogPath/build.log";
				$categdir = "$workpath/kextPacks/$categ"; // PowerMgmt/kextname.kext
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$name";
				break;
				
				case "Bootloader":
				$copyKextCmd = "cp -f $workpath/kextPacks/$categ/$fname/$name/boot /; echo \"Copy : $name $fname bootloader installed/updated<br>\" >> $buildLogPath/build.log";
				if (!is_dir("$workpath/kextPacks/$categ")) {
					system_call("mkdir $workpath/kextPacks/$categ");
				}
				$categdir = "$workpath/kextPacks/$categ/$fname";
				$packdir = "$categdir/$name";
				$svnpath = "kextpacks/$categ/$fname/$name";
				break;
			}
		}	
			
		if (is_dir("$packdir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $packdir; then echo \"Update : $name file(s) finished<br>\" >> $buildLogPath/build.log; $copyKextCmd; fi";

			writeToLog("$buildLogPath/dLoadScripts/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			system_call("sh $buildLogPath/dLoadScripts/$fname.sh >> $buildLogPath/build.log &");
			
			// system_call("svn --non-interactive --username edp --password edp --quiet --force update $packdir");
		}
		else {
			$checkoutCmd = "mkdir $categdir; cd $categdir; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"Download : $name file(s) finished<br>\" >> $buildLogPath/build.log; $copyKextCmd; fi";

			writeToLog("$buildLogPath/dLoadScripts/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile; ");
			system_call("sh $buildLogPath/dLoadScripts/$fname.sh >> $buildLogPath/build.log &");
			
			// system_call("mkdir $packdir; cd $packdir; svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/kextpacks/$pname/ .");
		}
	} 

}

$svnLoad = new svnDownload();

?> 
