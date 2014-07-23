<?php

class svnDownload {

	/*
	 * Public function to prepare the essential files download
	 */
	public function PrepareEssentialFilesDownload($model) {
		global $workpath, $modelNamePath, $os;
	
		if ($model != "") {
			$modelNamePath = $model;
		}
		
		$buildLogPath = "$workpath/logs/build";
  
    	$createStatFile = "touch $buildLogPath/dLoadStatus/essentialFiles.txt";	
		$endStatFile = "rm -f $buildLogPath/dLoadStatus/essentialFiles.txt";
		
		//
		// download essential files from common folder
		//
		$modelfolder = "$workpath/model-data/$modelNamePath/common";
		
		if (is_dir("$modelfolder")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --force --quiet update $modelfolder; then echo \"Update : Common Essential files update finished<br>\" >> $buildLogPath/build.log; else echo \"Update : Common Essential files update failed<br>\" >> $buildLogPath/build.log; fi";
		} else {
			$checkoutCmd = "mkdir $modelfolder; cd $modelfolder; if svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/common .; then echo \"Download : Common Essential files download finished<br>\" >> $buildLogPath/build.log; else echo \"Download : Common Essential files download failed<br>\" >> $buildLogPath/build.log; fi";
		}
	
		writeToLog("$buildLogPath/dLoadScripts/essentialFilesCommon.sh", "$createStatFile; $checkoutCmd; $endStatFile;");

		//
		// download essential files from $os folder
		//
		
		$modelfolder = "$workpath/model-data/$modelNamePath/$os";
		
		if (is_dir("$modelfolder")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --force --quiet update $modelfolder; then echo \"Update : $os Essential files update finished<br>\" >> $buildLogPath/build.log; else echo \"Update : $os Essential files update failed<br>\" >> $buildLogPath/build.log; fi";
		} else {
			$checkoutCmd = "mkdir $modelfolder; cd $modelfolder; if svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/$os .; then echo \"Download : $os Essential files download finished<br>\" >> $buildLogPath/build.log; else echo \"Download : $os Essential files download failed<br>\" >> $buildLogPath/build.log; fi";
		}
		
		writeToLog("$buildLogPath/dLoadScripts/essentialFiles$os.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
		
	}
	
	/*
	 * Public function to prepare the cpu ssdt files download
	 */
	public function PrepareSSDTFilesDownload($cpuModel) {
		global $workpath, $modelNamePath;
		
		$buildLogPath = "$workpath/logs/build";
    
    	$createStatFile = "touch $buildLogPath/dLoadStatus/essentialFiles.txt";	
		$endStatFile = "rm -f $buildLogPath/dLoadStatus/essentialFiles.txt";
		
		$modelcpudir = "$workpath/model-data/$modelNamePath/cpu";
		
		if (is_dir("$modelcpudir")) {
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --force --quiet update $modelcpudir; then echo \"Update : $cpuModel SSDT files update finished<br>\" >> $buildLogPath/build.log; else echo \"Update : $cpuModel SSDT files update failed<br>\" >> $buildLogPath/build.log; fi";
		} else {
			$checkoutCmd = "mkdir $modelcpudir; cd $modelcpudir; if svn --non-interactive --username osxlatitude-edp-read-only --force --quiet co http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/cpu/$cpuModel .; then echo \"Download : $cpuModel SSDT files download finished<br>\" >> $buildLogPath/build.log; else echo \"Download : $cpuModel SSDT files download failed<br>\" >> $buildLogPath/build.log; fi";
		}
		
		writeToLog("$buildLogPath/dLoadScripts/SSDT_$cpuModel.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
	
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
 	 * This function will prepare the download of kextpacks from SVN if requested (or update it if exists) 
 	 */
	public function PrepareKextpackDownload($categ, $fname, $name) {
		global $workpath, $edp, $ee;
		
    	$buildLogPath = "$workpath/logs/build";
    		
    	$createStatFile = "touch $buildLogPath/dLoadStatus/$fname.txt";	
		$endStatFile = "rm -f $buildLogPath/dLoadStatus/$fname.txt";
		
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
				$copyKextCmd = "cp -f $workpath/kextPacks/$categ/$fname/$name/boot /; echo \"Copy : $name $fname bootloader updated<br>\" >> $buildLogPath/build.log";
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
			$checkoutCmd = "if svn --non-interactive --username edp --password edp --quiet --force update $packdir; then echo \"Update : $name file(s) finished<br>\" >> $buildLogPath/build.log; $copyKextCmd; else echo \"Update : $name file(s) update failed<br>\" >> $buildLogPath/build.log; fi";

			writeToLog("$buildLogPath/dLoadScripts/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile;");
			// system_call("sh $buildLogPath/dLoadScripts/$fname.sh >> $buildLogPath/build.log &");
		}
		else {
			$checkoutCmd = "mkdir $categdir; cd $categdir; if svn --non-interactive --username osxlatitude-edp-read-only --quiet --force co http://osxlatitude-edp.googlecode.com/svn/$svnpath; then echo \"Download : $name file(s) finished<br>\" >> $buildLogPath/build.log; $copyKextCmd; else echo \"Download : $name file(s) download failed<br>\" >> $buildLogPath/build.log; fi";

			writeToLog("$buildLogPath/dLoadScripts/$fname.sh", "$createStatFile; $checkoutCmd; $endStatFile; ");
			// system_call("sh $buildLogPath/dLoadScripts/$fname.sh >> $buildLogPath/build.log &");
		}
	} 

}

$svnLoad = new svnDownload();

?> 
