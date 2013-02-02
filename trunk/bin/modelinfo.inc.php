<?


//Start by building our array
$mainmodeldb = array(
	//This one have to be empty, its used for when we do custom builds...
	array( 	name 			=> "", 
    		desc 			=> "",
            nullcpu 		=> "",
            sleepEnabler 	=> "",                      		
            ps2pack 		=> "",
            emulatedST 		=> "",                      		
            tscsync 		=> "",                      		
            batteryKext		=> "1",
            loadIOATAFamily	=> "",
            loadNatit		=> "yes",
            useACPIfix		=> "yes",
            patchCPU		=> "",
            ethernet		=> "",
            audiopack		=> "1",
            supports_sl		=> "",
            supports_lion	=> "",
            supports_ml		=> "",                      		                      		                      		
            customCham 		=> "",                      		
            customKernel 	=> ""                      		 
         ), 	                                                                                                                                                 
);
     

//Parse the vendordb
	include "vendordb.inc.php";
	$id = 0;
	while ($vendordb[$id] != ""){
		$name = $vendordb[$id]["name"];
		$dbfile = $vendordb[$id]["dbfile"];		
		AddToArray("$dbfile");
		$id++;
	}
		

		
function AddToArray($dbfile) {
	include "$dbfile";

	global $mainmodeldb;
	$mainid = 0;
	while ($mainmodeldb[$mainid] != ""){
			$mainid++;
	}
		
	
	$id = 1;
	while ($modeldb[$id] != ""){
			$mainmodeldb[$mainid]["name"] 			= $modeldb[$id]["name"];
			$mainmodeldb[$mainid]["desc"] 			= $modeldb[$id]["desc"];
			$mainmodeldb[$mainid]["nullcpu"] 		= $modeldb[$id]["nullcpu"];
			$mainmodeldb[$mainid]["sleepEnabler"] 	= $modeldb[$id]["sleepEnabler"];
			$mainmodeldb[$mainid]["ps2pack"] 		= $modeldb[$id]["ps2pack"];
			$mainmodeldb[$mainid]["emulatedST"] 	= $modeldb[$id]["emulatedST"];
			$mainmodeldb[$mainid]["tscsync"] 		= $modeldb[$id]["tscsync"];
			$mainmodeldb[$mainid]["batteryKext"] 	= $modeldb[$id]["batteryKext"];
			$mainmodeldb[$mainid]["loadIOATAFamily"] = $modeldb[$id]["loadIOATAFamily"];
			$mainmodeldb[$mainid]["loadNatit"] 		= $modeldb[$id]["loadNatit"];
			$mainmodeldb[$mainid]["useACPIfix"] 	= $modeldb[$id]["useACPIfix"];
			$mainmodeldb[$mainid]["patchCPU"] 		= $modeldb[$id]["patchCPU"];
			$mainmodeldb[$mainid]["ethernet"] 		= $modeldb[$id]["ethernet"];
			$mainmodeldb[$mainid]["audiopack"] 		= $modeldb[$id]["audiopack"];
			$mainmodeldb[$mainid]["supports_sl"] 	= $modeldb[$id]["supports_sl"];
			$mainmodeldb[$mainid]["supports_lion"] 	= $modeldb[$id]["supports_lion"];
			$mainmodeldb[$mainid]["supports_ml"] 	= $modeldb[$id]["supports_ml"];
			$mainmodeldb[$mainid]["customCham"] 	= $modeldb[$id]["customCham"];
			$mainmodeldb[$mainid]["customKernel"] 	= $modeldb[$id]["customKernel"];								
			
			$id++;
			$mainid++;
			
	}
	return "$mainmodeldb";
}


$modeldb = $mainmodeldb;

	
?>
