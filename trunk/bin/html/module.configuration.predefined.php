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

	if ($modelID == "") { echo "modelID is empty"; exit; }

	// Generate a multi dim. array used during the build process
	global $modeldb;

	$modeldb = array(
		//This one have to be empty, its used for when we do custom builds...
		array( 	
			'name' 			     => $_POST['name'], 
			'desc' 			     => $_POST['desc'],
			'useEDPExtensions' 	=> $_POST['useEDPExtensions'], 		
			'useEDPDSDT' 		=> $_POST['useEDPDSDT'],			
			'useEDPSSDT' 		=> $_POST['useEDPSSDT'],			
			'useEDPSMBIOS' 		=> $_POST['useEDPSMBIOS'], 			
			'useEDPCHAM' 		=> $_POST['useEDPCHAM'], 
			'useIncExtensions' 	=> $_POST['useIncExtensions'], 		
			'useIncDSDT' 		=> $_POST['useIncDSDT'],			
			'useIncSSDT' 		=> $_POST['useIncSSDT'],			
			'useIncSMBIOS' 		=> $_POST['useIncSMBIOS'], 			
			'useIncCHAM' 		=> $_POST['useIncCHAM'],            		
			'ps2pack' 		     => $_POST['ps2pack'],
			'batterypack'		 => $_POST['batterypack'],
			'ethernet'		     => $_POST['ethernet'],
			'wifipack'		     => $_POST['wifipack'],
			'audiopack'		     => $_POST['audiopack'],                    		                      		                      		
			'fakesmc'			 => $_POST['fakesmc'],
			'nullcpupwr'			 => $_POST['nullcpupwr'],
			'applecpupwr'			 => $_POST['applecpupwr'],
			'sleepenabler'			 => $_POST['sleepenabler'],
			'emupstates'			 => $_POST['emupstates'],
			'voodootsc'			 => $_POST['voodootsc'],
			'noturbo'			 => $_POST['noturbo'],
			'ACPICodec' 		 => $_POST['ChamModuleACPICodec'],
			'FileNVRAM' 		 => $_POST['ChamModuleFileNVRAM'],
			'KernelPatcher' 	 => $_POST['ChamModuleKernelPatcher'],
			'Keylayout' 		 => $_POST['ChamModulekeylayout'],
			'klibc' 			 => $_POST['ChamModuleklibc'],
			'Resolution'         => $_POST['ChamModuleResolution'],
			'Sata' 			     => $_POST['ChamModuleSata'],
			'uClibcxx' 		     => $_POST['ChamModuleuClibcxx'],
			'HDAEnabler' 		 => $_POST['ChamHDAEnabler'],
			'customCham' 		 => $_POST['customCham'],
			'updateCham' 		 => $_POST['updateCham'],
			'fixes' 		     => $_POST['fixes'],
			'optionalpacks'	     => $_POST['optionalpacks']                    		 
		),
	);

		global $workpath, $rootpath, $ee, $os; 
		global $chamModules; global $edp;
		global $modelID, $modelName;
	
		//id of modeldb array which is '0' for a model
		global $modeldbID;
		$modeldbID = "0";
		
		//
		// Start by defining our log file and cleaning it
		//
		$log = "$workpath/build.log";
		if (is_file("$log")) { 
			system_call("rm -Rf $log"); 
		}
	
		$log = "$workpath/checkout.log";
		if (is_file("$log")) { 
			system_call("rm -Rf $log"); 
		}
		
		$myFixlog = "$workpath/myFix.log";
		if (is_file("$myFixlog")) { 
			system_call("rm -Rf $myFixlog"); 
		}
		
		if(!is_dir("$workpath/kpsvn")) {
    		system_call("mkdir $workpath/kpsvn");
    	 }
	
		// Launch the script which provides the summary of the build process 
		echo "<script> document.location.href = 'workerapp.php?action=showBuildLog#myfix'; </script>";
		
   		$edp->writeToLog("$workpath/build.log", "  Cleaning up kexts in /Extra/Extensions and download status files in EDP...<br>");
    	system_call("rm -Rf /Extra/Extensions/*");
    
    	$edp->writeToLog("$workpath/build.log", "Cleaning up by System...<br>");
  		edpCleaner();
    
   	 	if(!is_dir("$workpath/kpsvn/dload/"))
    		system_call("mkdir $workpath/kpsvn/dload");
    	else
    		system_call("rm -Rf $workpath/kpsvn/dload/*");
    
		//
		// Check if myhack is up2date and ready for build
		//
		myHackCheck();
			
		//
		// Step 1 : Create the folder path and download the model data 
		//
		
		global $modelNamePath;
		$modelName = $modeldb[$modeldbID]["name"];
		$ven = builderGetVendorValuebyID($modelID);
		$gen = builderGetGenValuebyID($modelID);
		
		
		if (!is_dir("$workpath/model-data"))
			system_call("mkdir $workpath/model-data");
			
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 1) Download/update essential files for the $modelName:</b><br>");
		
		//use old method if there are is $gen type in db 
		if($gen == "") {
		
			$modelNamePath = "$modelName";

			if(!is_dir("$workpath/model-data/$modelName/"))
				system("mkdir $workpath/model-data/$modelName");
				
			system_call("svn --non-interactive --username osxlatitude-edp-read-only list http://osxlatitude-edp.googlecode.com/svn/model-data/$modelName/common >> $workpath/build.log 2>&1");
			
			svnModeldata("$modelName");
		}
		else {
		
			$modelNamePath = "$ven/$gen/$modelName";
			
			if(!is_dir("$workpath/model-data/$ven"))
				system("mkdir $workpath/model-data/$ven");
			
			if(!is_dir("$workpath/model-data/$ven/$gen"))
				system("mkdir $workpath/model-data/$ven/$gen");
			
			if(!is_dir("$workpath/model-data/$modelNamePath/"))
				system("mkdir $workpath/model-data/$modelNamePath");
			
			system_call("svn --non-interactive --username osxlatitude-edp-read-only list http://osxlatitude-edp.googlecode.com/svn/model-data/$modelNamePath/common >> $workpath/build.log 2>&1");
			
			//
			// We use the new method "loadModelEssentialFiles" for the models and 
			// old method "svnModeldata" for the old models which is not updated for the new DB to fetch files
			//
		
			loadModelEssentialFiles();
		}
			
		//
		// Step 2 : Copy essentials like dsdt, ssdt and plists 
		//
		$edp->writeToLog("$workpath/build.log", "<br><br><b>Step 2) Copying Essential files downloaded and from /Extra/include:</b><br>");
		copyEssentials();
			
		//
		// Step 3 : Applying Fixes and bootloader config
		//	
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 3) Applying fixes and Chameleon settings:</b><br>");
		applyFixes();
		
		if($modeldb[$modeldbID]["updateCham"] == "on") {
			$edp->writeToLog("$workpath/build.log", "Updating bootloader...<br>");
			system_call("cp -f $workpath/boot /");
		}
			
		$edp->writeToLog("$workpath/build.log", "  Copying selected modules...</b><br>");
		$chamModules->copyChamModules($modeldb[$modeldbID]);
		
		//
		// Step 4 : Copying kexts
		//
		$edp->writeToLog("$workpath/build.log", "<br><b>Step 4) Downlading and preparing kexts:</b><br>");
		copyEDPKexts();		
}

//-------------------------> Here starts the Vendor and model selector - but only if $action is empty

if ($action == "") {

		//
		// Clear build Status files
		//
		$myFixlog = "$workpath/myFix2.log";
		if (is_file("$myFixlog")) { 
			system_call("rm -Rf $myFixlog"); 
		}
		$statFiles = "$workpath/kpsvn/dload";
		if(is_dir("$statFiles"))
			system_call("rm -rf $workpath/kpsvn/dload/*");
				
		// Fetch standard model info needed for the configuration of the choosen model to build
		$stmt = $edp_db->query("SELECT * FROM modelsdata where id = '$modelID'");
		$stmt->execute();
		$result = $stmt->fetchAll(); $mdrow = $result[0];
		
	// Write out the top menu
	echoPageItemTOP("icons/big/config.png", "Select a model your wish to configure for:");

	echo "<div class='pageitem_bottom'>\n";
	echo "EDP's internal database contains 'best practice' schematics for 80+ systems - this makes it easy for to to choose the right configuration - however - you allways have the option to ajust the schematics before doing a build. <br><br>Doing a build means that EDP will copy a combination of kexts, dsdt, plists needed to boot your system.";

	include "header.inc.php";
	echo "<p><span class='graytitle'></span><ul class='pageitem'><li class='select'>";

	echo "<select name='vendor' id='vendor'>";
	
	if ($vendor == "") { echo "<option value='' selected>&nbsp;&nbsp;Select vendor...</option>\n"; } else { echo "<option value='' selected>&nbsp;&nbsp;Select vendor and type...</option>\n"; }

	echo builderGetVendorValues(); // For series and model we are using jquery

	echo "</select><span class='arrow'></span> </li>";

	echo "<li id='serie-container' class='select hidden'><td><select id='serie' name='serie'>";

	echo "</select><span class='arrow'></span> </li>";					

	echo "<li id='model-container' class='select hidden'><td><select id='model' name='model'>";

	echo "</select><span class='arrow'></span> </li></ul>";

	echo '<div id="continue-container" class="hidden">';
	echo "<p><B><center>After clicking 'Continue' EDP will let you to config your machine.<br></p><br>";
	echo "<ul class='pageitem'><li class='button'><input name='OK' type='button' value='Continue...' onclick='doConfirm();' /></li></ul></p>";
	echo '</div>';

	?>

	<style>
		.hidden {
			display: none;
		}
	</style>
	<script>
		jQuery('#vendor').change(function() {
			var vendor = jQuery('#vendor option:selected').val();

			console.log('Selected vendor: ' + vendor);

			if (vendor == '') {
				jQuery('#serie-container, #model-container, #continue-container').addClass('hidden');
				return;
			}

			jQuery.get('workerapp.php', { action: 'builderSerieValues', vendor: vendor }, function(data) {
				jQuery('#serie').empty().append(data).val('');
				jQuery('#serie-container').removeClass('hidden');
				jQuery('#model-container, #continue-container').addClass('hidden');
			});
		});

		jQuery('#serie').change(function() {
			var vendor = jQuery('#vendor option:selected').val();
			var serie  = jQuery('#serie option:selected').val();

			console.log('Selected serie: ' + serie);

			if (vendor == '' || serie == '') {
				jQuery('#model-container, #continue-container').addClass('hidden');
				return;
			}

			jQuery.get('workerapp.php', { action: 'builderModelValues', vendor: vendor, serie: serie }, function(data) {
				jQuery('#model').empty().append(data);
				jQuery('#model-container').removeClass('hidden');
			});
		});

		jQuery('#model').change(function() {
			var vendor = jQuery('#vendor option:selected').val();
			var serie  = jQuery('#serie option:selected').val();
			var model  = jQuery('#model option:selected').val();

			console.log('Selected model: ' + model);

			if (vendor == '' || serie == '' || model == '') {
				jQuery('#continue-container').addClass('hidden');
				return;
			}

			jQuery('#continue-container').removeClass('hidden');
		});
	</script>				

	<?php }

//<-------------------- Here stops the vendor and model selector		

//--------------------> Here starts the build confirmation page 
//Check if $action was set via GET or POST - if it is set, we asume that we are going to confirm the build
	if ($action == "confirm") {
		
		//Fetch standard model info needed for the configuration of the choosen model to build
		$stmt = $edp_db->query("SELECT * FROM modelsdata where id = '$modelID'");
		$stmt->execute();
		$bigrow = $stmt->fetchAll(); $mdrow = $bigrow[0];

		//Load the tabs
		echo "<script> $(function() { $( \"#tabs\" ).tabs(); }); </script>\n";
		
		echo "<form action='module.configuration.predefined.php' method='post'>";
		echoPageItemTOP("http://www.osxlatitude.com/wp-content/themes/osxlatitude/img/edp/modelpics/$mdrow[name].png", "$mdrow[desc]");
		
		//Show the tabs bar ?>
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
			echo "<ul class='pageitem'><li class='button'><input name='Submit input' type='submit' value='Do build!' onclick='clearLoadingScreen();' /></li></ul><br><br>\n";
			echo "</form>";		

		}
	//<------------------------------ Here ends the model confirmation page
		?>

		<script>
		function clearLoadingScreen() {
			// top.document.getElementById('edpmenu').src ='menu.inc.php?i=Configuration';
			// top.document.getElementById('edpmenu').src ='workerapp.php?action=showLoadingLog#myfix';
		}
		function doConfirm() {
			var vendor = '<?php echo "$vendor";?>';
			var a = document.getElementById("model");
			var model = a.options[a.selectedIndex].value;
			if (model == "") { alert('Please select a model before continuing..'); return; }
			document.location.href = 'module.configuration.predefined.php?vendor='+vendor+'&model='+model+'&action=confirm';		
			top.document.getElementById('edpmenu').src ='workerapp.php?action=showLoadingLog#myfix';
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
