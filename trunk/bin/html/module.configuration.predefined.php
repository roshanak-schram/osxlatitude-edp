<?php
	include_once "../functions.inc.php";
	include_once "../vendordb.inc.php";
	include_once "../config.inc.php";
	
	//Get server vars
	global $modelID;
	$vendor 	= $_GET['vendor'];	if ($vendor == "") 	{ $vendor 	= $_POST['vendor']; }
	$modelID 	= $_GET['model'];	if ($modelID == "") { $modelID 	= $_POST['model']; }
	$action 	= $_GET['action']; 	if ($action == "") 	{ $action 	= $_POST['action']; }
	
	//Include all model data based on what $vendor is set to
	if ($vendor == "0")  { include "../modelinfo.acer.laptop.inc.php"; }
	if ($vendor == "1")  { include "../modelinfo.acer.desktop.inc.php"; }	
	if ($vendor == "2")  { include "../modelinfo.asus.laptop.inc.php"; }
	if ($vendor == "3")  { include "../modelinfo.asus.desktop.inc.php"; }		
	if ($vendor == "4")  { include "../modelinfo.dell.laptop.inc.php"; }
	if ($vendor == "5")  { include "../modelinfo.dell.desktop.inc.php"; }
	if ($vendor == "6")  { include "../modelinfo.hp.laptop.inc.php"; }
	if ($vendor == "7")  { include "../modelinfo.hp.desktop.inc.php"; }			
	if ($vendor == "8")  { include "../modelinfo.lg.laptop.inc.php"; }
	if ($vendor == "9")  { include "../modelinfo.lg.desktop.inc.php"; }
	if ($vendor == "10") { include "../modelinfo.packardbell.desktop.inc.php"; }		
	if ($vendor == "11") { include "../modelinfo.samsung.laptop.inc.php"; }			
	if ($vendor == "12") { include "../modelinfo.samsung.desktop.inc.php"; }
	if ($vendor == "13") { include "../modelinfo.shuttle.laptop.inc.php"; }			
	if ($vendor == "14") { include "../modelinfo.shuttle.desktop.inc.php"; }
	if ($vendor == "15") { include "../modelinfo.intel.desktop.inc.php"; }
	
	include_once "../ps2db.inc.php";
	include_once "../audiodb.inc.php";
	include_once "../batterydb.inc.php";
	include_once "../landb.inc.php";
	include_once "../wifidb.inc.php";
	
	

?>








<?php


	//-------------------------> Do build page starts here
	if ($action == 'dobuild') {
		header("Content-Type: text/event-stream\n\n");
		while (ob_get_level()) ob_end_clean();
		
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
        echo "-----> DONE <-----";
        exit;	
	}



	function EDPdoBuild() {
		global $modeldb; global $modelID; global $workpath; global $rootpath;
		
		myHackCheck(); 
        echo "Step 1) Download/Update local model data... \n";
        	flush();
			$modelName = $modeldb[$modelID]["name"];
			svnModeldata("$modelName");
			
		echo "Step 2) Copying Essential files to $workpath \n";
			flush();
			copyEssentials();
			
		echo "Step 3) Preparing kexts for myHack.kext \n";
			flush();
			copyKexts();
  			
  		
		echo "Step 4) Call myFix to do the new build... \n";
			flush();
			system_call("myfix -q -t /");

		echo "Step 5) Doing sanity check... \n";
			kernelcachefix();
			updateCham();
					
		
	}







			
	//-------------------------> Here starts the Vendor and model selector - but only if $action is empty
				
	if ($action == "") {
				include "header.inc.php";
				include "include/watermark.inc.php";
				echo "<br><br><br><br><br>";
				echo "<span class='graytitle'>Select a model your wish to configure for:</span><ul class='pageitem'><li class='select'>";
				//Show the vendrop dropdown
				echo "<select name='vendor' id='vendor' onchange='showType();'>";
				$id = "0";
				if ($vendor == "") { echo "<option value='' selected>&nbsp;&nbsp;Select vendor and type...</option>\n"; }
				while ($vendordb[$id] != ""){
					$name = $vendordb[$id]["name"];		
					if ($vendor == "$id") { echo "<option value='$id' selected>&nbsp;&nbsp;$name</option>\n"; } else { echo "<option value='$id'>&nbsp;&nbsp;$name</option>\n"; }
					$id++;
				}
				
				echo "</select><span class='arrow'></span> </li>";


				//Show the model dropdown, but only if $vendor is set
				if ($vendor != "") {
					echo "<li class='select'><td><select id='model' name='model' style='width:160px;'>";
					if ($modelID == "") { echo "<option value='' selected>&nbsp;&nbsp;Select model...</option>\n"; }
					$id = 1;
					while ($modeldb[$id] != ""){
						$desc = $modeldb[$id]["desc"];		
						echo "<option value='$id'>&nbsp;&nbsp;$desc</option>\n";
						$id++;
					}
					echo "</select><span class='arrow'></span> </li>";
				}
				
				echo "<li class='button'><input name='OK' type='button' value='Continue...' onclick='doConfirm();' /></li></ul>";
	}
	
	//<-------------------- Here stops the vendor and model selector		









	//--------------------> Here starts the build confirmation page 
	//Check if $action was set via GET or POST - if it is set, we asume that we are going to confirm the build
	if ($action == "confirm") {
		include "header.inc.php";
		include "include/watermark.inc.php";
		$name 		= $modeldb[$modelID]["name"];
		$desc 		= $modeldb[$modelID]["desc"];
		
		//Show the header
		echo "<form action='module.configuration.predefined.php' method='post'>";
		echo "<br><div align='center'><table border=0 width='95%' cellpadding=0 style='border-collapse: collapse'>\n";
		echo "<tr><td rowspan='4' width='1%'><img src='http://www-dev.osxlatitude.com/wp-content/themes/osxlatitude/img/edp/modelpics/$name.png'></td></tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;&nbsp;<b>$desc</b></td>\n";
		echo "<td width='40%'>ROOT: $rootpath</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;&nbsp;$os_string ($os)</td>\n";
		echo "<td>EDP Path: $workpath</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo "<td>&nbsp;</td>\n";
		echo "<td>SLE path: $slepath</td>\n";
		echo "</tr>\n";
		echo "</table></div>\n";

		
		echo "<br><br>";



		echo "<span class='graytitle'>Kernel Extentions (kexts / Drivers)</span>\n";
		echo "<ul class='pageitem'>";
				
		//Show dropdown for PS2 kexts
			echo "<li class='select'><select name='ps2pack'>";
			if ($modeldb[$modelID]["ps2pack"] == "" || $modeldb[$modelID]["ps2pack"] == "no") { echo "<option value='no' SELECTED>&nbsp; PS2 kext: None selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; PS2 kext: None selected</option>\n"; }
			$id = "0";
			while ($ps2db[$id] != ""){
				$name 	= $ps2db[$id]["name"];
				$arch	= $ps2db[$id]["arch"];
				$notes	= $ps2db[$id]["notes"];
				$folder	= $ps2db[$id]["foldername"];
				$kname	= $ps2db[$id]["kextname"];
				$s=""; if ($id == $modeldb[$modelID]["ps2pack"]) { $s = "SELECTED"; }
				echo "<option value='$id' $s>&nbsp; PS2 kext: $name ($notes)</option>\n";
				$id++;
			}
			echo "</select><span class='arrow'></span> </li>";
			
			
			
		//Show dropdown for Audio kexts
			echo "<li class='select'><select name='audiopack'>\n";
			if ($modeldb[$modelID]["audiopack"] == "" || $modeldb[$modelID]["audiopack"] == "no") { echo "<option value='no' SELECTED>&nbsp; Audio kext: Not selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; Audio kext: Don't load</option>\n"; }
			$id = "0";
			while ($audiodb[$id] != ""){
				$name 	= $audiodb[$id]["name"];
				$arch	= $audiodb[$id]["arch"];
				$notes	= $audiodb[$id]["notes"];
				$s = ""; if ($id == $modeldb[$modelID]["audiopack"]) { $s = "SELECTED"; }
				echo "<option value='$id' $s>&nbsp; Audio kext: $name ($arch) - $notes</option>\n";
				$id++;
			}
			echo "</select><span class='arrow'></span> </li>\n";


		//Show dropdown for Ethernet (lan) Kexts
			echo "<li class='select'><select name='ethernet'>\n";
			if ($modeldb[$modelID]["ethernet"] == "" || $modeldb[$modelID]["ethernet"] == "no") { echo "<option value='no' SELECTED>&nbsp; Ethernet kext: Not selected</option>"; }
			else { echo "<option value='no'>&nbsp; Ethernet kext: Don't load</option>\n"; }
			$id = "0";
			while ($landb[$id] != ""){
				$name 	= $landb[$id]["name"];
				$arch	= $landb[$id]["arch"];
				$notes	= $landb[$id]["notes"];
				$s=""; if ($id == $modeldb[$modelID]["ethernet"]) { $s = "SELECTED"; }
				echo "<option value='$id' $s>&nbsp; Ethernet kext: $name ($arch) - $notes</option>\n";
				$id++;
			}
			echo "</select><span class='arrow'></span> </li>\n";
			
			
		//Show dropdown for Wifi Kexts
			echo "<li class='select'><select name='wifikext'>\n";
			if ($modeldb[$modelID]["wifikext"] == "" || $modeldb[$modelID]["wifikext"] == "no") { echo "<option value='no' SELECTED>&nbsp; Wifi kext: Not selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; Wifi kext: Don't load</option>\n"; }
			$id = "0";
			while ($wifidb[$id] != ""){
				$name 	= $wifidb[$id]["name"];
				$arch	= $wifidb[$id]["arch"];
				$notes	= $wifidb[$id]["notes"];
				$s=""; if ($id == $modeldb[$modelID]["wifikext"]) { $s = "SELECTED\n"; }
				echo "<option value='$id' $s>&nbsp; Wifi kext: $name ($arch) - $notes</option>\n";
				$id++;
			}
			echo "</select><span class='arrow'></span> </li>\n";
			
									
		//Show dropdown for Battery kexts
			echo "<li class='select'><select name='batteryKext'>\n";
			if ($modeldb[$modelID]["batteryKext"] == "" || $modeldb[$modelID]["batteryKext"] == "no") { echo "<option value='no' SELECTED>&nbsp; Battery kext: Not selected</option>\n"; }
			else { echo "<option value='no'>&nbsp; Battery kext: Don't load</option>\n"; }			
			$id = "0";
			while ($batterydb[$id] != ""){
				$name 		= $batterydb[$id]["name"];
				$kextname	= $batterydb[$id]["kextname"];
				$arch		= $batterydb[$id]["arch"];
				$s=""; if ($id == $modeldb[$modelID]["batteryKext"]) { $s = "SELECTED\n"; }				
				echo "<option value='$id' $s>&nbsp; Battery kext: $name ($arch)</option>\n";
				$id++;
			}
			echo "</select><span class='arrow'></span> </li>\n";
			
			
			
			checkbox("Use patched IOATAFamily.kext:", "loadIOATAFamily", $modeldb[$modelID]["loadIOATAFamily"]);
			checkbox("Load natit.kext:", "loadNatit", $modeldb[$modelID]["loadNatit"]);						
			
		echo "</ul><br>";
		




		echo "<span class='graytitle'>CPU & Power options</span>";
		echo "<ul class='pageitem'>";
			checkbox("Patch AppleIntelCPUPowerManagement.kext:", "patchCPU", $modeldb[$modelID]["patchCPU"]);
			checkbox("Emulated speedstep:", "emulatedST", $modeldb[$modelID]["emulatedST"]);
			checkbox("Install VoodooTSCsync:", "tscsync", $modeldb[$modelID]["tscsync"]);
			checkbox("Install NullCPUPowerManagement:", "nullcpu", $modeldb[$modelID]["nullcpu"]);	
			checkbox("Install Sleepenabler:", "sleepEnabler", $modeldb[$modelID]["sleepEnabler"]);		
		echo "</ul><br>";


		echo "<span class='graytitle'>Chameleon</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Update Chameleon to latest version:", "updateCham", "yes");	
			checkbox("Use custom chameleon:", "customCham", $modeldb[$modelID]["customCham"]);
		echo "</ul><br>";
		
		echo "<span class='graytitle'>Fixes</span>";
		echo "<ul class='pageitem'>";				
			checkbox("Use custom kernel:", "customKernel", $modeldb[$modelID]["customKernel"]);	
			checkbox("GMA950 Brightness fix:", "useGMA950brightfix", $modeldb[$modelID]["useGMA950brightfix"]);
			checkbox("Use ACPI fix (coolbook fix):", "useACPIfix", $modeldb[$modelID]["useACPIfix"]);
			
		echo "</ul><br>";
		
		
		//Send standard vars
		echo "<input type='hidden' name='action' value='dobuild'>";
		echo "<input type='hidden' name='name' value='".$modeldb[$modelID]["name"]."'>";
		echo "<input type='hidden' name='desc' value='".$modeldb[$modelID]["desc"]."'>";
		echo "<input type='hidden' name='model' value='$modelID'>";
		
		
		echo "<center><input type='submit' value='Do build!'><br><br>";
		echo "</form>";		
		

		exit;
	}



	function checkbox($title, $formname, $status) {
		if ($status == "yes") { $c = "checked"; }
		echo "<li class='checkbox'><span class='name'>$title</span><input name='$formname' type='checkbox' $c/> </li>\n";
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
		document.location.href = 'module.configuration.predefined.php?vendor='+vendor+'';
	}
</script>





</body>

</html>
