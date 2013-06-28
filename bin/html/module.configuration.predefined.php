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
                      		customKernel 	=> $customKernel,
                      		fakesmc			=> $_POST['fakesmc'],
                      		ACPICodec 		=> $_POST['ChamModuleACPICodec'],
                      		FileNVRAM 		=> $_POST['ChamModuleFileNVRAM'],
                      		KernelPatcher 	=> $_POST['ChamModuleKernelPatcher'],
                      		Keylayout 		=> $_POST['ChamModulekeylayout'],
                      		klibc 			=> $_POST['ChamModuleklibc'],
                      		Resolution 		=> $_POST['ChamModuleResolution'],
                      		Sata 			=> $_POST['ChamModuleSata'],
                      		uClibcxx 		=> $_POST['ChamModuleuClibcxx'],
                      		optionalpacks	=> $_POST['optionalpacks']                    		 
                    ),
             );

		     
		//($ChamModuleACPICodec == "on" ? "yes" : "no")
		   
        global $modelID; $modelID = "0";
        global $modelName;
        $modelName = $modeldb[$modelID]["name"];
 
        $builder->EDPdoBuild();	
	}










		    

			
//-------------------------> Here starts the Vendor and model selector - but only if $action is empty
				
if ($action == "") {
	
	//Write out the top menu
	echoPageItemTOP("icons/big/config.png", "Select a model your wish to configure for:");

	echo "<div class='pageitem_bottom'>\n";
	echo "EDP's internal database contains 'best practice' schematics for 80+ systems - this makes it easy for to to choose the right configuration - however - you allways have the option to ajust the schematics before doing a build. <br><br>Doing a build means that EDP will copy a combination of kexts, dsdt, plists needed to boot your system.";
	
    		$result = $edp_db->query("SELECT * FROM models order by vendor");
			include "header.inc.php";
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
							echo "</select><span class='arrow'></span> </li></ul>";
							echo "<p><B><center>After clicking 'Continue' EDP will download the latest model data for your machine.<br>- This may take a few minuts -	</center></p><br>";
							echo "<ul class='pageitem'><li class='button'><input name='OK' type='button' value='Continue...' onclick='doConfirm();' /></li></ul></p>";
						}

				}				
				
}

//<-------------------- Here stops the vendor and model selector		









//--------------------> Here starts the build confirmation page 
//Check if $action was set via GET or POST - if it is set, we asume that we are going to confirm the build
if ($action == "confirm") {
		
		//Fetch standard model info
		$stmt = $edp_db->query("SELECT * FROM models where id = '$modelID'");
		$stmt->execute();
		$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];

		//Download model data
		$modelName = $mdrow[name];
		echo "<div id='model_download' style='display: none'>";
		svnModeldata("$modelName");
		echo "</div>";
		
		//Load the tabs
		echo "<script> $(function() { $( \"#tabs\" ).tabs(); }); </script>\n";
        

		
		echo "<form action='module.configuration.predefined.php' method='post'>";
		echoPageItemTOP("http://www.osxlatitude.com/wp-content/themes/osxlatitude/img/edp/modelpics/$mdrow[name].png", "$mdrow[desc]");
	
		
    ?>

<? //Show the tabs bar ?>
	<div id="tabs">
		<div id="menutabs">
        	<ul>
            	<li><a href="#tabs-0">Overview</a></li>
                <li><a href="#tabs-1">Kext / Drivers</a></li>
                <li><a href="#tabs-2">CPU & Power</a></li>
                <li><a href="#tabs-3">Chameleon</a></li>
                <li><a href="#tabs-4">Fixes</a></li>
                <li><a href="#tabs-5">Optional</a></li>
            </ul>
        </div>
<?php
		
echo "<div class='pageitem_bottom'><br>\n";

//Include tabs
include "include/module.configuration.overview.inc.php";
include "include/module.configuration.kexts.inc.php";
include "include/module.configuration.cpu.inc.php";
include "include/module.configuration.chameleon.inc.php";		
include "include/module.configuration.fixes.inc.php";
include "include/module.configuration.optional.inc.php";		

	
	
//Send standard vars
echo "<input type='hidden' name='action' value='dobuild'>";
echo "<input type='hidden' name='name' value='$mdrow[name]'>";
echo "<input type='hidden' name='desc' value='$mdrow[desc]'>";
echo "<input type='hidden' name='model' value='$modelID'>";
		
echo "</div><br>";
echo "<ul class='pageitem'><li class='button'><input name='Submit input' type='submit' value='Do build!' /></li></ul><br><br>\n";
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
		if (model == "") { alert('Please select a model before continuing..'); return; }
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
