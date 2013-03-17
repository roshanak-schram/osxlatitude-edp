<?php
	include_once "../functions.inc.php";
	include_once "../config.inc.php";
	
	include "header.inc.php";

		
	//Get server vars
	global $modelID;
	$vendor 	= $_GET['vendor'];	if ($vendor == "") 	{ $vendor 	= $_POST['vendor']; }
	$serie 		= $_GET['serie'];	if ($serie == "") 	{ $serie 	= $_POST['serie']; }
	$modelID 	= $_GET['model'];	if ($modelID == "") { $modelID 	= $_POST['model']; }
	$action 	= $_GET['action']; 	if ($action == "") 	{ $action 	= $_POST['action']; }
	


	//-------------------------> Do build page starts here
	if ($action == 'dobuild') {
	
		echo "<span class='console'>";
		//header("Content-Type: text/event-stream\n\n");
		//while (ob_get_level()) ob_end_clean();
		
		
		if ($modelID == "") { echo "modelID is empty"; exit; }
		//Filter the data from the confirmation page 
		$sleepEnabler 		= $_POST['sleepEnabler']; 		if ($sleepEnabler == "on") 		{ $sleepEnabler = "yes"; } 			else { $sleepEnabler = "no"; }
		$nullcpu 			= $_POST['nullcpu']; 			if ($nullcpu == "on") 			{ $nullcpu = "yes"; } 				else { $nullcpu = "no"; } 
		$emulatedST 		= $_POST['emulatedST']; 		if ($emulatedST == "on") 		{ $emulatedST = "yes"; } 			else { $emulatedST = "no"; }
		$tscsync 			= $_POST['tscsync']; 			if ($tscsync == "on") 			{ $tscsync = "yes"; } 				else { $tscsync = "no"; }
		$loadIOATAFamily	= $_POST['loadIOATAFamily']; 	if ($loadIOATAFamily == "on") 	{ $loadIOATAFamily = "yes"; } 		else { $loadIOATAFamily = "no"; }
		$loadNatit			= $_POST['loadNatit']; 			if ($loadNatit == "on") 		{ $loadNatit = "yes"; } 			else { $loadNatit = "no"; }
		$useACPIfix			= $_POST['useACPIfix']; 		if ($useACPIfix == "on") 		{ $useACPIfix = "yes"; } 			else { $useACPIfix = "no"; }
		$patchCPU			= $_POST['patchCPU']; 			if ($patchCPU == "on") 			{ $patchCPU = "yes"; } 				else { $patchCPU = "no"; }
		$useGMA950brightfix	= $_POST['useGMA950brightfix']; if ($useGMA950brightfix == "on"){ $useGMA950brightfix = "yes"; } 	else { $useGMA950brightfix = "no"; }
		$customCham			= $_POST['customCham']; 		if ($customCham == "on")		{ $customCham = "yes"; } 			else { $customCham= "no"; }
		$customKernel		= $_POST['customKernel']; 		if ($customKernel == "on")		{ $customKernel = "yes"; } 			else { $customKernel = "no"; }
		$patchAHCIml		= $_POST['patchAHCIml']; 		if ($patchAHCIml == "on")		{ $patchAHCIml = "yes"; } 			else { $patchAHCIml = "no"; }
		
		//Generate a multi dim. array used during the build process
		global $modeldb;
		$modeldb = array(
					//This one have to be empty, its used for when we do custom builds...
					array( 	name 			=> $_POST['name'], 
                      		desc 			=> $_POST['desc'],
                      		nullcpu 		=> $nullcpu,
                      		sleepEnabler 	=> $sleepEnabler,                      		
                      		ps2pack 		=> $_POST['ps2pack'],
                      		emulatedST 		=> $emulatedST,                      		
                      		tscsync 		=> $tscsync,                      		
                      		batteryKext		=> $_POST['batteryKext'],
                      		loadIOATAFamily	=> $loadIOATAFamily,
                      		loadNatit		=> $loadNatit,
                      		useACPIfix		=> $useACPIfix,
                      		patchCPU		=> $patchCPU,
                      		patchAHCIml		=> $patchAHCIml,
                      		useGMA950brightfix	=> $useGMA950brightfix,
                      		ethernet		=> $_POST['ethernet'],
                      		wifikext		=> $_POST['wifikext'],
                      		audiopack		=> $_POST['audiopack'],                    		                      		                      		
                      		customCham 		=> $customCham,                      		
                      		customKernel 	=> $customKernel                     		 
                    ),
             );
        
        global $modelID; $modelID = "0";
        global $modelName;
        $modelName = $modeldb[$modelID]["name"];
 
        EDPdoBuild();	
	}



	function EDPdoBuild() {
		global $modeldb; global $modelID; global $workpath; global $rootpath;

		//Start by defining our log file and cleaning it..
		$log = "$workpath/build.log";
		if (is_file("$log")) { system_call("rm -Rf $log"); system_call("<br>echo Building....<br><br> >$log"); }
		
		//Check if myhack is up2date and ready for combat
			myHackCheck();
			
			//Step 1
			writeToLog("$workpath/build.log", "<br><b>Step 1) Download/Update local model data... </b><br>");
				$modelName = $modeldb[$modelID]["name"];
				svnModeldata("$modelName");

			//Step 2
			writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files to $workpath </b><br>");
				copyEssentials();

			//Step 3
			writeToLog("$workpath/build.log", "<br><b>Step 3) Preparing kexts for myHack.kext </b><br>");
				copyKexts();
			
			//Step 4
			writeToLog("$workpath/build.log", "<br><br><b>Step 4) Updating chameleon.. </b><br>");
				updateCham();
			
			writeToLog("$workpath/build.log", "<br><b>Step 5) Applying last minut fixes...</b><br>");
				lastMinFixes();
					
			//Step 5
			writeToLog("$workpath/build.log", "<br><b>Step 6) Calling myFix to copy kexts and generate kernelcache</b><br><pre>");
				system_call("stty -tostop; sudo myfix -q -t / >>$workpath/build.log 2>&1 &");
				
			echo "<script> document.location.href = 'workerapp.php?action=showBuildLog'; </script>";

			exit;
        		
	}








		    

			
//-------------------------> Here starts the Vendor and model selector - but only if $action is empty
				
if ($action == "") {
	
	//Write out the top menu
	echo "<div class='pageitem_top'><img src='icons/installer.png'><span>Select a model your wish to configure for:</span></div>\n";
	echo "<div class='pageitem_bottom'>\n";
	echo "EDP's internal database contains 'best practice' schematics for 50+ systems - this makes it easy for to to choose the right configuration - however - you allways have the option to ajust the schematics before doing a build. <br>Doing a build means that EDP will copy a combination of kexts, dsdt, plists needed to boot your system.";
	
    		$result = $edp_db->query("SELECT * FROM models order by vendor");
			include "header.inc.php";
			include "include/watermark.inc.php";
			echo "<p><span class='graytitle'></span><ul class='pageitem'><li class='select'>";
			//Show the vendrop dropdown
			echo "<select name='vendor' id='vendor' onchange='showSerie();'>";
			if ($vendor == "") { echo "<option value='' selected>&nbsp;&nbsp;Select vendor...</option>\n"; } else { echo "<option value='' selected>&nbsp;&nbsp;Select vendor and type...</option>\n"; }
				foreach($result as $row) {
					if ($row[vendor] != "$last") {
						$s = ""; if ($vendor == "$row[vendor]") { $s = "selected"; }
						echo "<option value='$row[vendor]' $s>&nbsp;&nbsp;$row[vendor]</option>\n";
					}
					$last = "$row[vendor]";
				}				
				echo "</select><span class='arrow'></span> </li>";

				
				if ($vendor != "") {
					$result = $edp_db->query("SELECT * FROM models where vendor = '$vendor' order by serie");
					echo "<li class='select'><td><select id='serie' name='serie' onchange='showType();'>";
					echo "<option value='' selected>&nbsp;&nbsp;Select serie...</option>\n";
					foreach($result as $row) {
						if ($row[serie] != "$last") { 
							$s = ""; if ($row[serie] == "$serie") { $s = "SELECTED"; }
							echo "<option value='$row[serie]' $s>&nbsp;&nbsp;$row[vendor] $row[serie]</option>\n";
						}
						$last = $row[serie];
					}						
					echo "</select><span class='arrow'></span> </li>";					
										

				
					//Show the model dropdown, but only if $vendor and $serie is set
					if ($vendor != "" && $serie != "") {
						$result = $edp_db->query("SELECT * FROM models where vendor = '$vendor' and serie = '$serie' order by type");
						echo "<li class='select'><td><select id='model' name='model'>";
						echo "<option value='' selected>&nbsp;&nbsp;Select model...</option>\n";
					
						foreach($result as $row) {
							echo "<option value='$row[id]'>&nbsp;&nbsp;$row[desc] ($row[type])</option>\n";
							}						
							echo "</select><span class='arrow'></span> </li>";
							echo "<li class='button'><input name='OK' type='button' value='Continue...' onclick='doConfirm();' /></li></ul></p>";
						}

				}				
				
	}
	
	//<-------------------- Here stops the vendor and model selector		









	//--------------------> Here starts the build confirmation page 
	//Check if $action was set via GET or POST - if it is set, we asume that we are going to confirm the build
	if ($action == "confirm") {
		$stmt = $edp_db->query("SELECT * FROM models where id = '$modelID'");
		$stmt->execute();
		$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];
		
		
		//Show the header
		echo "<form action='module.configuration.predefined.php' method='post'>";
		echo "<br><div align='center'><table border=0 width='95%' cellpadding=0 style='border-collapse: collapse'>\n";
		echo "<tr><td rowspan='4' width='1%'><img src='http://www-dev.osxlatitude.com/wp-content/themes/osxlatitude/img/edp/modelpics/$mdrow[name].png'></td></tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;&nbsp;<b>$mdrow[desc]</b></td>\n";
		echo "<td width='40%'>&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;&nbsp;$os_string ($os)</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>&nbsp;</td>\n";
		echo "</tr>\n";
		echo "</table></div>\n";

		
		echo "<br><br>";



		echo "<span class='graytitle'>Kernel Extentions (kexts / drivers)</span>\n";
		echo "<ul class='pageitem'>";
				
		//Show dropdown for PS2 kexts
			$result = $edp_db->query("SELECT * FROM ps2");
			echo "<li class='select'><select name='ps2pack'>";
			if ("$mdrow[ps2pack]" == "" || "$mdrow[ps2pack]" == "no") { echo "<option value='no' SELECTED>&nbsp; PS2 kext: None selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; PS2 kext: None selected</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[ps2pack]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; PS2 kext: $row[name] ($row[notes])</option>\n";
		    }
			echo "</select><span class='arrow'></span> </li>";
			
			
			
		//Show dropdown for Audio kexts
			$result = $edp_db->query("SELECT * FROM audio");
			echo "<li class='select'>";
			//Check if the field bundledAudio is set to yes, if so we will disable the dropdown field
			if ($mdrow[bundledAudio] == "yes") { 
				echo "<select name='audiopack' DISABLED SELECTED>\n";
				echo "<option value='no'>&nbsp; Audio: Comes with patched AppleHDA...</option>\n";
			}
			if ($mdrow[bundledAudio] == "" || $mdrow[bundledAudio] == "no") {	
				echo "<select name='audiopack'>\n";
				if ("$mdrow[audiopack]" == "" || "$mdrow[audiopack]" == "no") { echo "<option value='no' SELECTED>&nbsp; Audio kext: Not selected</option>\n"; }
				else { echo "<option value='no'>&nbsp; Audio kext: Don't load</option>\n"; }
				foreach($result as $row) {
					$s=""; if ("$mdrow[audiopack]" == "$row[id]") { $s = "SELECTED"; }
					echo "<option value='$row[id]' $s>&nbsp; Audio kext: $row[name] ($row[arch]) - $row[notes]</option>\n";
				}
			}
			echo "</select><span class='arrow'></span> </li>\n";



		//Show dropdown for Ethernet (lan) Kexts
			$result = $edp_db->query("SELECT * FROM ethernet");
			echo "<li class='select'><select name='ethernet'>\n";
			if ("$mdrow[ethernet]" == "" || "$mdrow[ethernet]" == "no") { echo "<option value='no' SELECTED>&nbsp; Ethernet kext: Not selected</option>"; }
			else { echo "<option value='no'>&nbsp; Ethernet kext: Don't load</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[ethernet]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Ethernet kext: $row[name] ($row[arch]) - $row[notes]</option>\n";
		    }			
			echo "</select><span class='arrow'></span> </li>\n";
			

			
		//Show dropdown for Wifi Kexts
			$result = $edp_db->query("SELECT * FROM wifi");
			echo "<li class='select'><select name='wifikext'>\n";
			if ("$mdrow[wifikext]" == "" || "$mdrow[wifikext]" == "no") { echo "<option value='no' SELECTED>&nbsp; Wifi kext: Not selected</option>\n"; }			
			else { echo "<option value='no'>&nbsp; Wifi kext: Don't load</option>\n"; }
			foreach($result as $row) {
				$s=""; if ("$mdrow[wifikext]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Wifi kext: $row[name] ($row[arch]) - $row[notes]</option>\n";
		    }
			echo "</select><span class='arrow'></span> </li>\n";
			

									
		//Show dropdown for Battery kexts
			$result = $edp_db->query("SELECT * FROM battery");
			echo "<li class='select'><select name='batteryKext'>\n";
			if ("$mdrow[batteryKext]" == "" || "$mdrow[batteryKext]" == "no") { echo "<option value='no' SELECTED>&nbsp; Battery kext: Not selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; Battery kext: Don't load</option>\n"; }	
			foreach($result as $row) {
				$s=""; if ("$mdrow[batteryKext]" == "$row[id]") { $s = "SELECTED"; }
				echo "<option value='$row[id]' $s>&nbsp; Battery kext: $row[name] ($row[arch])</option>\n";
		    }			
			echo "</select><span class='arrow'></span> </li>\n";
			
			
			
			checkbox("Use patched IOATAFamily.kext:", "loadIOATAFamily", "$mdrow[loadIOATAFamily]");
			checkbox("Load natit.kext:", "loadNatit", "$mdrow[loadNatit]");						
			
		echo "</ul><br>";
		




		echo "<span class='graytitle'>CPU & Power options</span>";
		echo "<ul class='pageitem'>";
			checkbox("Patch AppleIntelCPUPowerManagement.kext:", "patchCPU", "$mdrow[patchCPU]");
			checkbox("Emulated speedstep:", "emulatedST", "$mdrow[emulatedST]");
			checkbox("Install VoodooTSCsync:", "tscsync", "$mdrow[tscsync]");
			checkbox("Install NullCPUPowerManagement:", "nullcpu", "$mdrow[nullcpu]");	
			checkbox("Install Sleepenabler:", "sleepEnabler", "$mdrow[sleepEnabler]");		
		echo "</ul><br>";


		echo "<span class='graytitle'>Chameleon</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Update Chameleon to latest version:", "updateCham", "yes");	
			checkbox("Use custom chameleon:", "customCham", "$mdrow[customCham]");
		echo "</ul><br>";
		
		echo "<span class='graytitle'>Fixes</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Use custom kernel:", "customKernel", "$mdrow[customKernel]");	
			checkbox("GMA950 Brightness fix:", "useGMA950brightfix", "$mdrow[useGMA950brightfix]");
			checkbox("Use ACPI fix (coolbook fix):", "useACPIfix", "$mdrow[useACPIfix]");
			checkbox("Use AHCI fix (Fix waiting for root device):", "patchAHCIml", "$mdrow[patchAHCIml]");
		echo "</ul><br>";
		
		
		//Send standard vars
		echo "<input type='hidden' name='action' value='dobuild'>";
		echo "<input type='hidden' name='name' value='$mdrow[name]'>";
		echo "<input type='hidden' name='desc' value='$mdrow[desc]'>";
		echo "<input type='hidden' name='model' value='$modelID'>";
		
		
		echo "<center><input type='submit' value='Do build!'><br><br>";
		echo "</form>";		
		

		exit;
	}



	
	
	//<------------------------------ Here ends the model confirmation page

?>


		
		
			

			
			
			
		






<script>
	function doConfirm() {
		var vendor = '<?php echo "$vendor";?>';
		var a = document.getElementById("model");
		var model = a.options[a.selectedIndex].value;
		if (model == "") { alert('Please select a model before continueing..'); return; }
		document.location.href = 'module.configuration.predefined.php?vendor='+vendor+'&model='+model+'&action=confirm';		
	}
	function showType() {
		var a = document.getElementById("vendor");
		var vendor = a.options[a.selectedIndex].value;	
		var b = document.getElementById("serie");
		var serie = b.options[b.selectedIndex].value;
		document.location.href = 'module.configuration.predefined.php?vendor='+vendor+'&serie='+serie+'';
	}
	function showSerie() {
		var a = document.getElementById("vendor");
		var vendor = a.options[a.selectedIndex].value;	
		document.location.href = 'module.configuration.predefined.php?vendor='+vendor+'';			
	}
</script>





</body>

</html>
